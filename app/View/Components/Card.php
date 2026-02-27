<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class Card extends Component
{
    public string $titulo;

    public function __construct(string $titulo)
    {
        $this->titulo = $titulo;
    }

    public function render()
    {
        return view('components.card');
    }
}
