<?php

namespace App\Controllers;

// include 'models/TurnosDBModel.php';
include 'app/controllers/imagenController.php';
include 'app/controllers/planilla.turnos.controller.php';

use \App\models\TurnosDBModel;
use \App\controllers\imagenController;
use \App\controllers\planillaTurnosController;

class form_controller
{
    public $lista_datos = []; // lista de carga de estructura de ingreso del turno
    public $lista_datos_del_turno = []; // 
    public $tipo_restriccion = []; // estructura de cada campo de ingreso con (su tipo, si es obligatorio, restriccion, patron y valor)
    public $datos_reserva = []; // hash que contiene los datos a cargarse en la vista de ingreso del turno
    public $planilla = []; 
    public $planillaController = NULL; // clase planilla controller
    public $imgController = NULL; // clase que controla el ingreso correcto de la imagen
    public $dbturnos; // base de datos de turnos, donde hago las consultas de turnos y modificaciones
    public $id_turno_update; // identificador del turno, que se pasa a la vista de "modificacion.turno.view"
    public $datos_mal_cargados; // arreglo donde cargo los errores encontrados en la carga del turno

    public function __construct()
    {
        $this->planillaController = new planillaTurnosController;
        $this->dbturnos = new TurnosDBModel;
    }

    public function carga_arreglo($datosTurno, $pathImg = "", $tipo_imagen = "")
    {        
        // echo("<pre>");
        // var_dump($datosTurno);
        // exit(); 
        $this->datos_reserva['nombre_paciente'] = $datosTurno['Nombre_del_Paciente'];
        $this->datos_reserva['email'] = $datosTurno['Email'];
        $this->datos_reserva['telefono'] = $datosTurno['Telefono'];
        $this->datos_reserva['edad'] = intval($datosTurno['Edad']); 
        $this->datos_reserva['talla_calzado'] = $datosTurno['Talla_de_calzado'];
        $this->datos_reserva['altura'] = $datosTurno['altura'];
        $this->datos_reserva['fecha_nacimiento'] = $datosTurno['Fecha_de_nacimiento'];
        $this->datos_reserva['color_pelo'] = $datosTurno['Color_de_pelo'];
        $this->datos_reserva['fecha_turno'] = $datosTurno['Fecha_del_turno'];
        $this->datos_reserva['hora_turno'] = $datosTurno['Horario_del_turno'];
        $this->datos_reserva['dir_img'] = $pathImg;    
        $this->datos_reserva['tipo_imagen'] = $tipo_imagen;    
    }

    public function mostrarFormulario()
    {
        $this->agregar_dato('Nombre del Paciente','required','nombre','[a-zA-Z]+');
        $this->agregar_dato('Email', 'required','email','[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$');
        $this->agregar_dato('Telefono', 'required','tel','[0-1]{2}-[0-9]{4}-[0-9]{4}');
        $this->agregar_dato('Edad', '', 'edad','18-60');
        $this->agregar_dato('Talla de calzado', '', 'calzado','20-45');
        $this->agregar_dato('altura', '','altura','1-3');
        $this->agregar_dato('Fecha de nacimiento', 'required','date');
        $this->agregar_dato('Color de pelo','required','pelo','rubio-negro-castaño-marron');
        $this->agregar_dato('Fecha del turno', 'required','date');
        $this->agregar_dato('Horario del turno', '', 'horario_turno','8-17-15');

        // echo("<pre>");
        // echo("mostrarFormulario<br>");
        // var_dump($this->lista_datos);
        // var_dump($this->lista_datos_del_turno);
        // exit();
        

        include "app/views/nuevo.turno.view.php";
    }

    public function corregirIngreso(){

        $this->agregar_dato('Nombre del Paciente','required','nombre','[a-zA-Z]+',$_POST['Nombre_del_Paciente']);
        $this->agregar_dato('Email', 'required','email','[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',$_POST['Email']);
        $this->agregar_dato('Telefono', 'required','tel','[0-1]{2}-[0-9]{4}-[0-9]{4}',$_POST['Telefono']);
        $this->agregar_dato('Edad', '', 'edad','18-60',$_POST['Edad']);
        $this->agregar_dato('Talla de calzado', '', 'calzado','20-45',$_POST['Talla_de_calzado']);
        $this->agregar_dato('altura', '','altura','1-3', $_POST['altura']);
        $this->agregar_dato('Fecha de nacimiento', 'required','date','',$_POST['Fecha_de_nacimiento']);
        $this->agregar_dato('Color de pelo','required','pelo','rubio-negro-castaño-marron',$_POST['Color_de_pelo']);
        $this->agregar_dato('Fecha del turno', 'required','date','',$_POST['Fecha_del_turno']);
        $this->agregar_dato('Horario del turno', '', 'horario_turno','8-17-15',$_POST['Horario_del_turno']);

        include "app/views/modificar.turno.view.php";

    }

    public function modificacionTurno(){
        $valores = [];
        
        $valores = $this->dbturnos->getTurnoSeleccionado($_POST['modificacion_turno']);
        // echo("<pre>");
        // echo("modificacionTurno<br>");
        // var_dump($valores);
        // exit();
        $this->agregar_dato('Nombre del Paciente','required','nombre','[a-zA-Z]+',$valores[0]['nombre_paciente']);
        $this->agregar_dato('Email', 'required','email','[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',$valores[0]['email']);
        $this->agregar_dato('Telefono', 'required','tel','[0-1]{2}-[0-9]{4}-[0-9]{4}',$valores[0]['telefono']);
        $this->agregar_dato('Edad', '', 'edad','18-60',$valores[0]['edad']);
        $this->agregar_dato('Talla de calzado', '', 'calzado','20-45',$valores[0]['talla_calzado']);
        $this->agregar_dato('altura', '','altura','1-3', $valores[0]['altura']);
        $this->agregar_dato('Fecha de nacimiento', 'required','date','',$valores[0]['fecha_nacimiento']);
        $this->agregar_dato('Color de pelo','required','pelo','rubio-negro-castaño-marron',$valores[0]['color_pelo']);
        $this->agregar_dato('Fecha del turno', 'required','date','',$valores[0]['fecha_turno']);
        $this->agregar_dato('Horario del turno', '', 'horario_turno','8-17-15',$valores[0]['hora_turno']);
        
        // echo("<pre>");
        // echo("modificacionTurno<br>");
        // var_dump($this->lista_datos);
        // var_dump($this->lista_datos_del_turno);
        // exit();

        $this->id_turno_update = $_POST['modificacion_turno'];

        $this->imgController = new imagenController();
        if($valores[0]['imagen'] <> ''){
            $this->imgController->setTipoImagen($valores[0]['tipo_imagen']);
            $this->imgController->controlTipoImagenValida();
            $this->imgController->devolverPathImagen($valores[0]['imagen']);
        }



        include "app/views/modificar.turno.view.php";
    }

    public function agregar_dato($nombre_campo, $obligatorio = '', $tipo, $restriccion='', $valor = '')
    {
        $arreglo_nombre_campos = ['Fecha del turno','Horario del turno'];

        $this->tipo_restriccion['nombre_campo'] = $nombre_campo;
        $this->tipo_restriccion['obligatorio'] = $obligatorio;
        $this->tipo_restriccion['tipo'] = $tipo;
        $this->tipo_restriccion['restriccion'] = $restriccion;
        $this->tipo_restriccion['valor'] = $valor;


        if (in_array($this->tipo_restriccion['nombre_campo'],$arreglo_nombre_campos)){
            $this->lista_datos_del_turno[] = $this->tipo_restriccion;
            
        }else{
            $this->lista_datos[] = $this->tipo_restriccion;
        }
    }


        // entrada: 
        // - $_POST, $_FILES
        // salida:
        // - $this->datos_mal_cargados, con los errores encontrados
        // - obj imgController con los datos de la imagen cargada 

    public function controlFormulario($post, $files){
        $this->datos_mal_cargados = [];

        $this->carga_arreglo($_POST);

        $fecha_actual = strtotime(date("d-m-Y",time()));
        $fecha_turno = strtotime(date("d-m-Y",strtotime($_POST['Fecha_del_turno'])));
        $fecha_nacimiento = date("d-m-Y H:i:00",strtotime($_POST['Fecha_de_nacimiento']));
        $año_nacimiento = intval(date("o",strtotime($_POST['Fecha_de_nacimiento'])));
        $edad_ingresada = $this->datos_reserva['edad'];
        $año_actual = intval(date("o",time()));
        $dia_turno = date("l",$fecha_turno);

        if (($edad_ingresada + $año_nacimiento) < $año_actual){ // comprobar edad y fecha nacimiento
            $this->datos_mal_cargados[] = '#ERROR EDAD FECHA NACIMIENTO: la edad debe ser consistente con la fecha de nacimiento';
        }
        if(date("l",$fecha_turno) == 'Sunday'){ // que no sea dia domingo
            $this->datos_mal_cargados[] = '#ERROR DIA TURNO: la fecha del turno no puede ser domingo';
        }
        if( $fecha_actual > $fecha_turno){ // que sea superior a la fecha actual
            $this->datos_mal_cargados[] = '#ERROR FECHA TURNO: la fecha del turno debe ser superior o igual al dia actual';    
        }


        $this->imgController = new imagenController($_FILES);

        if ($this->imgController->imagenCargada()){
            if($this->imgController->controlTamanioMaximoImagen()){
                if($this->imgController->controlTipoImagenValida()){
                    $this->imgController->codificar();
                    $this->datos_reserva['dir_img'] = $this->imgController->getImagenCodificada();
                    $this->datos_reserva['tipo_imagen'] = $this->imgController->getTipoImagen();
                    $this->imgController->devolverPathImagen($this->datos_reserva['dir_img']);                
                }else{
                    $this->datos_mal_cargados[] = "#ERROR IMAGEN: Tipo de imagen no valido.";
                }    
            }else{
                $this->datos_mal_cargados[] = "#ERROR IMAGEN: Imagen no cargada, Tamanio de carga Excedido => ".$this->imgController->getTamanioEnMB();
             }
        }else{
            echo("Imagen no cargada");
        } 

        if ($this->buscarFechaTurno($this->datos_reserva['fecha_turno'],$this->datos_reserva['hora_turno'])){
            $this->datos_mal_cargados[] =  "#ERROR FECHA Y HORA TURNO: La fecha y turno cargados ya fueron asignados a otro paciente.";    
        }

    }

    public function buscarFechaTurno($fecha_turno, $hora_turno)
    {
        $existeTurno = false;
        foreach ($this->planillaTurnos as $id_turno => $turno):

            if ($turno['fecha_turno'] == $fecha_turno && $turno['hora_turno'] == $hora_turno)
            {
                $existeTurno = true;
            }
        endforeach;
        return $existeTurno;
    }

    public function guardarTurnoModificado(){
        // echo("<pre>");
        // echo("guardarTurnoModificado<br>");
        // var_dump($_FILES);
        // exit();        
        $this->controlFormulario($_POST,$_FILES); //     
        $this->dbturnos->actualizarTurno($_POST,$_FILES);
        $this->planillaController->verPlanillaTurnos();
    }

    public function guardarFormulario(){
        // entrada: 
        // - $_POST, $_FILES
        // salida:
        // - $this->datos_mal_cargados, con los errores encontrados
        // - obj imgController con los datos de la imagen cargada 
        $this->controlFormulario($_POST,$_FILES); //     

        include "app/views/confirmar.turno.view.php";
    }

    public function guardarTurnoConfirmado($turno)
    {
        // echo("<pre>");
        // echo("guardarTurnoConfirmado<br>");
        // var_dump($turno);
        // exit();        
        $this->dbturnos->insertarTurno($turno);
    }

    public function reservarTurno()
    {   
        // echo("<pre>");
        // echo("reservarTurno<br>");
        // var_dump($_POST);
        // exit();

        if (array_key_exists('enviar',$_POST)){
            $this->carga_arreglo($_POST,$_POST['dir_img'], $_POST['tipo_imagen']);
            $this->guardarTurnoConfirmado($this->datos_reserva);
            $this->planillaController->verPlanillaTurnos();
        }else{
            $this->corregirIngreso();
        }
    }

}

?>