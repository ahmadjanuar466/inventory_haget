<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class tableBody extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $rowView,
        public iterable $items = [],
        public int $columns = 1,
        public ?string $emptyMessage = null,
        public array $rowData = [],
        public string $itemKey = 'item',
    ) {
        $this->emptyMessage ??= __('No records found.');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table-body');
    }
}
