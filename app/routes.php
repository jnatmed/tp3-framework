 <?php

    $router->get('', 'form_controller@mostrarFormulario');
    $router->post('save_formulario', 'form_controller@guardarFormulario');
    $router->post('turno_confirmado', 'form_controller@reservarTurno');

    $router->get('planilla_turnos', 'planillaTurnosController@verPlanillaTurnos');
    $router->post('ver_turno_reservado', 'planillaTurnosController@verTurnoReservado');

   //  $router->post('users', 'UsersController@store');

   //  $router->get('tasks/create', 'TasksController@create');
   //  $router->post('tasks/save', 'TasksController@save');

    $router->get('not_found', 'ProjectController@notFound');
    $router->get('internal_error', 'ProjectController@internalError');
