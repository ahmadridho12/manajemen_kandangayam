<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReadonlyInput extends Component
{
    public $name;
    public $label;
    public $value;
    public $type;

    public function __construct($name, $label, $value = null, $type = 'text')
    {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.readonly-input');
    }
}
