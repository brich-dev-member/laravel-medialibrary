<?php

namespace Spatie\MediaLibrary\Image;


class Manipulation
{
    protected $type;

    protected $options;

    public function __construct(string $type, $options)
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

}
