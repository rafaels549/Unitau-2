<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;


class TarefaForm extends Form
{
  
    #[Validate('required|string')] 
    public $titulo = '';
 #[Validate('required')] 
    public $dateinicial = '';
 
    #[Validate('required')] 
    public $datefinal = '';
 
    #[Validate('nullable|string')] 
    public $descricao = '';

    #[Validate(['arquivos.*' => ''])]
    public $arquivos = [];

 



}
