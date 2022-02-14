<?php
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/
class Core_Controlador{
    static public function administrar_ingreso_controlador(){
        if(isset($_POST["ingreso_correo"]) && isset($_POST["ingreso_contrasena"])) {
            $encriptar = crypt(htmlspecialchars($_POST["ingreso_contrasena"]), '$2a$07$GSVs6pSNqiKLkHE6dOtZPejPtcf/bRSl2n2WvmNE2yIZAEW7t9J.a');
            $datos = array("correo"=>htmlspecialchars(strtolower($_POST["ingreso_correo"])), "contrasena"=>$encriptar);
            if(isset($_POST["tipo_ingreso"]) && $_POST["tipo_ingreso"] == "cliente") {
                $respuesta = Core_Modelo::administrar_ingreso_cliente_modelo("cliente", $datos);
                if($respuesta != false && $_POST["ingreso_contrasena"] != 'UsuarioSistema'){
                    date_default_timezone_set("America/Bogota");
                    $fecha = date("Y-m-d H:i:s");
                    $datos_registro_ingreso = array("id_usuario"=>$respuesta["id"], "fecha"=>$fecha);
                    $ingreso = Core_Modelo::administrar_registro_ingreso_modelo("cliente", $datos_registro_ingreso);
                    $_SESSION["id_cliente"] = $respuesta["id"];
                    $_SESSION["validar_sesion"] = "ok";
                    setcookie ("usuario", $respuesta["id"]);
                    setcookie ("raiz", $GLOBALS["raiz"]);
                    setcookie ("tipo_usuario", "cliente");
                    echo '<script type="text/javascript">
                    var pagina = "crear-solicitud";
                    var segundos = 0;
                    function redireccion() {
                        document.location.href=pagina;
                    }
                    setTimeout("redireccion()",segundos);
                    </script>';
                }else if($respuesta != false && $encriptar == '$2a$07$GSVs6pSNqiKLkHE6dOtZPelYlIQQPV/f2H6on4TRJk3qk4W6fxuS2'){
                    date_default_timezone_set("America/Bogota");
                    $fecha = date("Y-m-d H:i:s");
                    $datos_registro_ingreso = array("id_usuario"=>$respuesta["id"], "fecha"=>$fecha);
                    $ingreso = Core_Modelo::administrar_registro_ingreso_modelo("cliente", $datos_registro_ingreso);
                    $_SESSION["id_cliente"] = $respuesta["id"];
                    $_SESSION["validar_sesion"] = "pendiente";
                    echo '<script type="text/javascript">
                    var pagina = "cambio-contrasena-cliente";
                    var segundos = 0;
                    function redireccion() {
                        document.location.href=pagina;
                    }
                    setTimeout("redireccion()",segundos);
                    </script>';
                }
                else if(isset($_SESSION["id_cliente"]) && isset($_SESSION["validar_sesion"])){
                    echo '<script type="text/javascript">
                            var pagina = "crear-solicitud";
                            var segundos = 0;
                            function redireccion() {
                                document.location.href=pagina;
                            }
                            setTimeout("redireccion()",segundos);
                        </script>';
                }else {
                    echo '<div class="alert alert-danger text-center">Error al ingresar, verifique sus datos.</div>';
                }
            }else {
                $respuesta = Core_Modelo::administrar_ingreso_agente_modelo("usuario", $datos);
                if($respuesta != false && $_POST["ingreso_contrasena"] != 'UsuarioSistema'){
                    date_default_timezone_set("America/Bogota");
                    $fecha = date("Y-m-d H:i:s");
                    $datos_registro_ingreso = array("id_usuario"=>$respuesta["id"], "fecha"=>$fecha);
                    $ingreso = Core_Modelo::administrar_registro_ingreso_modelo("usuario", $datos_registro_ingreso);
                    $_SESSION["id"] = $respuesta["id"];
                    $_SESSION["rol"] = $respuesta["rol"];
                    $_SESSION["validar_sesion"] = "ok";
                    $_SESSION["cargo"] = $respuesta["cargo"];
                    setcookie ("cargo", $respuesta["cargo"]);
                    setcookie ("usuario", $respuesta["id"]);
                    setcookie ("raiz", $GLOBALS["raiz"]);
                    setcookie ("rol", $_SESSION["rol"]);
                    setcookie ("tipo_usuario", "funcionario");
                    echo '<script type="text/javascript">
                    var pagina = "inicio";
                    var segundos = 0;
                    function redireccion() {
                        document.location.href=pagina;
                    }
                    setTimeout("redireccion()",segundos);
                    </script>';
                }else if($respuesta != false && $encriptar == '$2a$07$GSVs6pSNqiKLkHE6dOtZPelYlIQQPV/f2H6on4TRJk3qk4W6fxuS2'){
                    date_default_timezone_set("America/Bogota");
                    $fecha = date("Y-m-d H:i:s");
                    $datos_registro_ingreso = array("id_usuario"=>$respuesta["id"], "fecha"=>$fecha);
                    $ingreso = Core_Modelo::administrar_registro_ingreso_modelo("usuario", $datos_registro_ingreso);
                    $_SESSION["id"] = $respuesta["id"];
                    $_SESSION["validar_sesion"] = "pendiente";
                    echo '<script type="text/javascript">
                    var pagina = "cambio-contrasena";
                    var segundos = 0;
                    function redireccion() {
                        document.location.href=pagina;
                    }
                    setTimeout("redireccion()",segundos);
                    </script>';
                }
                else if(isset($_SESSION["id"]) && isset($_SESSION["validar_sesion"])){
                    echo '<script type="text/javascript">
                            var pagina = "inicio";
                            var segundos = 0;
                            function redireccion() {
                                document.location.href=pagina;
                            }
                            setTimeout("redireccion()",segundos);
                        </script>';
                }else {
                    echo '<div class="alert alert-danger text-center">Error al ingresar, verifique sus datos.</div>';
                }
            }
        }
    }

    static public function mostrar_perfil_controlador(){
        if(isset($_SESSION["id_cliente"])){
            $respuesta = Core_Modelo::perfil_cliente_modelo("cliente", $_SESSION["id_cliente"]);
            $enlace_editar_perfil = "detalle-perfil-cliente";
        }else {
            $respuesta = Core_Modelo::perfil_agente_modelo("usuario", $_SESSION["id"]);
            $enlace_editar_perfil = "detalle-perfil-usuario";
        }
        echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="'.$GLOBALS['raiz'].'vista/img/avatar/'.$respuesta["avatar"].'" class="user-image" alt="User Image">
            <span class="hidden-xs">'.$respuesta["nombres"].'</span>
        </a>
        <ul class="dropdown-menu">
            <li class="user-header">
                <img src="'.$GLOBALS['raiz'].'vista/img/avatar/'.$respuesta["avatar"].'" class="img-circle" alt="User Image">
                <p>
                    '.$respuesta["nombres"].'
                    <small>'.$respuesta["rol"].'</small>

            </li>
            <li class="user-footer">
                <div class="pull-left">
                    <a href="'.$enlace_editar_perfil.'" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="pull-right">
                    <a href="salir" class="btn btn-default btn-flat">Salir</a>
                </div>
            </li>
        </ul>';
    }
}
