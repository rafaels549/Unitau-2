<?php

use Livewire\Volt\Component;
use App\Models\Equipe;
use App\Models\Tarefa;
use Livewire\Attributes\On;

new class extends Component {
    public Equipe $equipe;
    public Tarefa $tarefa;
    public bool $editing = false;


  public $arquivos = [];

    public function mount($id,$id1){
          $this->equipe = Equipe::findOrFail($id);
           $this->tarefa = Tarefa::findOrFail($id1);


          
    }

    #[On('tarefa-updated')]
    public function disableEditing() {
    
      $this->editing = false;
    }

    public function edit() {
      $this->editing = true;
    }

 

    public function download($fullFilePath)
{
    $path = storage_path('app/public/' . $fullFilePath);

    return response()->download($path);
}
}; ?>


   
  <div class=" p-20" >
  @if($editing)
 
  @livewire('unitau.edit-tarefa', ['tarefa' => $tarefa], key($tarefa->id))

  @else
<div>
    @php
    $dataInicio = is_string($tarefa->StartDate) ? new \DateTime($tarefa->StartDate) : $tarefa->StartDate;
    $dataFinal = is_string($tarefa->EndDate) ? new \DateTime($tarefa->EndDate) : $tarefa->EndDate;
   @endphp

    <h1 class="text-4xl mb-3">{{ $tarefa->texto }}</h1>
  <h2  class="text-lg mb-2">Data de Início: {{ $dataInicio->format('d/m/Y H:i') }}</h2>
  <h2  class="text-lg mb-3">Data de Entrega: {{ $dataFinal->format('d/m/Y H:i') }}</h2>
  <div class="bg-blue-600 rounded-md text-center flex justify-center text-white p-2 mb-3"><svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 256 256"><path fill="#FFFFFF" d="M209.66 122.34a8 8 0 0 1 0 11.32l-82.05 82a56 56 0 0 1-79.2-79.21l99.26-100.72a40 40 0 1 1 56.61 56.55L105 193a24 24 0 1 1-34-34l83.3-84.62a8 8 0 1 1 11.4 11.22l-83.31 84.71a8 8 0 1 0 11.27 11.36L192.93 81A24 24 0 1 0 159 47L59.76 147.68a40 40 0 1 0 56.53 56.62l82.06-82a8 8 0 0 1 11.31.04"/></svg>Anexos</div>
  <div class="flex flex-col gap-4">
  
 @foreach($tarefa->files as $file)   
<x-file-box :file="$file"/>
@endforeach
@if($tarefa->owner->is(auth()->user()))
  <button wire:click="edit" type="button" class="absolute top-0 right-0 mt-4 mr-4 px-4 py-2 hover:bg-gray-100 rounded-md">
  <svg  xmlns="http://www.w3.org/2000/svg"  width="24" height="24" viewBox="0 0 24 24" class="cursor-pointer"><path fill="#00688B" d="m19.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L19.3 8.925ZM17.85 10.4L7.25 21H3v-4.25l10.6-10.6l4.25 4.25Z"/></svg>
</button>

@endif

@endif
</div>
  </div>
  @cannot('update', $tarefa)
<livewire:unitau.create-tarefa-envio  :tarefa="$tarefa"/>
@endcan










     
    </div>    
