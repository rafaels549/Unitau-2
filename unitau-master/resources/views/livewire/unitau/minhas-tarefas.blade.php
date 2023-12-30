<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Computed, Url};

use Carbon\Carbon;

new 
#[Layout('components.layouts.teams')] 
class extends Component {
    #[Url(keep: true)]
    public $option = 'Em breve';

    #[Computed]
    public function tarefas()
    {
        $equipes = auth()->user()->teams;

     return  \App\Models\Tarefa::query()
            ->whereIn('equipe_id', $equipes->pluck('id')) 
            ->when($this->option === 'Em breve', function ($query) {
                return $query
                    ->whereDoesntHave('envios', function ($subQuery) {
                        $subQuery->where('status', 0);
                    })
                    ->where('EndDate', '>', Carbon::now());
            })
            ->when($this->option === 'Em atraso', function ($query) {
                return $query
                    ->whereDoesntHave('envios', function ($subQuery) {
                        $subQuery->where('status', 0);
                    })
                    ->where('EndDate', '<', Carbon::now());
            })
            ->when($this->option === 'Concluída', function ($query) {
                return $query
                    ->whereHas('envios', function ($subQuery) {
                        $subQuery->where('status', 0);
                    });
            })
            ->get();

    
    }
  

  
}; ?>

<div class="flex flex-col">
    <div class="absolute top-0 left-0 ml-24 flex gap-4 ">
        <div class="grid w-[40rem] grid-cols-4 gap-2 rounded-xl bg-gray-200 p-2">
            @foreach(['Em breve', 'Em atraso', 'Concluída'] as $filterOption)
                <div>
                    <input type="radio"  wire:model.change="option" id="{{ $filterOption }}" value="{{ $filterOption }}" class="peer hidden" />
                    <label for="{{ $filterOption }}" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-blue-500 peer-checked:font-bold peer-checked:text-white">
                        {{ ucfirst($filterOption) }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
<div class="flex flex-col items-center mt-[100px]">
    
  
       
        
      <span wire:loading wire:target="option" class="loader"></span>
    
    
            @forelse($this->tarefas as $tarefa)
       
                <x-tarefa-loop :tarefa="$tarefa"  />
      
              
                

              

            @empty 
                
            <h1 class="text-2xl">Nenhuma tarefa encontrada.</h1>

            @endforelse

            </div>
</div>