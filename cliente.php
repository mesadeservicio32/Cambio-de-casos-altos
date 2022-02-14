<?php
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Cliente_Controlador{
    static public function nueva_contrasena_cliente_controlador($datos){
        if(isset($datos["id_cliente_nueva_contrasena"]) && isset($datos["nueva_contrasena_cliente"])) {
            date_default_timezone_set("America/Bogota");
            $fecha = date("Y-m-d H:i:s");
            $encriptar = crypt($datos["nueva_contrasena_cliente"], '$2a$07$GSVs6pSNqiKLkHE6dOtZPejPtcf/bRSl2n2WvmNE2yIZAEW7t9J.a');
            $datos = array("id_cliente"=>$_POST["id_cliente_nueva_contrasena"], "contrasena"=>$encriptar, "actualizado"=>$fecha);
            $respuesta = Cliente_Modelo::nueva_contrasena_cliente_modelo("cliente", $datos);
            return $respuesta;
        }
    }

    static public function perfil_editar_avatar_cliente_controlador($datos){
        if(isset($datos["perfil_editar_avatar_imagen"]["tmp_name"])){
            if ($datos["perfil_editar_avatar_imagen"]["type"] == "image/jpeg"){
                $avatarBase = $datos["perfil_editar_avatar_auditoria_usuario"].".jpg";
            }
            if ($datos["perfil_editar_avatar_imagen"]["type"] == "image/png"){
                $avatarBase = $datos["perfil_editar_avatar_auditoria_usuario"].".png";
            }
            move_uploaded_file($_FILES["perfil_editar_avatar_imagen"]["tmp_name"],"../img/avatar/".$avatarBase);
        }
        $respuesta = Cliente_Modelo::perfil_editar_avatar_modelo("cliente", $datos, $avatarBase);
        return $respuesta;
    }

    static public function perfil_cliente_controlador(){
        $respuesta = Cliente_Modelo::perfil_cliente_modelo("cliente", $_SESSION["id_cliente"]);
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
                    <a href="#" class="btn btn-warning btn-block imagen-pefil" data-toggle="modal" data-target="#modal_perfil_cliente_cambiar_avatar"><b>Cambiar imagen de perfil</b></a>
                </div>
            </div>
        </div>';
    }

    static public function editar_perfil_cliente_controlador(){
        $respuesta = Cliente_Modelo::perfil_cliente_modelo("cliente", $_SESSION["id_cliente"]);
        echo '<form method="POST">
            <div class="form-group has-feedback">
                <label class= "control-label">ID Usuario</label>
                <input type="email" class="form-control" disabled id="perfil_editar_an8" value="'.$_SESSION["id_cliente"].'" required>
                <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class="control-label">Apellidos y Nombres</label>
                <input type="hidden" id="perfil_editar_cliente_auditoria_usuario" value="'.$_SESSION["id_cliente"].';?>">
                <input type="text" class="form-control" disabled id="perfil_editar_cliente_nombres" value="'.$respuesta["nombres"].'" required>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Número de documento</label>
                <input type="email" class="form-control" disabled id="perfil_editar_documento" value="'.$respuesta["documento"].'" required>
                <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Área</label>
                <input type="email" class="form-control" disabled id="perfil_editar_documento" value="'.$respuesta["area"].'" required>
                <span class="glyphicon glyphicon-globe form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Cargo</label>
                <input type="email" class="form-control" disabled id="perfil_editar_documento" value="'.$respuesta["cargo"].'" required>
                <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Empresa</label>
                <input type="email" class="form-control" disabled id="perfil_editar_documento" value="'.$respuesta["empresa"].'" required>
                <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Correo electrónico</label>
                <input type="email" class="form-control" disabled id="perfil_editar_cliente_correo" value="'.$respuesta["correo"].'" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label class= "control-label">Telefono</label>
                <input type="email" class="form-control" disabled id="perfil_editar_cliente_telefono" value="'.$respuesta["telefono"].'" required>
                <span class="glyphicon glyphicon-earphone form-control-feedback"></span>
            </div>';
            if($respuesta["usuario_jd"] != " "){
                echo '<div class="form-group has-feedback">
                            <label class= "control-label">Usuario JDE</label>
                            <input type="email" class="form-control" disabled id="perfil_editar_cliente_usuario_jd" value="'.$respuesta["usuario_jd"].'" required>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                    </form>';
            }
    }

    static public function formulario_con_excepcion_controlador($datos){
        $respuesta = Cliente_Modelo::formulario_con_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
        $excepcion = Cliente_Modelo::formulario_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
        echo '<option value="'.$excepcion["id"].'">'.$excepcion[$datos["columna"]].'</option>';
        foreach ($respuesta as $fila => $item) {
            echo '<option value="'.$item["id"].'">'.$item[$datos["columna"]].'</option>';
        }
    }


    static public function area_cliente_controlador(){
        $respuesta = Cliente_Modelo::area_cliente_modelo("cliente_area");
        foreach ($respuesta as $fila => $item) {
            echo '<option value="'.$item["id"].'">'.$item["area"].'</option>';
        }
    }

    static public function gestion_cliente_controlador(){
        $respuesta = Cliente_Modelo::lista_buscar_cliente_modelo("cliente");
        date_default_timezone_set("America/Bogota");
		$fecha = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"];
        $rol = $_COOKIE["rol"];
		$raiz = $_COOKIE["raiz"];
        $respuesta_json = '{
            "data": [';
            for($i = 0; $i<count($respuesta); $i++){
                if($respuesta[$i]["estado"] == 'Activo'){
                    $boton_estado = "<button class='btn btn-success btn-xs disabled' estado_id_cliente='".$respuesta[$i]["id"]."' estado_cliente_modificado='".$auditoria_usuario."' estado_cliente='".$respuesta[$i]["estado"]."'>".$respuesta[$i]["estado"]."</button>";
                }else{
                    $boton_estado = "<button class='btn btn-danger btn-xs disabled' estado_id_cliente='".$respuesta[$i]["id"]."' estado_cliente_modificado='".$auditoria_usuario."' estado_cliente='".$respuesta[$i]["estado"]."'>".$respuesta[$i]["estado"]."</button>";
                }
                $editar = "<button type='button' formulario_editar_id_cliente='".$respuesta[$i]["id"]."' formulario_editar_cliente_modificado='".$auditoria_usuario."' class='btn btn-success btn-sm editar-cliente' title='Editar' data-toggle='modal' data-target='#modal_editar_cliente'><i class='fa fa-edit'></i></button>";
                $eliminar = "<button type='button' formulario_eliminar_id_cliente='".$respuesta[$i]["id"]."' formulario_eliminar_cliente_modificado='".$auditoria_usuario."' class='btn btn-danger btn-sm eliminar-cliente' title='Eliminar' data-toggle='modal' data-target='#modal_eliminar_cliente'><i class='fa fa-trash-o'></i></button>";
                $noificacion = "<button type='button' formulario_notificacion_id_cliente='".$respuesta[$i]["id"]."' formulario_notificacion_cliente_auditoria_usuario='".$auditoria_usuario."' class='btn btn-warning btn-sm formulario-notificacion-cliente' title='Notificaciones' data-toggle='modal' data-target='#modal_notificacion_cliente'><i class='fas fa-bell'></i></button>";
                $acciones = "<div class='btn-group'>".$eliminar.$noificacion.$editar."</div>";
                $respuesta_json .='[
                    "'.ltrim(rtrim($respuesta[$i]["nombres"])).'",
                    "'.ltrim(rtrim($respuesta[$i]["telefono"])).'",
                    "'.ltrim(rtrim($respuesta[$i]["correo"])).'",
                    "'.ltrim(rtrim($respuesta[$i]["area"])).'",
                    "'.$boton_estado.'",
                    "'.$acciones.'"
                ],';
            }
        $respuesta_json = substr($respuesta_json, 0, -1);
		$respuesta_json .= '] 
        }';
        echo $respuesta_json;
    }

    static public function actualizar_estado_cliente_controlador($datos){
        $datos = array("estado_id_cliente"=>$datos["estado_id_cliente"],
                            "estado_cliente_modificado"=>$datos["estado_cliente_modificado"],
                            "estado_cliente"=>$nuevoEstado);
        $respuesta = Cliente_Modelo::actualizar_estado_cliente_modelo("cliente", $datos);
        if ($respuesta == 'ok' && $nuevoEstado == 1){
            echo 'Cliente bloqueado, los contactos asociados a este cliente no podrán ingresar al sistema!';
        }else if($respuesta == 'ok' && $nuevoEstado == 2){
            echo 'Cliente activado, los contactos asociados a este cliente ahora podrán ingresar al sistema!';
        }
    }

    static public function formulario_notificacion_cliente_controlador($datos){
        $consulta = Cliente_Modelo::formulario_notificacion_cliente_modelo("notificacion");
        echo '<div class="box-body">
        <form method="post">
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Notificación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-left">';
                    foreach($consulta as $fila => $item){
                        $consulta_notificacion_cliente = Cliente_Modelo::consulta_notificacion_cliente_modelo("cliente_notificacion", $datos["formulario_notificacion_id_cliente"], $item["id"]);
                        $noificacion = "<a href='#' class='btn btn-success btn-md modificar-estado-notificacion-cliente' id='modificar-estado-notificacion-cliente-".$item["id"]."' modificar_notificacion_cliente_id_cliente='".$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_notificacion='".$item["id"]."' modificar_notificacion_cliente_auditoria_usuario='".$datos["formulario_notificacion_cliente_auditoria_usuario"]."' modificar_notificacion_cliente_estado_actual='Activo' title='Desactivar'><i class='fas fa-bell'></i></a>";
                        $notificacion_estado_contrario = "<a href='#' class='hidden btn btn-danger btn-md modificar-estado-notificacion-cliente' id='modificar-estado-notificacion-cliente-".$item["id"].$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_cliente='".$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_notificacion='".$item["id"]."' modificar_notificacion_cliente_auditoria_usuario='".$datos["formulario_notificacion_cliente_auditoria_usuario"]."' modificar_notificacion_cliente_estado_actual='Desactivado' title='Activar'><i class='fas fa-bell-slash'></i></a>";
                        if($consulta_notificacion_cliente != NULL){
                            if($consulta_notificacion_cliente["estado"] == 1){
                                $noificacion = "<a href='#' class='hidden btn btn-success btn-md modificar-estado-notificacion-cliente' id='modificar-estado-notificacion-cliente-".$item["id"]."' modificar_notificacion_cliente_id_cliente='".$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_notificacion='".$item["id"]."' modificar_notificacion_cliente_auditoria_usuario='".$datos["formulario_notificacion_cliente_auditoria_usuario"]."' modificar_notificacion_cliente_estado_actual='Activo' title='Desactivar'><i class='fas fa-bell'></i></a>";
                                $notificacion_estado_contrario = "<a href='#' class='btn btn-danger btn-md modificar-estado-notificacion-cliente' id='modificar-estado-notificacion-cliente-".$item["id"].$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_cliente='".$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_notificacion='".$item["id"]."' modificar_notificacion_cliente_auditoria_usuario='".$datos["formulario_notificacion_cliente_auditoria_usuario"]."' modificar_notificacion_cliente_estado_actual='Desactivado' title='Activar'><i class='fas fa-bell-slash'></i></a>";
                            }
                        }
                        if($consulta_notificacion_cliente == NULL && $item["id"] != 1){
                            $noificacion = "<a href='#' class='hidden btn btn-success btn-md modificar-estado-notificacion-cliente' id='modificar-estado-notificacion-cliente-".$item["id"]."' modificar_notificacion_cliente_id_cliente='".$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_notificacion='".$item["id"]."' modificar_notificacion_cliente_auditoria_usuario='".$datos["formulario_notificacion_cliente_auditoria_usuario"]."' modificar_notificacion_cliente_estado_actual='Activo' title='Desactivar'><i class='fas fa-bell'></i></a>";
                            $notificacion_estado_contrario = "<a href='#' class='btn btn-danger btn-md modificar-estado-notificacion-cliente' id='modificar-estado-notificacion-cliente-".$item["id"].$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_cliente='".$datos["formulario_notificacion_id_cliente"]."' modificar_notificacion_cliente_id_notificacion='".$item["id"]."' modificar_notificacion_cliente_auditoria_usuario='".$datos["formulario_notificacion_cliente_auditoria_usuario"]."' modificar_notificacion_cliente_estado_actual='Desactivado' title='Activar'><i class='fas fa-bell-slash'></i></a>";
                        }
                        echo '<tr>
                                <td>'.$item["notificacion"].'</td>
                                <td>'.$noificacion.$notificacion_estado_contrario.'</td>
                            </tr>';
                    }
                echo ' </tbody>
                    </table>
                </div>
            </form>
        </div>';
    }

    static public function modificar_notificacion_cliente_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $auditoria_creado = date("Y-m-d H:i:s");
        $consulta_notificacion_cliente = Cliente_Modelo::consulta_notificacion_cliente_modelo("cliente_notificacion", $datos["modificar_notificacion_cliente_id_cliente"], $datos["modificar_notificacion_cliente_id_notificacion"]);
        if($datos["modificar_notificacion_cliente_estado_actual"] == "Activo"){
            $estado = 1;
        }else{
            $estado = 2;
        }
        $datos = array("cliente"=> $datos["modificar_notificacion_cliente_id_cliente"],
            "notificacion"=>  $datos["modificar_notificacion_cliente_id_notificacion"],
            "estado"=>  $estado,
            "auditoria_usuario"=>  $datos["modificar_notificacion_cliente_auditoria_usuario"],
            "auditoria_creado"=> $auditoria_creado);
        if($consulta_notificacion_cliente != NULL){
            $respuesta = Cliente_Modelo::modificar_notificacion_cliente_modelo("cliente_notificacion", $datos);
        }else {           
            $respuesta = Cliente_Modelo::registrar_notificacion_cliente_modelo("cliente_notificacion", $datos);
        }
        return $respuesta;
    }

    static public function formulario_editar_cliente_controlador($datos){
        $respuesta = Cliente_Modelo::formulario_editar_cliente_modelo("cliente", $datos["formulario_editar_id_cliente"]);
        echo '<form method="POST">
                <div class="form-group has-feedback"> 
                    <label class="control-label"><b>ID Solicitante</b></label> 
                    <input type="number" class="form-control" disabled value="'.$datos["formulario_editar_id_cliente"].'" required> 
                    <span class="glyphicon glyphicon-credit-card form-control-feedback"></span> 
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label">VIP</label>
                    <select class="form-control" id="editar_cliente_vip" required>
                        <option value="NO">NO</option>
                        <option value="SI">SI</option>
                    </select>
                </div>
                <div class="form-group has-feedback"> 
                    <label class="control-label"><b>Nombres y Apellidos</b></label> 
                    <input type="text" class="form-control" disabled id="editar_cliente_nombres" placeholder="Nombres" value="'.$respuesta["nombres"].'" required> 
                    <span class="glyphicon glyphicon-user form-control-feedback"></span> 
                </div>
                <div class="form-group has-feedback"> 
                    <label class="control-label"><b>Documento</b></label> 
                    <input type="number" class="form-control" disabled value="'.$respuesta["documento"].'" required> 
                    <span class="glyphicon glyphicon-credit-card form-control-feedback"></span> 
                </div>
                <div class="form-group has-feedback"> 
                    <label class="control-label"><b>Correo electronico</b></label> 
                    <input type="text" class="form-control" id="editar_cliente_correo" placeholder="Correo" value="'.$respuesta["correo"].'" required> 
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span> 
                </div>
                <div class="form-group has-feedback"> 
                    <label class="control-label"><b>Teléfono</b></label> 
                    <input type="text" class="form-control" disabled id="editar_cliente_telefono" placeholder="Teléfono" value="'.$respuesta["telefono"].'" required> 
                    <span class="glyphicon glyphicon-earphone form-control-feedback"></span> 
                </div>
                <div class="form-group has-feedback">
                    <input type="hidden" id="editar_cliente_id" value="'.$datos["formulario_editar_id_cliente"].'">
                    <input type="hidden" id="editar_cliente_apellidos" value=" ">
                    <input type="hidden" id="editar_cliente_auditoria_usuario" value="'.$datos["formulario_editar_cliente_modificado"].'">
                    <label class="control-label">Área</label>
                    <select class="form-control" disabled id="editar_cliente_area" required>';
                        $datos_formulario_con_excepcion = array("tabla"=> "cliente_area",
                        "columna"=>"area",
                        "excepcion"=>$respuesta["area"]);
                        $area = new Cliente_Controlador();
                        $area -> formulario_con_excepcion_controlador($datos_formulario_con_excepcion);
                echo '</select>
                </div>
                <div class="form-group has-feedback"> 
                    <label class="control-label"><b>Cargo</b></label> 
                    <input type="text" class="form-control" disabled value="'.$respuesta["cargo"].'" required> 
                    <span class="glyphicon glyphicon-briefcase form-control-feedback"></span> 
                </div>';
                if($respuesta["usuario_jd"] != " "){
                    echo '<div class="form-group has-feedback">
                        <label class= "control-label">Usuario JDE</label>
                        <input type="email" class="form-control" disabled id="perfil_editar_cliente_usuario_jd" value="'.$respuesta["usuario_jd"].'" required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>';
                }
        echo'</form>';
    }

    static public function editar_cliente_controlador($datos){
        $respuesta = Cliente_Modelo::editar_cliente_modelo("cliente", $datos);
        return $respuesta;
    }

    static public function formulario_eliminar_cliente_controlador($datos){
        $eliminar_id_cliente = $datos["formulario_eliminar_id_cliente"];
        $eliminar_cliente_modificado = $datos["formulario_eliminar_cliente_modificado"];
        echo '<div class="alert alert-danger text-center">
                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                <h5>Si eliminas este solicitante deberas comunicarte con el desarrollador del sistema para recuperarlo!</h5>
                <form method="post">
                    <input type="hidden" id="eliminar_id_cliente" value="'.$eliminar_id_cliente.'">
                    <input type="hidden" id="eliminar_cliente_modificado" value="'.$eliminar_cliente_modificado.'">
                </form>
            </div>';
    }

    static public function eliminar_cliente_controlador($datos){
        $estado = 3;
        $datos = array("eliminar_id_cliente"=>$datos["eliminar_id_cliente"],
                        "eliminar_cliente_modificado"=>$datos["eliminar_cliente_modificado"],
                        "eliminar_cliente_estado"=>$estado);
        $respuesta = Cliente_Modelo::eliminar_cliente_modelo("cliente", $datos);
        return $respuesta;
    }

    static public function formulario_recuperar_contrasena_cliente_controlador($datos){
        $respuesta = Cliente_Modelo::formulario_recuperar_contrasena_cliente_modelo("cliente", $datos);
        if($respuesta == NULL){
            echo 'Usuario no registrado!';
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_cliente_correo"] && $respuesta["estado"] == 1){
            echo 'El usuario existe y esta inactivo, comunicate con el administrador del sistema!';
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_cliente_correo"] && $respuesta["estado"] == 3){
            echo 'El usuario existe y esta borrado, comunicate con el administrador del sistema!';
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_cliente_correo"] && $respuesta["estado"] == 2 && $respuesta["contrasena_intento"] <= 5){
            $intentos = $respuesta["contrasena_intento"]+1;
            $enlace = $datos["formulario_recuperar_contrasena_cliente_correo"].$datos["formulario_recuperar_contrasena_cliente_fecha"];
            $llave = md5($enlace);
            $datosIntentos = array('email' => $datos["formulario_recuperar_contrasena_cliente_correo"],
                                    'contrasena_fecha' => $datos["formulario_recuperar_contrasena_cliente_fecha"],
                                    'contrasena_intento' => $intentos,
                                    'contrasena_llave' => $llave);
            $solicitar = Cliente_Modelo::formulario_intentos_cliente_contrasena_modelo("cliente", $datosIntentos);
            if ($solicitar == "ok"){
                $enlace = "nueva-contrasena-cliente?enlace=".$llave."";
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
                    $mail->Subject = 'Restablece tu contraseña | Portal Clientes Croydon';
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
        }else if($respuesta["email"] == $datos["formulario_recuperar_contrasena_cliente_correo"] && $respuesta["estado"] == 2 && $respuesta["contrasena_intento"] <= 6){
            $desactivar = Cliente_Modelo::desactivar_cliente_modelo("cliente", $datos["formulario_recuperar_contrasena_cliente_correo"]);
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
                    $mail->addAddress($respuesta["email"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Restablece tu contraseña | Portal Clientes Croydon';
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

    static public function actualizar_contrasena_cliente_controlador($datos){
        $respuesta = Cliente_Modelo::formulario_cambio_contrasena_cliente_modelo("cliente", $datos["formulario_llave_contrasena"]);
        if ($respuesta == NULL){
            echo 'El enlace no es valido!';
        }else{
            $segundos =  strtotime($datos["formulario_nueva_contrasena_cliente_fecha"]) - strtotime($respuesta["contrasena_fecha"]);
            $tiempo = abs(floor($segundos/60));
            if ($tiempo > 40){
                echo 'El enlace ha caducado!';
            }else{
                $encriptar = crypt($datos["formulario_nueva_contrasena_cliente_contrasena"], '$2a$07$GSVs6pSNqiKLkHE6dOtZPejPtcf/bRSl2n2WvmNE2yIZAEW7t9J.a');
                $actualizar = Cliente_Modelo::actualizar_contrasena_cliente_modelo("cliente", $datos, $encriptar);
                    if ($actualizar == "ok"){
                        echo 'Contraseña actualizada correctamente!';
                    }
            }
        }
    }

    static public function eliminar_tildes_controlador($cadena){
        $cadena = utf8_encode($cadena);
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );
        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );
        return $cadena;
    }

    static public function cargar_data_cliente_controlador($datos){
        $data = file_get_contents($_FILES["cargar_cliente_data_archivo"]['tmp_name']);
        $data = explode("\n", $data);
        $data = array_filter($data);
        $contador_actualizados = 0;
        $contador_insertados = 0;
        $contador_cargos_creados = 0;
        $contador_agentes_creados = 0;
        $contador_clientes_desactivados = 0;
        $contador_agentes_desactivados = 0;
        $contador_clientes_activados = 0;
        $contador_agentes_activados = 0;
        foreach($data as $fila => $item){
            $registro[] = explode(",", $item);
            $nombres = utf8_decode($registro[$fila][1]);
            $nombres = str_replace("?", "Ñ", $nombres);
            $nombres = Cliente_Controlador::eliminar_tildes_controlador($nombres);
            if(isset($registro[$fila][9])){
                $usuario_jd = ltrim(rtrim($registro[$fila][9]));
            }else{
                $usuario_jd = "";
            }
            if(isset($registro[$fila][10])){
                $correo = ltrim(rtrim($registro[$fila][10]));
                $correo_sin_tildes = Cliente_Controlador::eliminar_tildes_controlador($correo);
                $correo = $correo_sin_tildes;
            }else{
                $correo = "";
            }
            if(isset($registro[$fila][11])){
                $telefono = ltrim(rtrim($registro[$fila][11]));
            }else{
                $telefono = "";
            }
            if(isset($registro[$fila][12])){
                $supervisor = ltrim(rtrim($registro[$fila][12]));
            }else{
                $supervisor = "";
            }
            $datos_cliente = array("AN8" => $registro[$fila][0], 
                            "nombres" => ltrim(rtrim($nombres)), 
                            "documento" => ltrim(rtrim($registro[$fila][2])), 
                            "empresa" => ltrim(rtrim($registro[$fila][3])), 
                            "cargo" => ltrim(rtrim($registro[$fila][5])), 
                            "cargo_nombre" => ltrim(rtrim($registro[$fila][6])), 
                            "area" => ltrim(rtrim($registro[$fila][7])), 
                            "usuario_jd" => $usuario_jd, 
                            "correo" => $correo,
                            "estado" => 2, 
                            "telefono" => $telefono, 
                            "supervisor" => $supervisor);
            if($fila > 0 && $datos_cliente["nombres"] != "" && $datos_cliente["AN8"] > 1){
                $consulta_cliente_actual = Cliente_Modelo::consulta_existencia_cliente_modelo("cliente", $datos_cliente["AN8"]);
                if($consulta_cliente_actual == NULL && !isset($registro[$fila][14])){
                    $insertar = Cliente_Modelo::registar_cliente_modelo("cliente", $datos_cliente);
                    $contador_insertados ++;
                }else {
                    if(isset($registro[$fila][14]) && $consulta_cliente_actual != NULL){
                        if($registro[$fila][14] != "" && $consulta_cliente_actual["estado"] == 2){
                            $actualizar = Cliente_Modelo::desactivar_usuario_modelo("cliente", $datos_cliente);
                            $contador_clientes_desactivados ++;
                        }
                    }else if($consulta_cliente_actual != NULL){
                        if($datos_cliente["nombres"] != $consulta_cliente_actual["nombres"] || 
                            $datos_cliente["documento"] != $consulta_cliente_actual["documento"] ||
                            $datos_cliente["usuario_jd"] != $consulta_cliente_actual["usuario_jd"] ||
                            $datos_cliente["telefono"] != $consulta_cliente_actual["telefono"] ||
                            $datos_cliente["empresa"] != $consulta_cliente_actual["empresa"] ||
                            $datos_cliente["area"] != $consulta_cliente_actual["area"] ||
                            $datos_cliente["cargo"] != $consulta_cliente_actual["cargo"]){
                            $actualizar = Cliente_Modelo::actualizar_data_cliente_modelo("cliente", $datos_cliente);
                            $contador_actualizados ++;
                        }
                    }
                }
                if($consulta_cliente_actual != NULL && !isset($registro[$fila][14])){
                    if($consulta_cliente_actual["estado"] == 1){
                        $actualizar = Cliente_Modelo::activar_usuario_modelo("cliente", $datos_cliente);
                        $contador_clientes_activados ++;
                    }
                }
                $consulta_exitencia_cargo = Cliente_Modelo::consulta_existencia_cargo_cliente_modelo("cargo", $datos_cliente["cargo"]);
                if($consulta_exitencia_cargo == NULL){
                    $cargo = ltrim(rtrim($registro[$fila][6]));
                    $cargo = Cliente_Controlador::eliminar_tildes_controlador($cargo);
                    $crear_cargo = Cliente_Modelo::crear_cargo_cliente_modelo("cargo", $datos_cliente, $cargo);
                    $contador_cargos_creados ++;
                }
                if($datos_cliente["area"] == 11120){
                    $consultar_existencia_agente = Cliente_Modelo::consulta_existencia_cliente_modelo("usuario", $datos_cliente["AN8"]);
                    if(isset($registro[$fila][14]) && $consultar_existencia_agente != NULL){
                        if($consultar_existencia_agente["estado"] == 2){
                            $actualizar = Cliente_Modelo::desactivar_usuario_modelo("usuario", $datos_cliente);
                            $contador_agentes_desactivados ++;
                        }
                    }else if($consultar_existencia_agente == NULL && !isset($registro[$fila][14])){
                        $crear_agente = Cliente_Modelo::crear_agente_modelo("usuario", $datos_cliente);
                        $contador_agentes_creados ++;
                    }
                    if($consultar_existencia_agente != NULL && !isset($registro[$fila][14])){
                        if($consultar_existencia_agente["estado"] == 1){
                            $actualizar = Cliente_Modelo::activar_usuario_modelo("usuario", $datos_cliente);
                            $contador_agentes_activados ++;
                        }
                    }
                }
            }
        }
        echo 'Se registraron '.$contador_insertados.' clientes - Se actulizaron '.$contador_actualizados.' clientes';
        if($contador_cargos_creados > 0){
            echo ' - Se crearon '.$contador_cargos_creados.' cargos';
        }
        if($contador_agentes_creados > 0){
            echo ' - Se crearon '.$contador_agentes_creados.' agentes';
        }
        if($contador_clientes_desactivados > 0){
            echo ' - Se desactivarion '.$contador_clientes_desactivados.' clientes';
        }
        if($contador_agentes_desactivados > 0){
            echo ' - Se desactivarion '.$contador_agentes_desactivados.' agentes';
        }
        if($contador_clientes_activados > 0){
            echo ' - Se activarion '.$contador_clientes_activados.' clientes';
        }
        if($contador_agentes_activados > 0){
            echo ' - Se activarion '.$contador_agentes_activados.' agentes';
        }
    }
}
