<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WeekplanProgress extends Component
{
    public $profit;
    public $milestones;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($profit, $milestones)
    {
        $this->profit = $profit;
        $this->milestones = $milestones;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $limit = ($this->milestones->last()['profit']) + (0.1 * ($this->milestones->last()['profit']));
        $progress = $limit != 0 ? round($this->profit / $limit * 100) : 0;

        return view('components.weekplan-progress', [
            'profit' => $this->profit,
            'milestones' => $this->milestones,
            'limit' => $limit,
            'progress' => $progress
        ]);
    }
}
