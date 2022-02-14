<?php
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/
class Ruta_Controlador{

    static public function fecha_controlador(){
        $respuesta = Ruta_Modelo::fecha_modelo();
        return $respuesta;
    }
    
    static public function raiz_controlador(){
        $respuesta = Ruta_Modelo::raiz_modelo();
        return $respuesta;
    }

    static public function ruta_actual_controlador(){
        $url = array();
        if(isset($_GET["url"])) {
            $url = explode("/", $_GET["url"]);
            $enlace = $url[0];
        }
        else {
            $enlace = "index";
        }
        $respuesta = Ruta_Modelo::ruta_actual_modelo($enlace);
        include $respuesta;
    }
}
