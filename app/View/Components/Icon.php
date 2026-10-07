<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public string $name;
    public string $size;
    public string $class;

    public function __construct(
        string $name,
        string $size = 'w-5 h-5',
        string $class = ''
    ) {
        $this->name = $name;
        $this->size = $size;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.icon');
    }
}
