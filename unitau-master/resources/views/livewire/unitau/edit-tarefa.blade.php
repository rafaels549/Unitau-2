<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\TarefaForm;
use App\Models\TarefaFiles;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

new class extends Component {
    use WithFileUploads;
    public $tarefa;
    public TarefaForm $form;


    public function mount() {
  
 
    
      
        $this->form->titulo = $this->tarefa->texto;
        $this->form->dateinicial = Carbon::createFromFormat('Y-m-d H:i:s', $this->tarefa->StartDate)
        ->format('d/m/Y H:i');


$this->form->datefinal = Carbon::createFromFormat('Y-m-d H:i:s', $this->tarefa->EndDate)->format('d/m/Y H:i');;

    }

  
public function delete($id) {
   
    $arquivo = TarefaFiles::findOrFail($id);

    // Obter o caminho completo do arquivo no servidor
    $caminhoArquivo = storage_path('app/public/' . $arquivo->file);


    if (File::exists($caminhoArquivo)) {
        
        File::delete($caminhoArquivo);
    }


    $arquivo->delete();
}

    public function update() {
      $this->validate();
        $this->authorize('update', $this->tarefa);

        $carbonDateInicial = Carbon::createFromFormat('d/m/Y H:i', $this->form->dateinicial);
    
    if ($carbonDateInicial->isPast()) {
        session()->flash('errorInicial', 'Não é possível agendar eventos no passado.');
        return;
    }

 $carbonDateFinal =    Carbon::createFromFormat('d/m/Y H:i', $this->form->datefinal);

 if ($carbonDateFinal <= $carbonDateInicial) {
        session()->flash('errorFinal', 'Não é possível agendar.');
        return;
    }
       
      $this->tarefa->update([
            'texto' => $this->form->titulo,
             'StartDate' =>    $carbonDateInicial,
             'EndDate' =>  $carbonDateFinal,

        ]);

      
     
        foreach($this->form->arquivos as $arquivo) {
            $nomeRealDoArquivo = $arquivo->getClientOriginalName();
            $this->tarefa->files()->create([
              'file'  => $arquivo->storeAs('uploads', $nomeRealDoArquivo, 'public'),
            ]);
        }
$this->form->reset('arquivos');
$this->dispatch('tarefa-updated');
    
    }

    public function limpar($arquivo) {
      
        $indice = array_search($arquivo, $this->form->arquivos);
    
        unset($this->form->arquivos[$indice]);
    
      
        $this->form->arquivos = array_values($this->form->arquivos);
    }


}; ?>

<div class="flex flex-col">

<form wire:submit="update">
    <input type="text" class="text-4xl  rounded-lg  mb-3" wire:model="form.titulo" >
  <div x-data="{ date: '' }" x-init="() => {
                flatpickr($refs.datepicker, {
                    enableTime: true,
                    dateFormat: 'd/m/Y H:i',
                    locale: 'pt',
                    time_24hr: true,
                    minDate: 'today', 
                   
                   
                });
            }">
                <div class="mb-4">
                    <label  class="block  text-lg mb-1">Data de Ínicio</label>
                    <input x-ref="datepicker" wire:model="form.dateinicial"  type="text"  class="w-full border text-lg px-2 py-2 rounded focus:outline-none focus:border-blue-400">
                    @error('form.dateinicial') <span class="text-red-500">{{ $message }}</span> @enderror
                    @session('errorInicial')
                    <span class="text-red-500">{{ $value }}</span>
                    @endsession
                </div>
            </div>
            <div x-data="{ date: '' }" x-init="() => {
                  flatpickr($refs.datepicker2, {
                      enableTime: true,
                      dateFormat: 'd/m/Y H:i',
                      locale: 'pt',
                      time_24hr: true,
                      minDate: 'today', 
                     
                  });
              }">
                  <div class="mb-4">
                      
                    <label  class="block  text-lg mb-1">Data de Entrega</label>
                      <input x-ref="datepicker2"  wire:model="form.datefinal"  id="data2" type="text"  class="w-full border text-lg px-2 py-2 rounded focus:outline-none focus:border-blue-400">
                      @error('form.datefinal') <span class="text-red-500">{{ $message }}</span> @enderror
                    @session('errorFinal')
                      <span class="text-red-500">{{ $value }}</span>
                      @endsession
                  </div>
              </div>        

  <div class="bg-blue-600 rounded-md text-center flex justify-center text-white p-2 mb-3"><svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 256 256"><path fill="#FFFFFF" d="M209.66 122.34a8 8 0 0 1 0 11.32l-82.05 82a56 56 0 0 1-79.2-79.21l99.26-100.72a40 40 0 1 1 56.61 56.55L105 193a24 24 0 1 1-34-34l83.3-84.62a8 8 0 1 1 11.4 11.22l-83.31 84.71a8 8 0 1 0 11.27 11.36L192.93 81A24 24 0 1 0 159 47L59.76 147.68a40 40 0 1 0 56.53 56.62l82.06-82a8 8 0 0 1 11.31.04"/></svg>Anexos</div>
  <div class="flex flex-col gap-4">

 @foreach($tarefa->files as $file)   

<x-file-box :file="$file" wire:key="{{ $file->id }}">
<button wire:click.prevent="delete({{ $file->id }})" class="bg-red-500 text-white p-2 rounded-lg" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="delete({{ $file->id }})">
    <span wire:loading wire:target="delete({{ $file->id }})">Deletando...</span>
    <span wire:loading.remove wire:target="delete({{ $file->id }})">Deletar</span>
</button>
  
</x-file-box>

@endforeach
@foreach($this->form->arquivos as $arquivo)
<div class="border border-solid border-blue-500 p-2 rounded-sm flex justify-center gap-4 items-center" wire:key="'{{ $arquivo}}'">
            @switch($arquivo->getClientOriginalExtension())
    @case('pdf')
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 16 16"><g fill="#251BEB"><path d="M5.523 12.424c.14-.082.293-.162.459-.238a7.878 7.878 0 0 1-.45.606c-.28.337-.498.516-.635.572a.266.266 0 0 1-.035.012a.282.282 0 0 1-.026-.044c-.056-.11-.054-.216.04-.36c.106-.165.319-.354.647-.548m2.455-1.647c-.119.025-.237.05-.356.078a21.148 21.148 0 0 0 .5-1.05a12.045 12.045 0 0 0 .51.858c-.217.032-.436.07-.654.114m2.525.939a3.881 3.881 0 0 1-.435-.41c.228.005.434.022.612.054c.317.057.466.147.518.209a.095.095 0 0 1 .026.064a.436.436 0 0 1-.06.2a.307.307 0 0 1-.094.124a.107.107 0 0 1-.069.015c-.09-.003-.258-.066-.498-.256M8.278 6.97c-.04.244-.108.524-.2.829a4.86 4.86 0 0 1-.089-.346c-.076-.353-.087-.63-.046-.822c.038-.177.11-.248.196-.283a.517.517 0 0 1 .145-.04c.013.03.028.092.032.198c.005.122-.007.277-.038.465z"/><path fill-rule="evenodd" d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2zM4.165 13.668c.09.18.23.343.438.419c.207.075.412.04.58-.03c.318-.13.635-.436.926-.786c.333-.401.683-.927 1.021-1.51a11.651 11.651 0 0 1 1.997-.406c.3.383.61.713.91.95c.28.22.603.403.934.417a.856.856 0 0 0 .51-.138c.155-.101.27-.247.354-.416c.09-.181.145-.37.138-.563a.844.844 0 0 0-.2-.518c-.226-.27-.596-.4-.96-.465a5.76 5.76 0 0 0-1.335-.05a10.954 10.954 0 0 1-.98-1.686c.25-.66.437-1.284.52-1.794c.036-.218.055-.426.048-.614a1.238 1.238 0 0 0-.127-.538a.7.7 0 0 0-.477-.365c-.202-.043-.41 0-.601.077c-.377.15-.576.47-.651.823c-.073.34-.04.736.046 1.136c.088.406.238.848.43 1.295a19.697 19.697 0 0 1-1.062 2.227a7.662 7.662 0 0 0-1.482.645c-.37.22-.699.48-.897.787c-.21.326-.275.714-.08 1.103"/></g></svg>
    @break
    @case ('jpg')
   @case ('jpeg')
   @case ('png')
   @case('gif')
   <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="#251BEB" d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm2-4h10q.3 0 .45-.275t-.05-.525l-2.75-3.675q-.15-.2-.4-.2t-.4.2L11.25 16L9.4 13.525q-.15-.2-.4-.2t-.4.2l-2 2.675q-.2.25-.05.525T7 17m1.5-7q.625 0 1.063-.438T10 8.5q0-.625-.437-1.062T8.5 7q-.625 0-1.062.438T7 8.5q0 .625.438 1.063T8.5 10"/></svg>
   @break
   @case('docx')
   <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 256 256"><path fill="#888888" d="M200 20H72a20 20 0 0 0-20 20v16H36a20 20 0 0 0-20 20v104a20 20 0 0 0 20 20h16v16a20 20 0 0 0 20 20h128a20 20 0 0 0 20-20V40a20 20 0 0 0-20-20m-32 88h28v40h-28ZM76 44h120v40h-28v-8a20 20 0 0 0-20-20H76ZM40 80h104v96H40Zm36 132v-12h72a20 20 0 0 0 20-20v-8h28v40Zm-11.64-57.09l-12-48a12 12 0 1 1 23.28-5.82l4.13 16.53l1.5-3a12 12 0 0 1 21.46 0l1.5 3l4.13-16.53a12 12 0 0 1 23.28 5.82l-12 48a12 12 0 0 1-10.33 9a11.62 11.62 0 0 1-1.31.09a12 12 0 0 1-10.73-6.63L92 146.83l-5.27 10.54a12 12 0 0 1-22.37-2.46"/></svg>
   @default
   <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="#888888" d="M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h8l6 6v3q-.575.125-1.075.4t-.925.7l-6 5.975V22zm8 0v-3.075l5.525-5.5q.225-.225.5-.325t.55-.1q.3 0 .575.113t.5.337l.925.925q.2.225.313.5t.112.55q0 .275-.1.563t-.325.512l-5.5 5.5zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"/></svg>
   @endswitch
                <div>{{ $arquivo->getClientOriginalName() }}</div>
                <button   wire:click.prevent="limpar('{{ $arquivo }}')" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="limpar('{{ $arquivo }}')" class="bg-red-500 text-white p-2 rounded-lg">
                    <span wire:loading wire:target="limpar('{{ $arquivo }}')">Deletando...</span>
                    <span wire:loading.remove wire:target="limpar('{{ $arquivo }}')">Deletar</span>
                </button>            
                </div>       
@endforeach
<div
        x-data="{ uploading: false, progress: 0 }"
        x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false"
        x-on:livewire-upload-error="uploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
    >
      
<label for="upload" class="bg-blue-600 text-white px-4 py-2 rounded-sm flex items-center justify-center cursor-pointer">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
  </label>
  <input type="file" wire:model="form.arquivos" id="upload" multiple class="hidden">
  <div x-show="uploading">
        <progress max="100" x-bind:value="progress" class="w-full h-4 bg-gray-300 rounded-full overflow-hidden">
        <div class="h-full bg-blue-500"></div>
    </progress>
 </div>
  </div>
  <button type="submit" class="text-center text-white p-2 bg-blue-600 rounded-sm" wire:loading.attr="disabled" wire:target="update" wire:loading.class="opacity-50">
    <span wire:loading wire:target="update">Editando...</span>
    <span wire:loading.remove wire:target="update">Editar</span>
  </button>
  </form>
</div>
