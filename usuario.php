<?php
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Usuario_Controlador{
    static public function mostrar_formulario_con_excepcion_controlador($datos) {
        if($datos["excepcion"] == 0){
            echo '<option value="0"> Escoge una opción - El funcionario registra esta opción </option>'.$datos["excepcion"];
        }else{
            $consulta_excepcion = Usuario_Modelo::mostrar_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
            echo '<option value="'.$consulta_excepcion["id"].'"> '.$consulta_excepcion[$datos["columna"]].'</option>';
        }
        $consulta = Usuario_Modelo::mostrar_formulario_con_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
        foreach ($consulta as $fila => $item) {
            echo '<option value="'.$item["id"].'"> '.$item[$datos["columna"]].'</option>';
        }
    }

    static public function lista_usuario_controlador(){
        $fecha = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $respuesta = Usuario_Modelo::lista_usuario_modelo("usuario");
        if($respuesta == null){
            $respuesta_json = '{"data": []}';
            echo $respuesta_json;
        }else{
            $respuesta_json = '{
            "data": [';
            for($i = 0; $i<count($respuesta); $i++){
                if($respuesta[$i]["estado"] == 'Activo'){
                    $botonEstado = "<button class='btn btn-success btn-xs cambiar-estado-usuario' estado_usuario_id='".$respuesta[$i]["id"]."' estado_usuario_modificado='".$auditoria_usuario."' estado_usuario='".$respuesta[$i]["estado"]."'>".$respuesta[$i]["estado"]."</button></td>";
                }else if($respuesta[$i]["estado"] == 'Inactivo'){
                    $botonEstado = "<button class='btn btn-danger btn-xs cambiar-estado-usuario' estado_usuario_id='".$respuesta[$i]["id"]."' estado_usuario_modificado='".$auditoria_usuario."' estado_usuario='".$respuesta[$i]["estado"]."'>".$respuesta[$i]["estado"]."</button></td>";
                }
                $editar_grupo = "<button type='button' formulario_editar_grupo_agente_id='".$respuesta[$i]["id"]."' formulario_editar_grupo_agente_auditoria_usuario='".$auditoria_usuario."' class='btn btn-primary btn-sm consultar-grupo-trabajo-agente' title='Grupos' data-toggle='modal' data-target='#modal_modificar_grupo_agente'><i class='fas fa-user-friends'></i></button>";
                $editar_datos_usuario = "<button type='button' formulario_editar_id_usuario='".$respuesta[$i]["id"]."' formulario_editar_actualizado_por='".$auditoria_usuario."' class='btn btn-success btn-sm editar-usuario' title='Editar' data-toggle='modal' data-target='#modal_editar_perfil_usuario'><i class='fa fa-edit'></i></button>";
                $eliminar = "<button type='button' formulario_eliminar_id_usuario='".$respuesta[$i]["id"]."' formulario_eliminar_actualizado_por='".$auditoria_usuario."' class='btn btn-danger btn-sm eliminar-usuario' title='Eliminar' data-toggle='modal' data-target='#modal_eliminar_usuario'><i class='fa fa-trash-o'></i></button>";
                $acciones = "<div class='btn-group'>".$editar_datos_usuario.$eliminar."</div>";
                $avatar = "<img src='".$raiz."/vista/img/avatar/".$respuesta[$i]["avatar"]."' width='50' height='50' class='img-circle' alt='Avatar'>";
                $ingreso = date_create($respuesta[$i]["ingreso"]);
                $ingreso = date_format($ingreso, "d/m/Y g:i A");
                $respuesta_json .='[
                    "'.$respuesta[$i]["nombres"].'",
                    "'.$respuesta[$i]["rol"].'",
                    "'.$ingreso.'",
                    "'.$botonEstado.'",
                    "'.$acciones.'"
                ],';
            }
        $respuesta_json = substr($respuesta_json, 0, -1);
		$respuesta_json .= '] 
        }';
        echo $respuesta_json;
        }
    }

    static public function actualizar_estado_usuario_controlador($datos){
        if ($datos["estado_usuario"] == 'Activo'){
            $nuevo_estado = 1;
        }else if($datos["estado_usuario"] == 'Inactivo'){
            $nuevo_estado = 2;
        }
        $datos = array("estado_usuario_id"=>$datos["estado_usuario_id"],
                            "estado_usuario_modificado"=>$datos["estado_usuario_modificado"],
                            "estado_usuario"=>$nuevo_estado);
        $respuesta = Usuario_Modelo::actualizar_estado_usuario_modelo("usuario", $datos);
        if ($respuesta == 'ok' && $nuevo_estado == 1){
            echo 'Usuario bloqueado, no podrá ingresar al sistema!';
        }else if($respuesta == 'ok' && $nuevo_estado == 2){
            echo 'Usuario activado, ahora podrá ingresar al sistema!';
        }
    }

    static public function formulario_rol_controlador(){
        $respuesta = Usuario_Modelo::formulario_rol_modelo("rol");
        foreach ($respuesta as $fila => $item) {
            echo '<option value="'.$item["id"].'">'.$item["rol"].'</option>';
        }
    }

    static public function formulario_actualizar_usuario_controlador($datos){
        $editar_id_usuario = $datos["formulario_editar_id_usuario"];
        $editar_actualizado_por = $datos["formulario_editar_actualizado_por"];
        $respuesta = Usuario_Modelo::perfil_usuario_modelo("usuario", $editar_id_usuario);
        echo '<div class="box-body">
                <form method="post">
                    <div class="box-body">
                        <div class="form-group has-feedback">
                            <label for="registroNombres" class="control-label">Nombres y apellidos</label>
                            <input type="hidden" id="editar_id_usuario" value="'.$editar_id_usuario.'">
                            <input type="hidden" id="editar_actualizado_por" value="'.$editar_actualizado_por.'">
                            <input type="text" class="form-control" name="editar_nombres" id="editar_nombres" placeholder="Nombres" value="'.$respuesta["nombres"].'" required>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="registroEmail" class="control-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="editar_email" id="editar_email" placeholder="Correo electrónico" value="'.$respuesta["email"].'" required>
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        </div>
                        <div class="form-group">
                            <label for="registroEstado" class="control-label">Rol</label>
                            <select class="form-control select2" style="width: 100%;" name="editar_rol" id="editar_rol" required>
                                <option value="'.$respuesta["idRol"].'">'.$respuesta["rol"].'</option>
                                <option value="">Elige una opción</option>';
                                $rol = new Usuario_Controlador();
                                $rol -> formulario_rol_controlador();
                            echo '</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="registroEmail" class="control-label">Cargo</label>
                            <input type="email" class="form-control" id="" placeholder="Cargo" disabled value="'.$respuesta["cargo"].'" required>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="registroEmail" class="control-label">ID Usuario</label>
                            <input type="email" class="form-control" id="" placeholder="Cargo" disabled value="'.$editar_id_usuario.'" required>
                            <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Grupos de trabajo</label><br>';
                            $consulta_grupo = Usuario_Modelo::consultar_grupo_usuario_modelo("usuario_grupo_trabajo", $editar_id_usuario);
                            if(count($consulta_grupo) == 0){
                                echo '<a disabled class="btn btn-warning btn-xs">Este agente no esta en ningun grupo de trabajo</a>';
                            }
                            foreach($consulta_grupo as $fila => $item){
                                echo '<a disabled class="btn btn-warning btn-xs">'.$item["grupo_trabajo"].'</a> ';
                            }
                   echo'</div>
                   </div>
                </form>
            </div>';
    }

    static public function actualizar_usuario_controlador($datos){
        $respuesta = Usuario_Modelo::actualizar_usuario_modelo("usuario", $datos);
        return $respuesta;
    }

    static public function formulario_eliminar_usuario_controlador($datos){
        $eliminar_id_usuario = $datos["formulario_eliminar_id_usuario"];
        $eliminar_actualizado_por = $datos["formulario_eliminar_actualizado_por"];
        echo '<div class="alert alert-danger text-center">
                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                <h5>Al eliminar este usuario ya no podrá acceder al sistema !</h5>
                <h6>Para recuperarlo deberá comunicarse con soporte técnico.</h6>
                <form method="post" class="form-horizontal">
                    <input type="hidden" id="eliminar_id_usuario" value="'.$eliminar_id_usuario.'">
                    <input type="hidden" id="eliminar_actualizado_por" value="'.$eliminar_actualizado_por.'">
                </form>
            </div>';
    }

    static public function eliminar_usuario_controlador($datos){
        $respuesta = Usuario_Modelo::eliminar_usuario_modelo("usuario", $datos);
        return $respuesta;
    }

    static public function nueva_contrasena_usuario_controlador($datos){
        if(isset($datos["id_usuario_nueva_contrasena"]) && isset($datos["nueva_contrasena"])) {
            date_default_timezone_set("America/Bogota");
            $fecha = date("Y-m-d H:i:s");
            $encriptar = crypt($datos["nueva_contrasena"], '$2a$07$GSVs6pSNqiKLkHE6dOtZPejPtcf/bRSl2n2WvmNE2yIZAEW7t9J.a');
            $datos = array("id_usuario"=>$_POST["id_usuario_nueva_contrasena"], "contrasena"=>$encriptar, "actualizado"=>$fecha);
            $respuesta = Usuario_Modelo::nueva_contrasena_usuario_modelo("usuario", $datos);
            return $respuesta;
        }
    }

    static public function formulario_solicitar_contrasena_controlador($datos){
        $respuesta = Usuario_Modelo::formulario_solicitar_contrasena_modelo("usuario", $datos);
        if($respuesta == NULL){
            echo 'Usuario no registrado!';
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_correo"] && $respuesta["estado"] == 1){
            echo 'El usuario existe y esta inactivo, comunicate con el administrador del sistema!';
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_correo"] && $respuesta["estado"] == 3){
            echo 'El usuario existe y esta borrado, comunicate con el administrador del sistema!';
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_correo"] && $respuesta["estado"] == 2 && $respuesta["contrasena_intento"] <= 5){
            $intentos = $respuesta["contrasena_intento"]+1;
            $enlace = $datos["formulario_recuperar_contrasena_correo"].$datos["formulario_recuperar_contrasena_fecha"];
            $llave = md5($enlace);
            $datosIntentos = array('email' => strtolower($datos["formulario_recuperar_contrasena_correo"]),
                                    'contrasena_fecha' => $datos["formulario_recuperar_contrasena_fecha"],
                                    'contrasena_intento' => $intentos,
                                    'contrasena_llave' => $llave);
            $solicitar = Usuario_Modelo::formulario_intentos_contrasena_modelo("usuario", $datosIntentos);
            if ($solicitar == "ok"){
                $enlace = "nueva-contrasena?enlace=".$llave."";
                try {
                    require_once '../librerias/PHPmailer/Exception.php';
                    require_once '../librerias/PHPmailer/PHPMailer.php';
                    require_once '../librerias/PHPmailer/SMTP.php';
                    $mail = new PHPMailer(true);
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'mesadeservicio@croydon.com.co';
                    $mail->Password   = 'Cr0yd0n*2021.';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom('mesadeservicio@croydon.com.co', 'Mesa de ayuda - Croydon');
                    $mail->addAddress($respuesta["email"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Restablece tu contraseña | Portal Usuarios Croydon';
                    $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                    <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                    <center>
                                        <hr style="border:1px solid #005574; width:80%">
                                        <h2 style="font-weight:700">¿Olvidaste tu contraseña?</h2>
                                        <h4 style="font-weight:100; padding:0 20px">Hola '.$respuesta["nombres"].',<br>No te preocupes, estamos para ayudarte.<br>Haz clic en el botón para cambiar tu contraseña, recuerda que esta solicitud es válida solo durante 40 minutos.</h4>
                                        <a href="http://mesadeservicio.croydon.com.co/'.$enlace.'" target="_blank" style="text-decoration:none"><div style="line-height:60px; background:#005574; width:60%; color:white">Restablecer contraseña</div></a>
                                        <h4 style="font-weight:100; padding:0 20px">Si el botón no funciona, copia y pega el enlace en tu navegador: http://mesadeservicio.croydon.com.co/'.$enlace.'</h4>
                                        <h4 style="font-weight:100; padding:0 20px">¡Te esperamos!</h4>
                                        <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                    </center>
                                    </div>
                                </div>';
                    $mail->send();
                } catch (Exception $e) {
                    echo 'Ha ocurrido un error inesperado!';
                }
                echo 'Hemos enviado un mensaje a tu correo para restablecer tu contraseña, este mensaje será valido por 40 minutos!';
            }
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_correo"] && $respuesta["estado"] == 2 && $respuesta["contrasena_intento"] <= 6){
            $desactivar = Usuario_Modelo::desactivar_usuario_modelo("usuario", $datos["formulario_recuperar_contrasena_correo"]);
            if ($desactivar == "ok"){
                echo 'El usuario ha sido desactivo, por superar el numero de intentos permitidos!';
                try {
                    require_once '../librerias/PHPmailer/Exception.php';
                    require_once '../librerias/PHPmailer/PHPMailer.php';
                    require_once '../librerias/PHPmailer/SMTP.php';
                    $mail = new PHPMailer(true);
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'mesadeservicio@croydon.com.co';
                    $mail->Password   = 'Cr0yd0n*2021.';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom('mesadeservicio@croydon.com.co', 'Mesa de ayuda - Croydon');
                    $mail->addAddress($respuesta["correo"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Restablece tu contraseña | Portal Usuarios Croydon';
                    $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                    <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                    <center>
                                        <hr style="border:1px solid #005574; width:80%">
                                        <h2 style="font-weight:700">¿Olvidaste tu contraseña?</h2>
                                        <h4 style="font-weight:100; padding:0 20px">Hola '.$respuesta["nombres"].',<br>Tu usuario ha sido desactivo por superar el numero de intentos permitidos para recuperar contraseña, comunicate con el administrador del sistema, Gracias.</h4>
                                        <h4 style="font-weight:100; padding:0 20px">¡Si no solicitaste tu contraseña, por favor comunicalo al administrador, Gracias.!</h4>
                                        <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                    </center>
                                    </div>
                                </div>';
                    $mail->send();
                } catch (Exception $e) {
                    echo 'Ha ocurrido un error inesperado!';
                }
            }
        }
    }

    static public function actualizar_contrasena_controlador($datos){
        $respuesta = Usuario_Modelo::formulario_cambio_contrasena_modelo("usuario", $datos["formulario_llave_contrasena"]);
        if ($respuesta == NULL){
            echo 'El enlace no es valido!';
        }else{
            $segundos =  strtotime($datos["formulario_nueva_contrasena_fecha"]) - strtotime($respuesta["contrasena_fecha"]);
            $tiempo = abs(floor($segundos/60));
            if ($tiempo > 40){
                echo 'El enlace ha caducado!';
            }else{
                $encriptar = crypt($datos["formulario_nueva_contrasena"], '$2a$07$GSVs6pSNqiKLkHE6dOtZPejPtcf/bRSl2n2WvmNE2yIZAEW7t9J.a');
                $actualizar = Usuario_Modelo::actualizar_contrasena_modelo("usuario", $datos, $encriptar);
                    if ($actualizar == "ok"){
                        echo 'Contraseña actualizada correctamente!';
                    }
            }
        }
    }

    static public function perfil_usuario_controlador(){
        $respuesta = Usuario_Modelo::perfil_usuario_modelo("usuario", $_SESSION["id"]);
        echo '<div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Carnet digital</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="'.$GLOBALS['raiz'].'vista/img/avatar/'.$respuesta["avatar"].'" alt="User profile picture">
                    <h3 class="profile-username text-center">'.$respuesta["nombres"].'</h3>
                    <p class="text-muted text-center">'.$respuesta["cargo"].'</p>
                    <div class="d-flex">
                        <div class="text-aling-center">
                            <img class="img-responsive" src="https://barcode.tec-it.com/barcode.ashx?data='.$respuesta["documento"].'&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&dataattributekey_2=&dataattributeval_2=&dataattributekey_3=&dataattributeval_3=&dataattributekey_4=&dataattributeval_4=&dataattributekey_5=&dataattributeval_5=&digitallink=&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0&hidehrt=False&eclevel=M&dmsize=Default"/>
                        </div>
                    </div>
                    <a href="#" class="btn btn-warning btn-block imagen-pefil" data-toggle="modal" data-target="#modal_perfil_cambiar_avatar"><b>Cambiar imagen de perfil</b></a>
                </div>
            </div>
        </div>';
    }

    static public function editar_usuario_controlador(){
        $respuesta = Usuario_Modelo::perfil_usuario_modelo("usuario", $_SESSION["id"]);
        echo '<form method="POST">
            <div class="form-group has-feedback">
                <label class= "control-label">ID Usuario</label>
                <input type="email" class="form-control" disabled value="'.$_SESSION["id"].'" required>
                <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class="control-label">Nombres y apellidos</label>
                <input type="hidden" id="perfil_actualizado_por" value="'.$_SESSION["id"].'">
                <input type="text" class="form-control" disabled id="perfil_editar_usuario_nombres" name="perfil_editar_usuario_nombres" value="'.$respuesta["nombres"].'" required>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Documento</label>
                <input type="email" class="form-control" disabled value="'.$respuesta["documento"].'" required>
                <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Correo electrónico</label>
                <input type="email" class="form-control" id="perfil_editar_usuario_email" name="perfil_editar_usuario_email" value="'.$respuesta["email"].'" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Área</label>
                <input type="email" class="form-control" disabled value="'.$respuesta["area"].'" required>
                <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Empresa</label>
                <input type="email" class="form-control" disabled value="'.$respuesta["empresa"].'" required>
                <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Cargo</label>
                <input type="email" class="form-control" disabled value="'.$respuesta["cargo"].'" required>
                <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
            </div>';
            if ($respuesta["usuario_jd"] != "" || $respuesta["usuario_jd"] != NULL) {
                echo '<div class="form-group has-feedback">
                        <label class= "control-label">Usuario de JDE</label>
                        <input type="email" class="form-control" disabled value="'.$respuesta["usuario_jd"].'" required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>';
            }
        echo'<div class="form-group has-feedback">
                <label class= "control-label">Teléfono</label>
                <input type="email" class="form-control" disabled value="'.$respuesta["telefono"].'" required>
                <span class="glyphicon glyphicon-earphone form-control-feedback"></span>
            </div>
        </form>';
    }

    static public function perfil_actualizar_usuario_controlador($datos){
        $respuesta = Usuario_Modelo::perfil_actualizar_usuario_modelo("usuario", $datos);
        return $respuesta;
    }

    static public function perfil_editar_avatar_usuario_controlador($datos){
        if(isset($datos["perfil_editar_avatar_usuario"]["tmp_name"])){
            if ($datos["perfil_editar_avatar_usuario"]["type"] == "image/jpeg"){
                $avatarBase = $datos["perfil_editar_avatar_id_usuario"].".jpg";
            }
            if ($datos["perfil_editar_avatar_usuario"]["type"] == "image/png"){
                $avatarBase = $datos["perfil_editar_avatar_id_usuario"].".png";
            }
            move_uploaded_file($_FILES["perfil_editar_avatar_usuario"]["tmp_name"],"../img/avatar/".$avatarBase);
        }
        $respuesta = Usuario_Modelo::perfil_editar_avatar_usuario_modelo("usuario", $datos, $avatarBase);
        return $respuesta;
    }
}
