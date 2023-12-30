

<div class="w-full mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl mb-4 "  wire:transition wire:key="{{ $tarefa->id }}">
                            <div class="p-8 flex justify-between">
                                <div class="pr-4">
                                    <div class="flex items-center">
                                        <img src="{{ asset('storage/' . $tarefa->equipe->imagem) }}" class="w-16 h-16" />
                                        <p class="ml-2 font-bold">{{ $tarefa->texto }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
     <a class="bg-blue-600 p-5 rounded-lg text-white" href="/equipe/{{ $tarefa->equipe->id }}/tarefas/{{ $tarefa->id }}" wire:navigate>Ver Tarefa</a>                               <!--  -->
                                  
                                </div>
                            </div>
                        </div>