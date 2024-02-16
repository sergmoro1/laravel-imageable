<?php

namespace Sergmoro1\Imageable\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Button color.
     * 
     * @var string $color
     */
    public $color;
    public $action;

    public function __construct(string $color, string $action)
    {
        $this->color = $color;
        $this->action = $action;
    }

    public function render()
    {
        return view('vendor.imageable.components.button');
    }
}