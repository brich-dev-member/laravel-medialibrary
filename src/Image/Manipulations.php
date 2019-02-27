<?php

namespace Spatie\MediaLibrary\Image;

use Illuminate\Support\Collection;

class Manipulations
{
    protected $manipulationCollection;

    public function __construct()
    {
        $this->manipulationCollection = new Collection();
    }

    public function optimize($optimizeOptions = [])
    {
        $this->manipulationCollection->push(new Manipulation('optimize', $optimizeOptions));

        return $this;
    }

    public function nonOptimized()
    {
        $index = $this->manipulationCollection->search(function (Manipulation $manipulation) {
            return $manipulation->getType() === 'optimize';
        });

        if ($index) {
            $this->manipulationCollection->forget($index);
        }

        return $this;
    }

    public function removeManipulation(string $name)
    {
        $this->manipulationCollection->forget($name);

        return $this;
    }

    public function format(string $format)
    {
        $this->manipulationCollection->push(new Manipulation('format', $format));

        return $this;
    }

    public function maxWidth($width)
    {
        $this->manipulationCollection->push(new Manipulation('maxWidth', $width));

        return $this;
    }

    public function maxHeight($height)
    {
        $this->manipulationCollection->push(new Manipulation('maxHeight', $height));

        return $this;
    }

    public function fitCrop($width, $height)
    {
        $this->manipulationCollection->push(new Manipulation('fitCrop', [
            'width' => $width,
            'height' => $height
        ]));

        return $this;
    }

    /**
     * @return Collection|Manipulation[]
     */
    public function getCollection()
    {
        return $this->manipulationCollection;
    }

    public function getManipulationOptions($type)
    {
        /** @var Manipulation $manipulation */
        $manipulation = $this->manipulationCollection->first(function (Manipulation $manipulation) use ($type) {
            return $manipulation->getType() === $type;
        });

        if ($manipulation) {
            return $manipulation->getOptions();
        }

        return null;
    }
}