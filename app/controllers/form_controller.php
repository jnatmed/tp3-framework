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

        /**
         * el `id` => puede ser -1 porque al ser un nuevo ingreso, todavia no tiene un id de turno
         * a parte, como uso la misma vista `modificar.turno.view` tengo que diferenciar
         * cuando envio la informacion al render, si se trata de una correccion de ingreso
         * o de una modificacion que quiere hacer el usuario 
         */

        $arreglo = [
            'dato_persona' => $this->lista_datos, 
            'dato_turno' => $this->lista_datos_del_turno, 
            'id' => -1,
            'error_imagen' => array_key_exists("error_imagen", $_POST) ? $_POST['error_imagen'] : false,
            'error_fecha_turno' => array_key_exists("error_fecha_turno", $_POST) ? $_POST['error_fecha_turno'] : false,
            'error_dia_turno' => array_key_exists("error_dia_turno", $_POST) ? $_POST['error_dia_turno'] : false,
            'error_edad' => array_key_exists("error_edad", $_POST) ? $_POST['error_edad'] : false,
        ];

        /**
         * dentro de los input ocultos, tambien envio el archivo de la imagen en base64, 
         * si el mismo fue cargado y no tiene errores de imagen entonces lo tengo que volver
         * a mandar para que sea ingresado
         */
        if(!empty($_POST['archivo_imagen']) && !array_key_exists("error_imagen",$_POST)){
                $arreglo['tipo_imagen'] = $_POST['tipo_imagen'];
                $arreglo['archivo_imagen'] = $_POST['archivo_imagen'];

                // echo("<pre>");
                // echo("modificacionTurno => arreglo<br>");
                // var_dump($arreglo);
                // exit();
        }

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
            'tipo_imagen' => $valores['tipo_imagen'],
            'datos_mal_cargados' => $this->datos_mal_cargados       
        ];

        // echo("<pre>");
        // echo("modificacionTurno => arreglo<br>");
        // var_dump($arreglo['dato_persona']);
        // var_dump($arreglo['dato_turno']);
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
            $this->datos_mal_cargados['error_edad'] = '#ERROR EDAD FECHA NACIMIENTO: la edad debe ser consistente con la fecha de nacimiento';
        }
        if(date("l",$fecha_turno) == 'Sunday'){ // que no sea dia domingo
            $this->datos_mal_cargados['error_dia_turno'] = '#ERROR DIA TURNO: la fecha del turno no puede ser domingo';
        }
        if( $fecha_actual > $fecha_turno){ // que sea superior a la fecha actual
            $this->datos_mal_cargados['error_fecha_turno'] = '#ERROR FECHA TURNO: la fecha del turno debe ser superior o igual al dia actual';    
        }

        if($_FILES['imagen_receta']['size'] > 0){
            $this->controlImagen($_FILES);
        }

    }

    public function controlImagen($files){
        $paramsImagen = [
            'extension' => pathinfo($files["imagen_receta"]["name"], PATHINFO_EXTENSION),
            'tamanio' => $files['imagen_receta']['size'],
            'archivo' => file_get_contents($files['imagen_receta']['tmp_name'])
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
                    $this->datos_mal_cargados['error_imagen'] = "#ERROR IMAGEN: Tipo de imagen no valido.";
                }    
            }else{
                $this->datos_mal_cargados['error_imagen'] = "#ERROR IMAGEN: Imagen no cargada, Tamanio de carga Excedido => ".$this->imgController->getTamanioEnMB();
             }
        }else{
            echo("Imagen no cargada");
        } 
    }    
    public function edicion_turno(){
        if(isset($_POST['baja_turno'])){
            return $this->bajaTurnoReservado();
        }else if(isset($_POST['modificacion_turno'])){
            // echo("<pre>");
            // echo("modificacion<br>");
            // var_dump($_POST['modificacion_turno']);
            // exit(0);
            return $this->modificacionTurno();
        }
}

    public function bajaTurnoReservado()
    {
        // echo("<pre>");    
        // echo("bajaTurnoReservado<br>");    
        // var_dump($_POST);
        // exit();
        $this->turno = $this->dbturnos->bajaTurnoSeleccionado($_POST);
        return $this->verPlanillaTurnos();
    }

    public function guardarTurnoModificado(){

        $this->controlFormulario($_POST,$_FILES); 
        
        /**
         * aca luego del control del formulario
         * tengo que preguntar de estado anterior vengo. 
         * si el $_POST['id'] == -1 significa que es un nuevo
         * ingreso y tuvo un error por eso entro en esta rama 
         * 
         * si es distinto de -1 es un turno que ya tiene id
         * y el estado anterior se pulso la opcion de modificacion de
         * turno. 
         * 
         * si se hizo una correccion pero aun hay datos mal cargados
         * se vuelve a controlar y en caso de seguir habiendo errores
         * se vuelven a cargar y se mandar de nuevo a la rama de correccion
         * en la opcion 
         *       =>   $this->corregirIngreso()
         */
        if(empty($this->datos_mal_cargados)){
            if($_POST['id']<>-1){
                $this->dbturnos->actualizarTurno($_POST,$_FILES);
            }else {
                $this->dbturnos->insertarTurno($_POST);
            }
            return $this->planillaController->verPlanillaTurnos();
        }else{

            /**
             * cuando el formulario vuelve a ser ingresado por correccion
             * aun no paso por el control de error. el arreglo $_POST
             * no tiene las keys de errores, hay que agregarlas.
             * 
             * luego se pasan al metodo de corregirIngreso()
             */
            foreach ($this->datos_mal_cargados as $error => $detalle){
                $_POST[$error] = $detalle;
            }
            return $this->corregirIngreso();    
        }
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

        $turnos = $this->dbturnos->getTurnos();
        
        return view('listadoTurnosView', array('lista_turnos' => $turnos));

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

            return $this->verPlanillaTurnos();
        }else{
           return $this->corregirIngreso();
        }
    }

}

?>