<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $class,
        public string $title,
        public string $subtitle,
        public string $wireTarget,
        public string $createFeedback = '',
        public string $closeAction = 'closeCreateModal',
        public ?string $loadingMessage = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
