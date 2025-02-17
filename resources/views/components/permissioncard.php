<?php
namespace App\View\Components;

use App\Models\Letter;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LetterCard extends Component
{
    public Letter $letter;

    public function __construct(Letter $letter = null)
    {
        $this->letter = $letter;
    }

    public function render(): View|string|Closure
    {
        return view('components.permision-card');
    }
}
