<?php

namespace Spatie\MediaLibrary\Image;

class ImageManipulator
{
    protected $filePath;

    /** @var Manipulations */
    protected $manipulations;

    /** @var \Imagick */
    protected $imagick;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public static function load($filePath)
    {
        return new static($filePath);
    }

    public function manipulations($manipulations)
    {
        $this->manipulations = $manipulations;
        return $this;
    }

    /**
     * @throws \ImagickException
     */
    public function executeManipulations()
    {
        $this->imagick = new \Imagick($this->filePath);

        // gif needs special treatment
        // execute manipulations on every frames.
        if ($this->imagick->getImageFormat() === 'GIF') {
            $this->imagick = $this->imagick->coalesceImages();

            do {
                $this->executeManipulationsInternal();
            } while ($this->imagick->nextImage());

            $this->imagick = $this->imagick->deconstructImages();
        } else {
            $this->executeManipulationsInternal();
        }
    }

    public function executeManipulationsInternal()
    {
        foreach ($this->manipulations->getCollection() as $manipulation) {
            $this->executeManipulation($manipulation);
        }
    }

    public function executeManipulation(Manipulation $manipulation)
    {
        switch ($manipulation->getType()) {
            case 'optimize':
                $this->optimize();
                break;

            case 'maxWidth':
                $this->maxWidth($manipulation->getOptions());
                break;

            case 'maxHeight':
                $this->maxHeight($manipulation->getOptions());
                break;

            case 'width':
                $this->width($manipulation->getOptions());
                break;

            case 'height':
                $this->height($manipulation->getOptions());
                break;

            case 'format':
                $this->format($manipulation->getOptions());
                break;
        }
    }

    public function optimize()
    {
        $this->imagick->stripImage();
    }

    public function maxWidth($width)
    {
        if ($this->imagick->getImageWidth() > $width) {
            $this->width($width);
        }
    }

    public function width($width)
    {
        // according to http://php.net/manual/en/imagick.examples-1.php,
        // providing rows 0 keep aspect ratio maintained
        $this->imagick->scaleImage($width, 0);
    }

    public function maxHeight($height)
    {
        if ($this->imagick->getImageHeight() > $height) {
            $this->height($height);
        }
    }

    public function height($height)
    {
        $this->imagick->scaleImage(0, $height);
    }

    public function format(string $format)
    {
        if (strtolower($this->imagick->getImageFormat()) !== strtolower($format)) {
            $this->imagick->setImageFormat($format);
        }
    }

    public function save()
    {
        $this->executeManipulations();

        if ($this->imagick->getImageFormat() === 'GIF') {
            $this->imagick->writeImages($this->filePath, true);
        } else {
            $this->imagick->writeImage();
        }

        $this->imagick->clear();
        $this->imagick->destroy();
    }
}