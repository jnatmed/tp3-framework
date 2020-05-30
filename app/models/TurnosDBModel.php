<?php
namespace App\models;

require 'vendor/autoload.php';

use PDO;
use \App\controlador\imagenController;

use \Monolog\Logger;
use \Monolog\Handler\RotatingFileHandler;
use \Monolog\Handler\BrowserConsoleHandler;

class TurnosDBModel
{
   
    public $turnos;
    private $db, $dsn, $conexion;
    public $params;
    
    public function motrarMsj($msj){
        // echo($msj);
    }

    public function __construct(){
    /**
     * Cargo el objeto Logger
     *  */    
        $this->params = require 'config.php';
        $this->logger = new Logger('LogABMTurnosDataBase');
        $this->logger->pushHandler(new RotatingFileHandler('logs/LogABMTurnosDataBase.log'), 7);
        $this->logger->pushHandler(new BrowserConsoleHandler());

        $this->dsn = sprintf("mysql:host=%s;dbname=%s", $this->params['host'], $this->params['db']);
        try {
            $this->db = new PDO($this->dsn, $this->params['user'],$this->params['pwd']);    
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Throwable $th) {
            echo ("<pre>");
            var_dump($th);
            exit(0);   
        }
    }   

    private function setNames(){
        return $this->db->query("SET NAMES 'utf8'");
    }

    public function getTurnos(){
        // self::setNames();
        // var_dump($this->db);
        
        $sql = "SELECT * FROM turnos";   
        $result = $this->db->query($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        foreach ($result as $res){
            $this->turnos[] = $res;
        }

        return $this->turnos;
        $this->db = NULL;
    }       

    public function getTurnoSeleccionado($id_turno){

        // echo("<pre>");
        // echo("getTurnoSeleccionado<br>");
        // var_dump($id_turno);
        $sql = "SELECT * FROM turnos WHERE id ='{$id_turno}'";
        try{
            foreach ($this->db->query($sql) as $res){
                $resu[] = $res;
            }    

        // echo("<pre>");
        // echo("getTurnoSeleccionado<br>");
        // var_dump($resu);
        // exit();

            return $resu;
            $this->db = NULL;    
        }catch(Exception $e){
            echo($e);
        }
    }

    public function insertarTurno($valores){
        // echo("<pre>");
        // echo("insertarTurno<br>");
        // var_dump($valores);
        // exit(); 
        // $this->imgController = new imagenController();
        // $this->imgController->codificar($valores['dir_img']);
        $consulta = "INSERT INTO `turnos`(`id`, 
                                        `fecha_turno`, 
                                        `hora_turno`, 
                                        `nombre_paciente`, 
                                        `email`, 
                                        `telefono`, 
                                        `fecha_nacimiento`, 
                                        `edad`, 
                                        `talla_calzado`, 
                                        `altura`, 
                                        `color_pelo`, 
                                        `imagen`,
                                        `tipo_imagen`) VALUES (NULL,
                                                        '{$valores['fecha_turno']}',
                                                        '{$valores['hora_turno']}',
                                                        '{$valores['nombre_paciente']}',
                                                        '{$valores['email']}',
                                                        '{$valores['telefono']}',
                                                        '{$valores['fecha_nacimiento']}',
                                                        '{$valores['edad']}',
                                                        '{$valores['talla_calzado']}',
                                                        '{$valores['altura']}',
                                                        '{$valores['color_pelo']}',
                                                        '{$valores['dir_img']}',
                                                        '{$valores['tipo_imagen']}')";
        try{
            $this->motrarMsj($consulta);
            // echo($consulta);
            $sql = $this->db->prepare($consulta);
            $sql->execute();    
            unset($valores['dir_img']);
            unset($valores['tipo_imagen']);
            // $this->logger->info();   
            $this->logger->info("ALTA TURNO: ", $valores);
        }catch(Exception $e){
            echo($e);        
        }
    }
    public function actualizarTurno($valores,$img_receta){
        // echo("<pre>");
        // echo("insertarTurno<br>");
        // var_dump($img_receta);
        // exit(); 
        $this->imgController = new imagenController($img_receta);
        if($this->imgController->imagenCargada()){
            $this->imgController->codificar();
        }
        $consulta = "UPDATE `turnos` SET 
                            `fecha_turno`='{$valores['Fecha_del_turno']}',
                            `hora_turno`='{$valores['Horario_del_turno']}',
                            `nombre_paciente`='{$valores['Nombre_del_Paciente']}',
                            `email`='{$valores['Email']}',
                            `telefono`='{$valores['Telefono']}',
                            `fecha_nacimiento`='{$valores['Fecha_de_nacimiento']}',
                            `edad`='{$valores['Edad']}',
                            `talla_calzado`='{$valores['Talla_de_calzado']}',
                            `altura`='{$valores['altura']}',
                            `color_pelo`='{$valores['Color_de_pelo']}', 
                            `imagen`='{$this->imgController->getImagenCodificada()}',
                            `tipo_imagen`='{$this->imgController->getTipoImagen()}' WHERE id = '{$valores['id']}'";
        try{
            // $this->motrarMsj($consulta);
            // $p = explode("'imagen'",$consulta);
            // echo($p[0]);
            $sql = $this->db->prepare($consulta);
            // $sql->execute($valores);    
            $sql->execute(); 
            unset($valores['dir_img']);
            unset($valores['corregir_turno']);
            $this->logger->info("MODIFICACION TURNO:",$valores);   
        }catch(Exception $e){
            echo($e);        
        }
    }

    public function bajaTurnoSeleccionado($post){
        $consulta = "DELETE FROM turnos WHERE id = ?;";
        echo("id turno: ".$post['baja_turno']."<br>");
        // echo("<pre>");
        // var_dump($post);
        // exit();
        try{
            $sql = $this->db->prepare($consulta);
            // $sql->bindColumn(':id',$id_turno);
            if($sql->execute([$post['baja_turno']]) === TRUE){
                echo("registro ha sido eliminado, id=>{$post['baja_turno']}");

                $this->logger->info("BAJA TURNO:", $post);   

                // $this->logger->guardarAccion('b',$consulta);   
            }else{
                echo("No se pudo eliminar el registro<br>");
            }    
        }catch(Exception $e){
            echo($e);
        }
    }


    public function setTurno($fecha_turno, $hora_turno){
        self::setNames();
        $sql = "INSERT INTO dbturnos(fecha_turno,hora_turno) VALUES ('".$fecha_turno."','".$hora_turno."')";
        $result = $this->db->query($sql);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
}


?>