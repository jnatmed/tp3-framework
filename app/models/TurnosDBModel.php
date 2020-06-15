<?php
namespace App\Models;

require 'vendor/autoload.php';

use PDO;
use \App\Controllers\imagenController;

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

        $this->dsn = sprintf("%s;dbname=%s", $this->params['database']['connection'], $this->params['database']['name']);
        // echo ("<pre>");
        // var_dump($this->dsn);
        // exit(0);
        try {
            $this->db = new PDO($this->dsn, $this->params['database']['username'],$this->params['database']['password']);    
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
        $sql = "SELECT * FROM turnos WHERE `id` =:id";
        $array_consulta = [':id' => $id_turno];
        try{
            $result = $this->db->prepare($sql);
            $result->execute($array_consulta);
            $result->setFetchMode(PDO::FETCH_ASSOC);
            foreach ($result as $res){
                $turno[] = $res;
            }
            // echo("<pre>");
            // echo("getTurnoSeleccionado<br>");
            if(isset($turno)){
                return $turno;
                $this->db = NULL;    
            }else{
                return array();
            };
        }catch(Exception $e){
            echo($e);
        }catch(PDOException $Exception){
            throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
        } //catch( PDOException $Exception ) {
            // exit();
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
                                                         :fecha_turno,
                                                         :hora_turno,
                                                         :nombre_paciente,
                                                         :email,
                                                         :telefono,
                                                         :fecha_nacimiento,
                                                         :edad,
                                                         :talla_calzado,
                                                         :altura,
                                                         :color_pelo,
                                                         :archivo_imagen,
                                                         :tipo_imagen)";    

        $array_consulta = [
            ':fecha_turno' => $valores['fecha_turno'],
            ':hora_turno' => $valores['hora_turno'],
            ':nombre_paciente' => $valores['nombre_paciente'],
            ':email' => $valores['email'],
            ':telefono' => $valores['telefono'],
            ':fecha_nacimiento' => $valores['fecha_nacimiento'],
            ':edad' => $valores['edad'],
            ':talla_calzado' => $valores['talla_calzado'],
            ':altura' => $valores['altura'],
            ':color_pelo' => $valores['color_pelo'],
            ':archivo_imagen' => base64_decode($valores['archivo_imagen']),
            ':tipo_imagen' => $valores['tipo_imagen']   
        ];    
        try{
            // $this->motrarMsj($consulta);
            // echo($consulta);
            $sql = $this->db->prepare($consulta);
            $sql->execute($array_consulta);    
            // $this->logger->info();   
            unset($valores['archivo_imagen']);
            unset($valores['tipo_imagen']);
            unset($valores['enviar']);
            $this->logger->info("ALTA TURNO: ", $valores);
        }catch(Exception $e){
            echo($e);        
        }
    }
    public function actualizarTurno($valores,$img_receta){
        // echo("<pre>");
        // echo("insertarTurno<br>");
        // var_dump($img_receta);
        // // var_dump($valores);
        // echo($img_receta['imagen_receta']['size']);
        // exit(); 
        // $this->imgController = new imagenController($img_receta);
        // if($this->imgController->imagenCargada()){
        //     $this->imgController->codificar();
        // }
        $consulta = "UPDATE `turnos` SET 
                            `fecha_turno`=:fecha_turno,
                            `hora_turno`=:hora_turno,
                            `nombre_paciente`=:nombre_paciente,
                            `email`=:email,
                            `telefono`=:telefono,
                            `fecha_nacimiento`=:fecha_nacimiento,
                            `edad`=:edad,
                            `talla_calzado`=:talla_calzado,
                            `altura`=:altura,
                            `color_pelo`=:color_pelo, 
                            `imagen`=:archivo_imagen,
                            `tipo_imagen`=:tipo_imagen WHERE `id` = :id";

        if($img_receta['imagen_receta']['size']<>0){
            $archivo_imagen = file_get_contents($img_receta['imagen_receta']['tmp_name']);
            $tipo_imagen = pathinfo($img_receta['imagen_receta']['name'],PATHINFO_EXTENSION);
            // echo("HAY NUEVA IMAGEN");
        }else{
            if(array_key_exists('archivo_imagen',$valores)){
                $archivo_imagen = base64_decode($valores['archivo_imagen']);
                $tipo_imagen = $valores['tipo_imagen'];
                // echo("SI HAY IMAGEN GUARDADA");
            }else{
                $archivo_imagen = NULL;
                $tipo_imagen = NULL;
                // echo("NO HAY IMAGEN GUARDADA");
            }
        }                            

        // echo("<pre>");
        // var_dump($archivo_imagen);
        // var_dump($tipo_imagen);
        // exit();

        $array_consulta = [
            ':id' => $valores['id'],
            ':fecha_turno' => $valores['fecha_turno'],
            ':hora_turno' => $valores['hora_turno'],
            ':nombre_paciente' => $valores['nombre_paciente'],
            ':email' => $valores['email'],
            ':telefono' => $valores['telefono'],
            ':fecha_nacimiento' => $valores['fecha_nacimiento'],
            ':edad' => $valores['edad'],
            ':talla_calzado' => $valores['talla_calzado'],
            ':altura' => $valores['altura'],
            ':color_pelo' => $valores['color_pelo'],
            ':archivo_imagen' => $archivo_imagen,
            ':tipo_imagen' => $tipo_imagen   
        ]; 
        try{
            // $this->motrarMsj($consulta);
            // $p = explode("'imagen'",$consulta);
            // echo($p[0]);
            $sql = $this->db->prepare($consulta);
            // $sql->execute($valores);    
            $sql->execute($array_consulta);   
             
            if(array_key_exists('archivo_imagen',$valores)){
                unset($valores['archivo_imagen']);
                unset($valores['tipo_imagen']);
            }
            unset($valores['corregir_turno']);
            $this->logger->info("MODIFICACION TURNO:",$valores);   
        }catch(Exception $e){
            echo($e);        
        }
    }

    public function bajaTurnoSeleccionado($post){
        $consulta = "DELETE FROM turnos WHERE id = ?;";
        // echo("id turno: ".$post['baja_turno']."<br>");
        // echo("<pre>");
        // var_dump($post);
        // exit();
        try{
            $sql = $this->db->prepare($consulta);
            // $sql->bindColumn(':id',$id_turno);
            if($sql->execute([$post['baja_turno']]) === TRUE){
                // echo("registro ha sido eliminado, id=>{$post['baja_turno']}");

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