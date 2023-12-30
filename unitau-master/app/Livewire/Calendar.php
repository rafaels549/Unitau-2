<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Layout};
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Tarefa;



class Calendar extends Component
{

    public function newEvent($name, $startDate, $endDate)
    {
        $validated = Validator::make(
            [
                'texto' => $name,
                'StartDate' => $startDate,
                'EndDate' => $endDate,
            ],
            [
                'texto' => 'required|min:1|max:40',
                'StartDate' => 'required',
                'EndDate' => 'required',
            ]
        )->validate();

        $startDate = Carbon::parse($validated['StartDate'])->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($validated['EndDate'])->format('Y-m-d H:i:s');

        $tarefa = auth()->user()->tarefas()->create([
            'texto' => $validated['texto'],
            'StartDate' => $startDate,
        'EndDate' => $endDate,
            'descricao' => 'Qualquer coisa',
            'equipe_id' => 1,
            'status' => 0,

        ]);

        return $tarefa->id;
    }

    public function updateEvent($id, $name, $startDate, $endDate)
    {
        $validated = Validator::make(
            [
                'StartDate' => $startDate,
                'EndDate' => $endDate,
            ],
            [
                'StartDate' => 'required',
                'EndDate' => 'required',
            ]
        )->validate();
    
        // Converta as datas para o formato desejado
        $startDate = Carbon::parse($validated['StartDate'])->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($validated['EndDate'])->format('Y-m-d H:i:s');
    
        Tarefa::findOrFail($id)->update([
            'StartDate' => $startDate,
            'EndDate' => $endDate,
        ]);
    }
    


    #[Layout('components.layouts.teams')] 
    public function render()
    {
        $user = auth()->user();
        $tarefas = [];

        foreach ($user->tarefas as $tarefa) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $tarefa->StartDate);
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $tarefa->EndDate);
            $tarefas[] =  [
             
                'id' => $tarefa->id,
                'title' => $tarefa->texto,
                'start' => $startDate->toIso8601String(),
                'end' => $endDate->toIso8601String(),
            ];
        }
        return view('livewire.unitau.calendar',[
            'tarefas' => $tarefas
        ]);
    }

    
}
