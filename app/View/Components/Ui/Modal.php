<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public bool $showCloseButton;
    public bool $isFullscreen;
    public string $modalId;

    public function __construct(
        bool $showCloseButton = true,
        bool $isFullscreen = false,
        ?string $modalId = null
    ) {
        $this->showCloseButton = $showCloseButton;
        $this->isFullscreen = $isFullscreen;
        $this->modalId = $modalId ?? 'modal-' . uniqid();
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.modal');
    }
}
