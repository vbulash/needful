<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tile extends Component
{
	public string $title;
	public string $subtitle;
	public bool $active;
	public string $icon;
	public string $link;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
		string $title, string $subtitle, bool $active, string $icon, string $link = '')
    {
        $this->title = $title;
		$this->subtitle = $subtitle;
		$this->active = $active;
		$this->icon = $icon;
		$this->link = $link ?? 'javascript:void(0)';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.tile');
    }
}
