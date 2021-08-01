<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PeriodControl extends Component
{
    protected $route;
    protected $button;
    protected $method;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $route, ?string $button = 'Обновить', ?string $method = 'GET')
    {
        $this->route = $route;
        $this->button = $button;
        $this->method = $method;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.period-control', [
            "route" => $this->route,
            "button" => $this->button,
            "method" => $this->method,
        ]);
    }
}
