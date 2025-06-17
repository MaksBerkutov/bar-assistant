<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PhoneInput extends Component
{
    public string $name;
    public string $placeholder;
    public string $value;
    public bool $showDivPlaceholder;

    public function __construct($name, $placeholder = null, $value = '', bool $showDivPlaceholder = true)
    {
        $this->name = $name;
        $this->placeholder = $placeholder ?? "Введите {$name}";
        $this->value = $value;
        $this->showDivPlaceholder = $showDivPlaceholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.phone-input');
    }
}
