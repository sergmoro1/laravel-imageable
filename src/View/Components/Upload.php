<?php

namespace Sergmoro1\Imageable\View\Components;

use Illuminate\View\Component;
use Illuminate\Database\Eloquent\Model;

class Upload extends Component
{
    /**
     * The model for which the images are being uploaded.
     * 
     * @var object
     */
    public $model;

    /**
     * Limit on the number of images uploaded.
     * 
     * @var int
     */
    public $limit;

    public function __construct(Model $model, int $limit = 0)
    {
        $this->model = $model;
        $this->limit = $limit;
    }

    public function render()
    {
        return view('vendor.imageable.upload');
    }
}