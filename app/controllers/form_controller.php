<?php

namespace App\Controllers;

// include 'models/TurnosDBModel.php';
include 'app/controllers/imagenController.php';
include 'app/controllers/planillaTurnosController.php';

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
    public $datos_mal_cargados; // arreglo donde cargo los errores encontrados en la carga del turno
    public $rangos;

    public function __construct()
    {
        $this->planillaController = new planillaTurnosController;
        $this->dbturnos = new TurnosDBModel;

        $this->rangos = [
            'edades' => [ 'min' => '18', 
                          'max' => '60'],
            'talles_calzado' => [ 'min' => '20', 
                                  'max' => '45'],
            'alturas' => [ 'min' => '100', 
                           'max' => '280'],
            'colores_pelo' => [ 'rubio' , 'negro', 'castaño' , 'marron'],
            'horarios_atencion' => [ 'rango_atencion' => ['08', '09', '10', '11','12', '13', '14', '15', '16'],  
                                     'intervalos' => ['00', '15', '30', '45']]
        ];

    }

    public function carga_arreglo($datosTurno, $pathImg = "", $tipo_imagen = "")
    {        
        // echo("<pre>");
        // var_dump($datosTurno);
        // exit(); 
        $this->datos_reserva['nombre_paciente'] = $datosTurno['nombre_paciente'];
        $this->datos_reserva['email'] = $datosTurno['email'];
        $this->datos_reserva['telefono'] = $datosTurno['telefono'];
        $this->datos_reserva['edad'] = intval($datosTurno['edad']); 
        $this->datos_reserva['talla_calzado'] = $datosTurno['talla_calzado'];
        $this->datos_reserva['altura'] = $datosTurno['altura'];
        $this->datos_reserva['fecha_nacimiento'] = $datosTurno['fecha_nacimiento'];
        $this->datos_reserva['color_pelo'] = $datosTurno['color_pelo'];
        $this->datos_reserva['fecha_turno'] = $datosTurno['fecha_turno'];
        $this->datos_reserva['hora_turno'] = $datosTurno['hora_turno'];
        $this->datos_reserva['tipo_imagen'] = $tipo_imagen;    
    }

    public function mostrarFormulario()
    {

        $this->agregar_dato('nombre_paciente','required','nombre','');
        $this->agregar_dato('email', 'required','email','[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$');
        $this->agregar_dato('telefono', 'required','tel','[0-1]{2}-[0-9]{4}-[0-9]{4}');
        $this->agregar_dato('edad', '', 'edad',$this->rangos['edades']);
        $this->agregar_dato('talla_calzado', '', 'calzado',$this->rangos['talles_calzado']);
        $this->agregar_dato('altura', '','altura',$this->rangos['alturas']);
        $this->agregar_dato('fecha_nacimiento', 'required','date');
        $this->agregar_dato('color_pelo','required','pelo',$this->rangos['colores_pelo']);
        $this->agregar_dato('fecha_turno', 'required','date');
        $this->agregar_dato('hora_turno', '', 'horario_turno',$this->rangos['horarios_atencion']);

        // echo("<pre>");
        // echo("mostrarFormulario<br>");
        // var_dump($this->lista_datos);
        // var_dump($this->lista_datos_del_turno);
        // exit();
        
        $arreglo = [
            'dato_persona' => $this->lista_datos, 
            'dato_turno' => $this->lista_datos_del_turno
        ];

        return view('nuevo.turno.view', $arreglo);
    }

    public function corregirIngreso(){

        $this->agregar_dato('nombre_paciente','required','nombre','',$_POST['nombre_paciente']);
        $this->agregar_dato('email', 'required','email','[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',$_POST['email']);
        $this->agregar_dato('telefono', 'required','tel','[0-1]{2}-[0-9]{4}-[0-9]{4}',$_POST['telefono']);
        $this->agregar_dato('edad', '', 'edad',$this->rangos['edades'],$_POST['edad']);
        $this->agregar_dato('talla_calzado', '', 'calzado',$this->rangos['talles_calzado'],$_POST['talla_calzado']);
        $this->agregar_dato('altura', '','altura',$this->rangos['alturas'], $_POST['altura']);
        $this->agregar_dato('fecha_nacimiento', 'required','date','',$_POST['fecha_nacimiento']);
        $this->agregar_dato('color_pelo','required','pelo',$this->rangos['colores_pelo'],$_POST['color_pelo']);
        $this->agregar_dato('fecha_turno', 'required','date','',$_POST['fecha_turno']);
        $this->agregar_dato('hora_turno', '', 'horario_turno',$this->rangos['horarios_atencion'],$_POST['hora_turno']);

        $arreglo = [
            'dato_persona' => $this->lista_datos, 
            'dato_turno' => $this->lista_datos_del_turno, 
            'id' => -1,
            'tipo_imagen' => pathinfo($_FILES["imagen_receta"]["name"], PATHINFO_EXTENSION),
            'archivo_imagen' => base64_encode(file_get_contents($_FILES['imagen_receta']['tmp_name']))
        ];

        // echo("<pre>");
        // echo("modificacionTurno => arreglo<br>");
        // var_dump($arreglo);
        // exit();

        return view('modificar.turno.view', $arreglo);
    }

    public function modificacionTurno(){
        $valores = [];
        
        $valores = $this->dbturnos->getTurnoSeleccionado($_POST['modificacion_turno']);
        $valores = $valores[0];
        // echo("<pre>");
        // echo("modificacionTurno<br>");
        // var_dump($valores);
        // exit();
        $this->agregar_dato('nombre_paciente','required','nombre','',$valores['nombre_paciente']);
        $this->agregar_dato('email', 'required','email','[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',$valores['email']);
        $this->agregar_dato('telefono', 'required','tel','[0-1]{2}-[0-9]{4}-[0-9]{4}',$valores['telefono']);
        $this->agregar_dato('edad', '', 'edad',$this->rangos['edades'],$valores['edad']);
        $this->agregar_dato('talla_calzado', '', 'calzado',$this->rangos['talles_calzado'],$valores['talla_calzado']);
        $this->agregar_dato('altura', '','altura',$this->rangos['alturas'], $valores['altura']);
        $this->agregar_dato('fecha_nacimiento', 'required','date','',$valores['fecha_nacimiento']);
        $this->agregar_dato('color_pelo','required','pelo',$this->rangos['colores_pelo'],$valores['color_pelo']);
        $this->agregar_dato('fecha_turno', 'required','date','',$valores['fecha_turno']);
        $this->agregar_dato('hora_turno', '', 'horario_turno',$this->rangos['horarios_atencion'],$valores['hora_turno']);
        
        // echo("<pre>");
        // echo("modificacionTurno<br>");
        // var_dump($this->lista_datos);
        // var_dump($this->lista_datos_del_turno);
        // exit();

        $arreglo = [
            'dato_persona' => $this->lista_datos, 
            'dato_turno' => $this->lista_datos_del_turno, 
            'id' => $_POST['modificacion_turno'],
            'archivo_imagen' => base64_encode($valores['imagen']),
            'tipo_imagen' => $valores['tipo_imagen']            
        ];

        // echo("<pre>");
        // echo("modificacionTurno => arreglo<br>");
        // var_dump($arreglo);
        // exit();

        return view('modificar.turno.view', $arreglo);
    }

    public function agregar_dato($nombre_campo, $obligatorio = '', $tipo, $restriccion='', $valor = '')
    {
        $arreglo_nombre_campos = ['fecha_turno','hora_turno'];

        $this->tipo_restriccion['nombre_campo'] = $nombre_campo;
        $this->tipo_restriccion['obligatorio'] = $obligatorio;
        $this->tipo_restriccion['tipo'] = $tipo;
        $this->tipo_restriccion['restriccion'] = $restriccion;
        $this->tipo_restriccion['valor'] = $valor;


        if (in_array($this->tipo_restriccion['nombre_campo'],$arreglo_nombre_campos)){
            $this->lista_datos_del_turno[$nombre_campo] = $this->tipo_restriccion;
            
        }else{
            $this->lista_datos[$nombre_campo] = $this->tipo_restriccion;
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
        $fecha_turno = strtotime(date("d-m-Y",strtotime($_POST['fecha_turno'])));
        $fecha_nacimiento = date("d-m-Y H:i:00",strtotime($_POST['fecha_nacimiento']));
        $año_nacimiento = intval(date("o",strtotime($_POST['fecha_nacimiento'])));
        $edad_ingresada = $_POST['edad'];
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

        $paramsImagen = [
            'extension' => pathinfo($_FILES["imagen_receta"]["name"], PATHINFO_EXTENSION),
            'tamanio' => $_FILES['imagen_receta']['size'],
            'archivo' => file_get_contents($_FILES['imagen_receta']['tmp_name'])
        ];

        $this->imgController = new imagenController($paramsImagen);

        // echo("<pre>");
        // var_dump($this->imgController);
        // exit(0);

        if ($this->imgController->imagenCargada()){
            if($this->imgController->controlTamanioMaximoImagen()){
                if($this->imgController->controlTipoImagenValida()){
                    $this->datos_reserva['tipo_imagen'] = $this->imgController->getTipoImagen();
                    $this->datos_reserva['archivo_imagen'] = $this->imgController->getArchivoImagen();
                }else{
                    $this->datos_mal_cargados[] = "#ERROR IMAGEN: Tipo de imagen no valido.";
                }    
            }else{
                $this->datos_mal_cargados[] = "#ERROR IMAGEN: Imagen no cargada, Tamanio de carga Excedido => ".$this->imgController->getTamanioEnMB();
             }
        }else{
            echo("Imagen no cargada");
        } 

    }

    public function edicion_turno(){
        if(isset($_POST['baja_turno'])){
            $this->bajaTurnoReservado();
        }else if(isset($_POST['modificacion_turno'])){
            // echo("<pre>");
            // echo("modificacion<br>");
            // var_dump($_POST['modificacion_turno']);
            // exit(0);
            $this->modificacionTurno();
        }else{
            echo("<pre>");
            echo("ninguna opcion");
            var_dump($_POST['modificacion_turno']);
            exit(0);
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
        
        // echo("<pre>");
        // var_dump($this->datos_reserva);
        // exit(0);
        $arreglo = [
            'turno' => $this->datos_reserva,
            'datos_mal_cargados' => $this->datos_mal_cargados,
            'tipo_imagen' => pathinfo($_FILES["imagen_receta"]["name"], PATHINFO_EXTENSION),
            'archivo_imagen' => base64_encode(file_get_contents($_FILES['imagen_receta']['tmp_name']))
        ];

        return view('confirmar.turno.view', $arreglo);
    }

    public function guardarTurnoConfirmado()
    {
        // echo("<pre>");
        // echo("guardarTurnoConfirmado<br>");
        // var_dump($turno);
        // exit();        
        $this->dbturnos->insertarTurno($_POST);
    }

    public function verPlanillaTurnos(){

        return view('listadoTurnosView', array('lista_turnos' => $this->dbturnos->getTurnos()));

    }

    public function reservarTurno()
    {   
        // echo("<pre>");
        // echo("reservarTurno<br>");
        // var_dump($_POST);
        // exit();

        if (array_key_exists('enviar',$_POST)){
            $this->guardarTurnoConfirmado($_POST);
        // echo("<pre>");
        // echo("reservarTurno--<br>");
        // var_dump($_POST);

            $this->verPlanillaTurnos();
        }else{
            $this->corregirIngreso();
        }
    }

}

?>