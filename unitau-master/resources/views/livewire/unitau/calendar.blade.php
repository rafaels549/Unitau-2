<div>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js' defer></script>
<script src="
https://cdn.jsdelivr.net/npm/@fullcalendar/moment-timezone@6.1.10/index.global.min.js
" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/pt-br.js" defer></script>
  


    <div wire:ignore id='calendar' class="ml-24"></div>
    <div id="loadingIndicator" class="text-center p-5">

    </div>


    <script>
            document.addEventListener('livewire:navigated', function() {
                // Exibe o indicador de carregamento
                var loadingIndicator = document.getElementById('loadingIndicator');
                loadingIndicator.innerHTML = '<div class="spinner-border" role="status"><span class="loader"></span></div>';

                setTimeout(function() {
                    var calendarEl = document.getElementById('calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        
                        headerToolbar: {
    left: 'prev,next today',
    right: 'dayGridMonth,timeGridWeek,timeGridDay' // Adicionei a vírgula aqui
},
                        initialView: 'timeGridWeek',
                        timeZone: 'America/Sao_Paulo',
                        editable: true,
                      
                        selectable: true,
                        events: @json($tarefas),
                        locale: 'pt-br', 
						validRange: {
                        start: new Date(), // Impede datas anteriores à data atual
                    },
                        select: function(data) {
                            var event_name = prompt('Nome da Tarefa:');
                            if (!event_name) {
                                return;
                            }
                            @this.newEvent(event_name, data.start.toISOString(), data.end.toISOString())
                                .then(function(id) {
                                    calendar.addEvent({
                                        id: id,
                                        title: event_name,
                                        start: data.startStr,
                                        end: data.endStr,
                                    });
                                    calendar.unselect();
                                });
                        },
                        eventDrop: function(data) {
                            console.log(data.event.id)
                            @this.updateEvent(
                                data.event.id,
                                data.event.name,
                                data.event.start.toISOString(),
                                data.event.end.toISOString()).then(function() {
                                    alert('moved event');
                                })
                        },
                    });

                    // Remove o indicador de carregamento
                    loadingIndicator.innerHTML = '';

                    calendar.render();
                }, 1000);
            });
        </script> 
		</div>