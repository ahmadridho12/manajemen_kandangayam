<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DashboardCardMaster extends Component
{
    public $label, $value, $color, $icon;

    public function __construct($label, $value, $color, $icon)
    {
        $this->label = $label;
        $this->value = $value;
        $this->color = $color;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.dashboard-card-master');
    }
}
