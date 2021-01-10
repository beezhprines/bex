<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PeriodControl extends Component
{
    protected $route;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $route)
    {
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.period-control', [
            "route" => $this->route
        ]);
    }
}
