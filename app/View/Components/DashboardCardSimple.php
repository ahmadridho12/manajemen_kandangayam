<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCardSimple extends Component
{
    public string $label;
    public string $color;
    public string $icon;
    public mixed $value; // Diubah dari int ke mixed
    public float $percentage;
    public bool $daily;
    public ?string $route; // Menambahkan properti route

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $value, $daily, $color, $icon, $percentage, $route = null)
    {
        $this->label = $label;
        $this->value = $value;
        $this->daily = $daily;
        $this->color = $color;
        $this->icon = $icon;
        $this->percentage = $percentage ?? 0.00;
        $this->route = $route; // Menambahkan properti route
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard-card-simple');
    }

}
