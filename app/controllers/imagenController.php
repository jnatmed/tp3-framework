<?php

namespace App\Controllers;

class imagenController
{
    public $extensionImagen;
    public $tamanioImagen;
    public $nombreImagen;
    public $imagenCodificada;
    public $tiposPermitidos = ['jpg','png','jpeg'];
    public $extension;
    public $archivoImagen;
    public $maximo_tamanio_imagen_valido;
    const MAXIMO_TAMANIO_IMAGEN = 10;

    public function __construct($paramsImagen = NULL)
    {
        if (isset($paramsImagen)){
            $this->extensionImagen = $paramsImagen['extension'];
            $this->tamanioImagen = $paramsImagen['tamanio'];
            $this->archivoImagen = $paramsImagen['archivo'];
        }else{
            $this->extensionImagen = "";
            $this->tamanioImagen = 0;
            $this->nombreImagen = "";
        }
        $this->setMaximo_tamanio_imagen_valido(floatval(self::MAXIMO_TAMANIO_IMAGEN));
    }

    public function setArchivoImagen($archivo){
        $this->archivoImagen = $archivo;
    }

    public function getArchivoImagen(){
        return $this->archivoImagen;
    }

    public function controlTamanioMaximoImagen(){
        
        if (($this->getTamanioEnMB()) <= $this->getMaximo_tamanio_imagen_valido()) {
            return true;
        }else{
            return false;
        } 
    }

public function getMaximo_tamanio_imagen_valido(){
        return $this->maximo_tamanio_imagen_valido;
    }

    public function setMaximo_tamanio_imagen_valido($val){
        $this->maximo_tamanio_imagen_valido = $val;
    }
    public function codificar(){
        $fp = fopen($this->nombreImagen,"rb");
        $this->imagenCodificada = fread($fp, filesize($this->nombreImagen));
        $this->imagenCodificada = base64_encode($this->imagenCodificada);
        fclose($fp);
    }
    
    public function decodificar(){
        return base64_decode($this->getImagenCodificada());
    }

    public function setTipoImagen($tipoImg){
        $this->extensionImagen = $tipoImg;
    }
    public function getTipoImagen(){
        return $this->extensionImagen;
    }

    public function devolverPathImagen($imgBase64){
        $decoded = base64_decode($imgBase64);
        $this->setPathFile('img/tmp.'.$this->getExtension());
        file_put_contents($this->getPathFile(),$decoded);
        $this->setTamanioImagen(filesize($this->getPathFile()));
    }

    public function cargarImagen(){
        if ($this->imagenCargada()){    
            return $this->getPathFile();
        }else{
            return "img/contenido-no-disponible.jpg";
        }

    }

    public function setExtension($ext){
        $this->extension = $ext;
    }
    public function getExtension(){
        return $this->extension;
    }
    
    public function setPathFile($pfile){
        $this->pathFile = $pfile;
    }

    public function getPathFile(){
        return $this->pathFile;
    }

    public function getTamanioEnMB(){
        return ($this->getTamanioImagen() / 1048576);
    }

    public function getImagenCodificada(){
        return $this->imagenCodificada;
    }

    public function imagenCargada(){
        return ($this->getTamanioImagen() <> 0);
    }

    public function setTamanioImagen($tamanio){
        $this->tamanioImagen = $tamanio;
    }
    public function getTamanioImagen(){
        return $this->tamanioImagen;
    }
    public function controlTipoImagenValida()
    {
        $encontrado = false;
        foreach ($this->tiposPermitidos as $extension){
            if (strpos($this->getTipoImagen(),$extension)!==false){
                $this->setExtension($extension);
                $encontrado = true;
            }
        }
        if ($encontrado) {return true;} else {return false;}
    }

}

?>