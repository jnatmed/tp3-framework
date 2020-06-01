<?php

namespace App\Controllers;

include "app/models/TurnosDBModel.php";

use App\Core\App;
use \App\models\TurnosDBModel;
use \App\controllers\imagenController;

class planillaTurnosController
{
    public $planillaTurnos = [];
    public $tamaño_planilla = 0;
    public $archivoTurnos = "";
    public $turno = [];
    public $dbturnos;
    public $loader;
    public $twig;

    public function __construct()
    {
        // ACA SE OBTIENE LA BASE DE DATOS   
        $this->dbturnos = new TurnosDBModel;
    }

    public function verPlanillaTurnos()
    {
        // echo("pre");
        // var_dump($this->planillaTurnos);
        // exit(0);

        $this->planillaTurnos = $this->dbturnos->getTurnos();

        $planilla = $this->planillaTurnos;
        // echo("pre");
        // var_dump($planilla);
        // exit(0);
        // echo $this->twig->render('listadoTurnosView.html', array('lista_turnos' => $planilla));
        return view('listadoTurnosView', array('lista_turnos' => $planilla));
    }

    public function verTurnoReservado()
    {
        $this->turno = $this->dbturnos->getTurnoSeleccionado($_POST['id_turno']);
        
        // echo("<pre>");    
        // echo("verTurnoReservado<br>");    
        // var_dump($this->turno[0]);
        // exit();

        $this->turno[0]['imagen'] = base64_encode($this->turno[0]['imagen']); 

        return view('consulta.turno.view', array('turno' => $this->turno[0]));

    }
    public function bajaTurnoReservado()
    {
        // echo("<pre>");    
        // echo("bajaTurnoReservado<br>");    
        // var_dump($_POST);
        // exit();
        $this->turno = $this->dbturnos->bajaTurnoSeleccionado($_POST);
        $this->verPlanillaTurnos();
    }
}

?>