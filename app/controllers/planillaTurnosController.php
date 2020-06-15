<?php

namespace App\Controllers;

include "app/models/TurnosDBModel.php";

use App\Core\App;
use App\Core\Router;
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
        return view('listadoTurnosView', array('lista_turnos' => $planilla));
    }

    public function verTurnoReservado()
    {
        
        $this->turno = $this->dbturnos->getTurnoSeleccionado($_GET['id_turno']);
        
        // echo("<pre>");    
        // echo("verTurnoReservado<br>");    
        // var_dump(!empty($this->turno));
        // exit();
        if(!empty($this->turno)){
            $this->turno[0]['imagen'] = base64_encode($this->turno[0]['imagen']); 
            return view('consulta.turno.view', array('turno' => $this->turno[0]));
        }else{
            return redirect('turno-no-encontrado');
        }                

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