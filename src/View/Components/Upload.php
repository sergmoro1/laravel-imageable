<?php

namespace Sergmoro1\Imageable\View\Components;

use Illuminate\View\Component;

class Upload extends Component
{
    /**
     * The model for which the images are being uploaded.
     * 
     * @var object $model
     */
    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function render()
    {
        return view('vendor.imageable.upload');
    }
}