 <?php

    $router->get('', 'form_controller@mostrarFormulario');
    $router->post('save_formulario', 'form_controller@guardarFormulario');
    $router->post('turno_confirmado', 'form_controller@reservarTurno');

    $router->get('planilla_turnos', 'planillaTurnosController@verPlanillaTurnos');
    $router->get('ver_turno_reservado', 'planillaTurnosController@verTurnoReservado');

    $router->post('edicion_turno', 'form_controller@edicion_turno');
    $router->post('guardar_modificacion_turno', 'form_controller@guardarTurnoModificado');

   //  $router->get('tasks/create', 'TasksController@create');
   //  $router->post('tasks/save', 'TasksController@save');

    $router->get('not_found', 'ProjectController@notFound');
    $router->get('internal_error', 'ProjectController@internalError');
