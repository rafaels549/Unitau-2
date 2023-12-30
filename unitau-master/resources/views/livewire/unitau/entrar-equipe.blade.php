<?php

use Livewire\Volt\Component;
use App\Models\Equipe;
use Livewire\Attributes\{ Layout};

new 
#[Layout('components.layouts.teams')] 
class extends Component {
    public function mount($code) {
        $equipe = Equipe::where("code", $code)->first();

        if (!$equipe) {
        abort(404);
        }
    
    
        if ($equipe->users()->where('user_id', auth()->user()->id)->exists()) {
            return session()->flash('erro', 'Você já está na equipe.');
        }
        
    
        $equipe->users()->attach(auth()->user()->id);
    
     
    
        $this->redirect("equipe/{$equipe->id}", navigate: true); 

    }
}; ?>

<div>
    
</div>
