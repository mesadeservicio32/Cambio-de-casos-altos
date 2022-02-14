<?php 
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Pqr_Controlador{
    static public function mostrar_formulario_con_excepcion_controlador($datos) {
        if($datos["excepcion"] == 0){
            echo '<option value="0"> Escoge una opción - El funcionario registra esta opción </option>'.$datos["excepcion"];
        }else{
            $consulta_excepcion = Pqr_Modelo::mostrar_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
            echo '<option value="'.$consulta_excepcion["id"].'"> '.$consulta_excepcion[$datos["columna"]].'</option>';
        }
        $consulta = Pqr_Modelo::mostrar_formulario_con_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
        foreach ($consulta as $fila => $item) {
            echo '<option value="'.$item["id"].'"> '.$item[$datos["columna"]].'</option>';
        }
    }

    static public function mostrar_formulario_con_excepcion_ordenado_controlador($datos) {
        if($datos["excepcion"] == 0){
            echo '<option value="0"> Escoge una opción </option>'.$datos["excepcion"];
        }else{
            $consulta_excepcion = Pqr_Modelo::mostrar_excepcion_modelo($datos["tabla"], $datos["excepcion"]);
            echo '<option value="'.$consulta_excepcion["id"].'"> '.$consulta_excepcion[$datos["columna"]].'</option>';
        }
        $consulta = Pqr_Modelo::mostrar_formulario_con_excepcion_ordenado_modelo($datos["tabla"], $datos["excepcion"]);
        foreach ($consulta as $fila => $item) {
            echo '<option value="'.$item["id"].'"> '.$item[$datos["columna"]].'</option>';
        }
    }

    static public function consultar_permiso_envio_notificacion_controlador($id_notificacion, $cliente){
        $consulta_notificacion_cliente = Pqr_Modelo::consulta_notificacion_cliente_modelo("cliente_notificacion", $cliente, $id_notificacion);
        $enviar_notificacion = "Si";
        if($consulta_notificacion_cliente == NULL && $id_notificacion != 1){
            $enviar_notificacion = "No";
        }
        if($consulta_notificacion_cliente != NULL){
            if($consulta_notificacion_cliente["estado"] == 1){
                $enviar_notificacion = "No";
            }
        }
        return $enviar_notificacion;
    }

    static public function total_caso_controlador($estado){
        if($estado == "Sin resolver"){
            $respuesta = Pqr_Modelo::listado_solicitudes_modelo("pqr_solicitud");
            $total = count($respuesta);
        }elseif($estado == "No asignado"){
            $respuesta = Pqr_Modelo::listado_solicitudes_sin_asignar_modelo("pqr_solicitud");
            $total = count($respuesta);
        }elseif($estado == "En espera"){
            $respuesta = Pqr_Modelo::listado_solicitudes_en_espera_modelo("pqr_solicitud");
            $total = count($respuesta);
        }else{
            $fecha_primer_dia_mes = date("Y-m-");
            $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
            $respuesta = Pqr_Modelo::listado_solicitudes_mensual_modelo("pqr_solicitud", $fecha_primer_dia_mes);
            $total = count($respuesta);
        }
        echo '<h3>'.$total.'</h3>';
    }

    static public function ver_detalle_casos_mensual_controlador(){
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Pqr_Modelo::listado_solicitudes_mensual_modelo("pqr_solicitud", $fecha_primer_dia_mes);
        
        echo '<div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha Creado</th>
                            <th>Hora Creado</th>
                            <th>ID caso</th>
                            <th>Tema</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-left">';
                    $contador_categoria_requerimiento = 0;
                    $contador_categoria_incidente = 0;
                    $contador_categoria_otro = 0;
                    $contador_categoria_problema = 0;
                    foreach ($respuesta as $fila => $item) {
                        if($item["prioridad"] == 1){
                            $contador_categoria_otro ++;
                        }elseif($item["prioridad"] == 2){
                            $contador_categoria_requerimiento ++;
                        }elseif($item["prioridad"] == 3){
                            $contador_categoria_incidente ++;
                        }elseif($item["prioridad"] == 4){
                            $contador_categoria_problema ++;
                        }
                        if($item["estado"] == "Sin asignar"){
                            $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "Asignado"){
                            $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "En espera"){
                            $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "En ejecución"){
                            $estado = "<a class='btn btn-info disabled'> ".$item["estado"]." </a>"; 
                        }elseif($item["estado"] == "Resuelto"){
                            $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "Cerrado"){
                            $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "Cancelado"){
                            $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
                        }
                        $fecha_creado = date("d-m-Y", strtotime($item["auditoria_creado"]));
                        $hora = date("H:i A", strtotime($item["auditoria_creado"]));
                        echo '<tr>
                                <td>'.$fecha_creado.'</td>
                                <td>'.$hora.'</td>
                                <td>'.$item["id"].'</td>
                                <td>'.$item["tema"].'</td>
                                <td>'.$item["cliente"].'</td>
                                <td>'.$estado.'</td>
                            </tr>';
                    }
                echo'</tbody>
                </table>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-6"><h5 class="text-left">Requerimiento: '.$contador_categoria_requerimiento.'</h5></div>
                    <div class="col-md-6"><h5 class="text-left">Incidente: '.$contador_categoria_incidente.'</h5></div>
                    <div class="col-md-6"><h5 class="text-left">Otro: '.$contador_categoria_otro.'</h5></div>
                    <div class="col-md-6"><h5 class="text-left">Problema: '.$contador_categoria_problema.'</h5></div>
                </div>
            </div>';
    }

    static public function gestor_casos_usuario_controlador(){
        $respuesta = Pqr_Modelo::listado_casos_usuario_modelo("usuario");
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
                $caso_en_espera = Pqr_Modelo::listado_casos_activos_usuario_modelo("pqr_solicitud", $respuesta[$i]["id"]);
                $consultar_casos_asignados = "<button type='button' formulario_consultar_casos_usuario_id='".$respuesta[$i]["id"]."' formulario_consultar_casos_usuario_estado='Asignado' class='btn btn-warning btn-sm consultar-casos-usuario' title='Casos Asignados' data-toggle='modal' data-target='#modal_consultar_casos_usuario'><i class='fas fa-clipboard-list'></i></button>";
                $consultar_casos_en_espera = "<button type='button' formulario_consultar_casos_usuario_id='".$respuesta[$i]["id"]."' formulario_consultar_casos_usuario_estado='En espera' class='btn btn-danger btn-sm consultar-casos-usuario' title='Casos En espera' data-toggle='modal' data-target='#modal_consultar_casos_usuario'><i class='fas fa-clock'></i></button>";
                $acciones = "<div class='btn-group'>".$consultar_casos_asignados.$consultar_casos_en_espera."</div>";
                $respuesta_json .='[
                    "'.$respuesta[$i]["nombres"].'",
                    "'.$respuesta[$i]["total"].'",
                    "'.$caso_en_espera["total"].'",
                    "'.$acciones.'"
                ],';
            }
        $respuesta_json = substr($respuesta_json, 0, -1);
		$respuesta_json .= '] 
        }';
        echo $respuesta_json;
    }

    static public function formulario_consultar_casos_usuario_controlador($datos){
       if($datos["formulario_consultar_casos_usuario_estado"] == "Asignado"){
            $titulo = "Casos Asignados";     
            $consulta = Pqr_Modelo::consulta_casos_asignados_usuario_modelo("pqr_solicitud", $datos["formulario_consultar_casos_usuario_id"]);
       }else {
            $titulo = "Casos En espera";        
            $consulta = Pqr_Modelo::consulta_casos_usuario_en_espera_modelo("pqr_solicitud", $datos["formulario_consultar_casos_usuario_id"]);
       }
       echo '<h3>'.$titulo.'</h3>';
        if(count($consulta) > 0){
            echo '<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha Creado</th>
                                <th>ID caso</th>
                                <th>Tema</th>
                                <th>Cliente</th>
                            </tr>
                        </thead>
                        <tbody class="text-left">';
                        foreach ($consulta as $fila => $item) {
                            $fecha_creado = date("d-m-Y", strtotime($item["auditoria_creado"]));
                            echo '<tr>
                                    <td>'.$fecha_creado.'</td>
                                    <td>'.$item["id"].'</td>
                                    <td>'.$item["tema"].'</td>
                                    <td>'.$item["cliente"].'</td>
                                </tr>';
                        }
                    echo'</tbody>
                    </table>
                </div>';
        }else{
            echo 'Hay 0 casos '.$datos["formulario_consultar_casos_usuario_estado"];
        }
    }

    static public function nueva_solicitud_pqr_controlador($datos) {
        if(isset($datos["nueva_solicitud_archivo"]["tmp_name"])){
            $consecutivo = Pqr_Modelo:: consulta_consecutivo_modelo("pqr_solicitud");
            $fecha_creado = date("d-m-y", strtotime($datos["nueva_solicitud_auditoria_creado"]));
            $hora_creado = date("H-i-s", strtotime($datos["nueva_solicitud_auditoria_creado"]));
            $nombre_archivo = $datos["nueva_solicitud_id_solicitante"].'-'.$fecha_creado .'-'. $hora_creado.'-'.$consecutivo["consecutivo"];
			list($ancho, $alto) = getimagesize($datos["nueva_solicitud_archivo"]["tmp_name"]);
			$nuevo_ancho = 800;
			$nuevo_alto = 600;
			$destino = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
			if ($datos["nueva_solicitud_archivo"]["type"] == "image/jpeg"){
				$ruta = "../img/pqr_solicitud/".$nombre_archivo.".jpg";
				$origen = @imagecreatefromjpeg($datos["nueva_solicitud_archivo"]["tmp_name"]);
				$destino = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
				@imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
				imagejpeg($destino, $ruta);
				$nombre_archivo = $nombre_archivo.".jpg";
			}
			if ($datos["nueva_solicitud_archivo"]["type"] == "image/png"){
				$ruta = "../img/pqr_solicitud/".$nombre_archivo.".png";
				$origen = @imagecreatefrompng($datos["nueva_solicitud_archivo"]["tmp_name"]);
				imagealphablending($destino, FALSE);
				@imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
				imagepng($destino, $ruta);
				$nombre_archivo = $nombre_archivo.".png";
			}
		}else{
			$nombre_archivo = "";
		}
        $respuesta = Pqr_Modelo:: nueva_solicitud_pqr_modelo("pqr_solicitud", $datos, $nombre_archivo);
        if($respuesta == "Ok"){
            $datos_cliente_contratante = Pqr_Modelo::consultar_cliente_contratante_modelo("cliente", $datos["nueva_solicitud_id_solicitante"]);
            $datos_correo_cliente = Pqr_Modelo::consultar_correo_cliente_modelo("cliente", $datos["nueva_solicitud_id_solicitante"]);
            $fecha_creado = $datos["nueva_solicitud_auditoria_creado"];
            $fecha_creado = date("d/m/Y");
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
                $mail->addAddress($datos_correo_cliente["correo"]);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Creaste un caso | Portal Solicitantes Mesa de Ayuda';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">¿Necesitas nuestra ayuda?</h2>
                                    <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_correo_cliente["nombres"].',<br>No te preocupes, ya recibimos tu solicitud "'.$datos["nueva_solicitud_asunto"].'".</h4>
                                    <h4 style="font-weight:100; padding:0 20px">¡La atenderemos lo más pronto posible!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
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
                $mail->addAddress('mesadeservicio@croydon.com.co');
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = '¡Tienes una nueva solicitud! | Portal Usuarios Croydon';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">Han registrado una nueva solicitud</h2>
                                    <h4 style="font-weight:100; padding:0 20px">Hola Administrador de mesa de servicio,<br>El cliente "'.$datos_cliente_contratante["nombres"].'" ha realizado una solicitud a nombre de '.$datos_correo_cliente["nombres"].' el '.$fecha_creado.'.</h4>
                                    <h4 style="font-weight:100; padding:0 20px">¡Ya esta disponible en el gestor de solicitudes para ser asignada!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
        }
        echo $respuesta;
    }
    static public function consulta_incidente_id_subcategoria_controlador($datos){
        $respuesta = Pqr_Modelo::formulario_pqr_incidente_modelo("pqr_incidente", $datos["consulta_incidente_id_subcategoria"]);
        foreach ($respuesta as $fila => $item) {
            echo '<option value="'.$item["id"].'">'.$item["incidente"].'</option>';
        }
    }

    static public function formulario_pqr_controlador($tabla){
        if ($tabla == "estado") {
            $respuesta = Pqr_Modelo::formulario_pqr_modelo($tabla);
            foreach ($respuesta as $fila => $item) {
                echo '<option value="'.$item["id"].'">'.$item["estado"].'</option>';
            }
        }else if($tabla == "colaborador"){
            $rol = 5;
            $respuesta = Pqr_Modelo::consulta_colaborador_modelo('usuario', $rol);
            print_r($respuesta);
            foreach ($respuesta as $fila => $item) {
                echo '<option value="'.$item["id"].'">'.$item["nombres"].'</option>';
            }
        }else if($tabla == "pqr_categoria"){
            $respuesta = Pqr_Modelo::formulario_pqr_modelo($tabla);
            foreach ($respuesta as $fila => $item) {
                echo '<option value="'.$item["id"].'">'.$item["categoria"].'</option>';
            }
        }else if($tabla == "pqr_subcategoria"){
            $respuesta = Pqr_Modelo::formulario_pqr_subcategoria_modelo($tabla);
            foreach ($respuesta as $fila => $item) {
                echo '<option value="'.$item["id"].'">'.$item["subcategoria"].'</option>';
            }
        }else if($tabla == "pqr_prioridad"){
            $respuesta = Pqr_Modelo::formulario_pqr_modelo($tabla);
            foreach ($respuesta as $fila => $item) {
                echo '<option value="'.$item["id"].'">'.$item["prioridad"].'</option>';
            }
        }else {
            $respuesta = Pqr_Modelo::formulario_pqr_modelo($tabla);
            foreach ($respuesta as $fila => $item) {
                echo '<option value="'.$item["id"].'">'.$item["nombre"].'</option>';
            }
        }
        
    }
    
    static public function lista_mis_solicitudes_controlador(){
        date_default_timezone_set("America/Bogota");
        $fecha = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"]; 
        $raiz = $_COOKIE["raiz"];
        $tipo_usuario = $_COOKIE["tipo_usuario"];
        $respuesta = Pqr_Modelo::lista_mis_solicitudes_modelo("pqr_solicitud", $auditoria_usuario);
        echo '<div class="box-body">
            <div class="table-responsive">
                <table id ="tabla-mis-casos" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID solicitud</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach ($respuesta as $fila => $item) {
                        $cerrar_caso = "";
                        $historial = "";
                        $reabrir = "";
                        $calificacion = "";
                        $ver_detalle = "<button type='button' formulario_detalle_solicitud='".$item["id"]."' formulario_detalle_auditoria_usuario='".$auditoria_usuario."' class='btn btn-primary btn-sm formulario-detalle-solicitud' title='Ver detalle' data-toggle='modal' data-target='#modal_detalle_solicitud'><i class='fas fa-edit'></i></button>";
                        $historial = "<button type='button' historial_respuesta_id_cliente='".$item["id"]."' class='btn btn-info btn-sm consulta-cliente-solicitud' title='Historial de respuestas' data-toggle='modal' data-target='#modal_historial_respuesta'><i class='fas fa-list'></i></button>";
                        if($item["estado"] == "Sin asignar"){
                            $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "Asignado"){
                            $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                        }elseif($item["estado"] == "En espera"){
                            $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                        }else if($item["estado"] == "En ejecución"){
                            $estado = "<a class='btn btn-info disabled'> ".$item["estado"]." </a>"; 
                        }else if($item["estado"] == "Resuelto"){
                            $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                            $cerrar_caso = "<button type='button' formulario_finalizar_tipo_usuario='cliente' formulario_finalizar_id_solicitud='".$item["id"]."' formulario_finalizar_auditoria_usuario='".$auditoria_usuario."' formulario_finalizar_auditoria_modificado='".$fecha."' class='btn btn-danger btn-sm finalizar-solicitud' title='Cerrar caso' data-toggle='modal' data-target='#modal_finalizar_solicitud_soporte'><i class='far fa-times-circle'></i></button>";
                        }else if($item["estado"] == "Cerrado"){
                            $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                            $reabrir = "<button type='button' formulario_reabrir_auditoria_creado='".$fecha."' formulario_reabrir_auditoria_usuario='".$auditoria_usuario."' formulario_reabrir_id_solicitud='".$item["id"]."' class='btn btn-success btn-sm reabrir-solicitud' title='Reabrir solicitud' data-toggle='modal' data-target='#modal_reabrir_solicitud'><i class='fas fa-redo-alt'></i></button>";
                        }else if($item["estado"] == "Cancelado"){
                            $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
                        }
                        if($item["archivo"] != NULL){
                            $archivo = "<button type='button' formulario_archivo_adjunto_solicitud='".$item["id"]."' class='btn btn-warning btn-sm formulario-adjunto-solicitud' title='Archivos ajuntos' data-toggle='modal' data-target='#modal_adjunto_solicitud'><i class='fas fa-folder-open'></i></button>";
                        }else{
                            $archivo = "";
                        }
                        if($item["estado"] == "Cerrado"){
                            $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                            $calificacion = "<button type='button' consulta_calificacion_id='".$item["id"]."' class='btn btn-primary btn-sm consulta-calificacion' title='Consultar resultados encuesta' data-toggle='modal' data-target='#modal_consulta_calificacion'><i class='fas fa-clipboard-check'></i></button>";
                            if($item["id"] >= 3500){
                                $reabrir = "<button type='button' formulario_reabrir_auditoria_creado='".$fecha."' formulario_reabrir_auditoria_usuario='0' formulario_reabrir_id_solicitud='".$item["id"]."' class='btn btn-success btn-sm reabrir-solicitud' title='Reabrir solicitud' data-toggle='modal' data-target='#modal_reabrir_solicitud'><i class='fas fa-redo-alt'></i></button>";       
                            }
                        }
                        $acciones = "<div class='btn-group'>".$ver_detalle."".$historial."".$cerrar_caso."".$reabrir."".$calificacion."".$archivo."</div>";
                        echo '<tr>
                                <td>'.$item["id"].'</td>
                                <td>'.ltrim(rtrim($item["tema"])).'</td>
                                <td>'.$estado.'</td>
                                <td>'.$acciones.'</td>
                            </tr>';
                    }
                echo'</tbody>
                </table>
            </div>
        </div>';
        echo " <script type='text/javascript'>
            $(document).ready(function() {
                $('#tabla-mis-casos thead tr').clone(true).appendTo( '#tabla-mis-casos thead' );
                $('#tabla-mis-casos thead tr:eq(1) th').each( function (i) {
                    var title = $(this).text();
                    $(this).html( '<input type="; echo 'text" placeholder="Filtro'; echo " '+title+'"; echo ' " '; echo "/>'"; echo ");
                
                    $( 'input', this ).on( 'keyup change', function () {
                        if ( table.column(i).search() !== this.value ) {
                            table
                                .column(i)
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );
                var table = $('#tabla-mis-casos').DataTable( {
                    orderCellsTop: true,
                    fixedHeader: true,
                    dom: 'Bfrtip',
                    order: [[ 0, 'desc' ]],
                    buttons: [
                        'pdfHtml5'
                    ],
                    deferRender: true,
                    retrieve: true,
                    processing: true,";
                echo 'language: {
                    sProcessing: "Procesando...",
                    sLengthMenu: "Mostrar _MENU_ registros",
                    sZeroRecords: "No se encontraron resultados",
                    sEmptyTable: "Ningún dato disponible en esta tabla",
                    sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                    sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0",
                    sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                    sInfoPostFix: "",
                    sSearch: "Buscar:",
                    sUrl: "",
                    sInfoThousands: ",",
                    sLoadingRecords: "Cargando...",
                    oPaginate: {
                        sFirst: "Primero",
                        sLast: "Último",
                        sNext: "Siguiente",
                        sPrevious: "Anterior"
                    },oAria: {
                        sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                        sSortDescending: ": Activar para ordenar la columna de manera descendente"
                    }
                }';
            echo "} );
            } );
        </script>";
    }

    static public function listado_cliente_controlador(){
        $respuesta = Pqr_Modelo::listado_cliente_modelo("cliente");
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
                if($respuesta[$i]["area"] != ""){
                    $area = $respuesta[$i]["area"];
                }else{
                    $area = "Dato no registrado";
                }
                $area = ltrim(rtrim($respuesta[$i]["area"]));
                $crear_pqr = "<button type='button' formulario_crear_pqr_id_cliente='".$respuesta[$i]["id"]."' formulario_crear_pqr_auditoria_usuario='".$auditoria_usuario."' formulario_crear_pqr_auditoria_creado='".$fecha."' class='btn btn-success btn-sm crear-solicitud' title='Crear solicitud' data-toggle='modal' data-target='#modal_crear_solicitud'><i class='fas fa-ticket-alt'></i></button>";
                $editar = "<button type='button' formulario_editar_id_cliente='".$respuesta[$i]["id"]."' formulario_editar_cliente_modificado='".$auditoria_usuario."' class='btn btn-success btn-sm editar-cliente' title='Editar' data-toggle='modal' data-target='#modal_editar_cliente'><i class='fa fa-edit'></i></button>";
                $eliminar = "<button type='button' formulario_eliminar_id_cliente='".$respuesta[$i]["id"]."' formulario_eliminar_cliente_modificado='".$auditoria_usuario."' class='btn btn-danger btn-sm eliminar-cliente' title='Eliminar' data-toggle='modal' data-target='#modal_eliminar_cliente'><i class='fa fa-trash-o'></i></button>";
                $acciones = "<div class='btn-group'>".$crear_pqr."</div>";
                $respuesta_json .='[
                    "'.ltrim(rtrim($respuesta[$i]["nombres"])).'",
                    "'.ltrim(rtrim($respuesta[$i]["telefono"])).'",
                    "'.ltrim(rtrim($respuesta[$i]["correo"])).'",
                    "'.$area.'",
                    "'.$boton_estado.'",
                    "'.$acciones.'"
                ],';
            }
        $respuesta_json = substr($respuesta_json, 0, -1);
		$respuesta_json .= '] 
        }';
        echo $respuesta_json;
    }

    static public function gestor_solicitud_controlador($estado){
        date_default_timezone_set("America/Bogota");
        $fecha = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $contador_solicitudes = 0;
        $tipo_usuario = $_COOKIE["tipo_usuario"];
        if($estado == "resuelto"){
            $respuesta = Pqr_Modelo::listado_solicitudes_resueltas_modelo("pqr_solicitud");
        }elseif($estado == "sin asignar"){
            $respuesta = Pqr_Modelo::listado_solicitudes_sin_asignar_modelo("pqr_solicitud");
        }elseif($estado == "cerrado"){
            $respuesta = Pqr_Modelo::listado_solicitudes_cerrado_modelo("pqr_solicitud");
        }else{
            $respuesta = Pqr_Modelo::listado_solicitudes_modelo("pqr_solicitud");
        }
        echo '<div class="box box-default">
            <div class="box-body">
                    <div class="table-responsive">
                        <table id ="tabla-filtro-caso-estado" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID solicitud</th>
                                    <th>Cliente</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($respuesta as $fila => $item) {
                                $cerrar_caso = "";
                                $historial = "";
                                $reabrir = "";
                                $reactivar = "";
                                $asignar = "<button type='button' formulario_asignar_auditoria_creado='".$fecha."' formulario_asignar_id_solicitud='".$item["id"]."' formulario_asignar_auditoria_usuario='".$auditoria_usuario."' class='btn btn-warning btn-sm asignar-solicitud-pqr' title='Asignar' data-toggle='modal' data-target='#modal_asignar_solicitud'><i class='fas fa-people-carry'></i></button>";
                                $ver_detalle = "<button type='button' formulario_detalle_solicitud='".$item["id"]."' formulario_detalle_auditoria_usuario='".$auditoria_usuario."' class='btn btn-primary btn-sm formulario-detalle-solicitud' title='Ver detalle' data-toggle='modal' data-target='#modal_detalle_solicitud'><i class='fas fa-edit'></i></button>";
                                $historial = "<button type='button' historial_respuesta_id_cliente='".$item["id"]."' class='btn btn-info btn-sm consulta-cliente-solicitud' title='Historial de respuestas' data-toggle='modal' data-target='#modal_historial_respuesta'><i class='fas fa-list'></i></button>";
                                if($item["estado"] == "Sin asignar"){
                                    $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
                                }elseif($item["estado"] == "Asignado"){
                                    $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                                }elseif($item["estado"] == "En espera"){
                                    $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                                    $reactivar = "<button type='button' formulario_cambiar_estado_solicitud_soporte_estado='3' formulario_cambiar_estado_solicitud_soporte_id='".$item["id"]."' formulario_cambiar_estado_solicitud_soporte_auditoria_usuario='".$auditoria_usuario."' formulario_cambiar_estado_solicitud_soporte_fecha='".$fecha."' class='btn btn-success btn-sm cambiar-estado-solicitud' title='Reactivar caso' data-toggle='modal' data-target='#modal_cambiar_estado_solicitud_soporte'><i class='fas fa-play'></i></button>";
                                }else if($item["estado"] == "En ejecución"){
                                    $estado = "<a class='btn btn-info disabled'> ".$item["estado"]." </a>"; 
                                }else if($item["estado"] == "Resuelto"){
                                    $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                                    $asignar = "";
                                }else if($item["estado"] == "Cerrado"){
                                    $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                                    $reabrir = "<button type='button' formulario_reabrir_auditoria_creado='".$fecha."' formulario_reabrir_auditoria_usuario='".$auditoria_usuario."' formulario_reabrir_id_solicitud='".$item["id"]."' class='btn btn-success btn-sm reabrir-solicitud' title='Reabrir solicitud' data-toggle='modal' data-target='#modal_reabrir_solicitud'><i class='fas fa-redo-alt'></i></button>";
                                    $asignar = "";
                                }else if($item["estado"] == "Cancelado"){
                                    $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
                                    $reabrir = "<button type='button' formulario_reabrir_auditoria_creado='".$fecha."' formulario_reabrir_auditoria_usuario='".$auditoria_usuario."' formulario_reabrir_id_solicitud='".$item["id"]."' class='btn btn-success btn-sm reabrir-solicitud' title='Reabrir solicitud' data-toggle='modal' data-target='#modal_reabrir_solicitud'><i class='fas fa-redo-alt'></i></button>";
                                    $asignar = "";
                                }
                                if($item["estado"] == "Cerrado"){
                                    $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                                    $asignar = "";
                                }
                                $acciones = "<div class='btn-group'>".$reactivar."".$historial."".$cerrar_caso."".$asignar."".$reabrir."".$ver_detalle."</div>";
                                echo '<tr>
                                        <td>'.$item["id"].'</td>
                                        <td>'.$item["nombres"].'</td>
                                        <td>'.ltrim(rtrim($item["tema"])).'</td>
                                        <td>'.$estado.'</td>
                                        <td>'.$acciones.'</td>
                                    </tr>';
                            }
                        echo'</tbody>
                        </table>
                    </div>
                </div>
            </div>';
            echo " <script type='text/javascript'>
                $(document).ready(function() {
                    $('#tabla-filtro-caso-estado thead tr').clone(true).appendTo( '#tabla-filtro-caso-estado thead' );
                    $('#tabla-filtro-caso-estado thead tr:eq(1) th').each( function (i) {
                        var title = $(this).text();
                        $(this).html( '<input type="; echo 'text" placeholder="Filtro'; echo " '+title+'"; echo ' " '; echo "/>'"; echo ");
                  
                        $( 'input', this ).on( 'keyup change', function () {
                            if ( table.column(i).search() !== this.value ) {
                                table
                                    .column(i)
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                    } );
                    var table = $('#tabla-filtro-caso-estado').DataTable( {
                        orderCellsTop: true,
                        fixedHeader: true,
                        dom: 'Bfrtip',
                        buttons: [
                            'pdfHtml5'
                        ],
                        deferRender: true,
                        retrieve: true,
                        processing: true,";
                    echo 'language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0",
                        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                        sInfoPostFix: "",
                        sSearch: "Buscar:",
                        sUrl: "",
                        sInfoThousands: ",",
                        sLoadingRecords: "Cargando...",
                        oPaginate: {
                            sFirst: "Primero",
                            sLast: "Último",
                            sNext: "Siguiente",
                            sPrevious: "Anterior"
                        },oAria: {
                            sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                            sSortDescending: ": Activar para ordenar la columna de manera descendente"
                        }
                    }';
                echo "} );
                } );
            </script>";
    }

    static public function formulario_crear_solicitud_controlador($datos){
        $consulta_cliente = Pqr_Modelo::consulta_cliente_modelo("cliente", $datos["formulario_crear_pqr_id_cliente"]);
        $casos_por_calificar = Pqr_Modelo::consulta_casos_por_calificar_cliente_modelo("pqr_solicitud", $datos["formulario_crear_pqr_id_cliente"]);
        if(count($casos_por_calificar) > 0 && $datos["formulario_crear_pqr_auditoria_usuario"] == 0){
            echo '<h2 style="font-weight:700">Tienes '.count($casos_por_calificar).' casos por calificar</h2>
                  <h4 style="font-weight:100; padding:0 20px">Hola '.$consulta_cliente["nombres"].',<br>ya resolvimos tus solicitudes "';
            $index = 0;
            foreach ($casos_por_calificar as $fila => $caso) {
                echo $caso["id"];
                if(count($casos_por_calificar) - 1 != $index){
                    if(count($casos_por_calificar) - 2 == $index){
                        echo ' y ';
                    }else{
                        echo ' , ';
                    }
                }
                $index ++;
            }
            echo '"</h4><h4 style="font-weight:100; padding:0 20px">¡Podras crear más casos cuando hayas calificado!</h4>
                  <h4 style="font-weight:100; padding:0 20px"><cite>Calificar tu experiencia nos ayuda a mejorar</cite></h4>';
        }else{
            if($consulta_cliente["correo"] == ""){
                echo '<div class="alert alert-info text-center">
                    <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                    <h5>No puedes crear un caso para este cliente, primero debes registrarle una dirección de correo.</h5>
                    <h6>Recuerda que el correo que registres es al que llegara toda la información asociada al caso.</h6>
                </div>';
            }
            if($datos["formulario_crear_pqr_auditoria_usuario"] == 0){
                echo '<div class="box-body>
                        <form method="POST">
                            <div class="form-group has-feedback">
                                <input type="hidden" id="crear_solicitud_id_solicitante" value="'.$datos["formulario_crear_pqr_id_cliente"].'">
                                <input type="hidden" id="crear_solicitud_auditoria_usuario" value="">
                                <input type="hidden" id="crear_solicitud_auditoria_creado" value="'.$datos["formulario_crear_pqr_auditoria_creado"].'">
                                <input type="hidden" id="crear_solicitud_tipo_registro" value="cliente">
                                <input type="hidden" id="crear_solicitud_prioridad" value="0">
                                <input type="hidden" id="crear_solicitud_categoria" value="0">
                                <input type="hidden" id="crear_solicitud_subcategoria" value="0">
                                <input type="hidden" id="crear_solicitud_incidente" value="0">
                            </div>';
                          
            }else{
                echo '<div class="box-body>
                        <form method="POST">
                            <div class="form-group has-feedback">
                                <input type="hidden" id="crear_solicitud_id_solicitante" value="'.$datos["formulario_crear_pqr_id_cliente"].'">
                                <input type="hidden" id="crear_solicitud_auditoria_usuario" value="'.$datos["formulario_crear_pqr_auditoria_usuario"].'">
                                <input type="hidden" id="crear_solicitud_auditoria_creado" value="'.$datos["formulario_crear_pqr_auditoria_creado"].'">
                                <input type="hidden" id="crear_solicitud_tipo_registro" value="agente">
                                <label class="control-label">¿Quien atendera la solicitud?</label>
                                <select id="crear_solicitud_id_asignado" class="form-control">';
                                    $datos_formulario = array("tabla"=> "usuario",
                                    "columna"=>"nombres",
                                    "excepcion"=> 0);
                                    $funcionario = new Pqr_Controlador();
                                    $funcionario -> mostrar_formulario_con_excepcion_ordenado_controlador($datos_formulario);
                           echo'</select>
                            </div>
                            <div class="form-group has-feedback">
                                <label class="control-label">Prioridad</label>
                                <select id="crear_solicitud_prioridad" class="form-control">
                                    <option value=""> Escoja una opción </option>';
                                    $prioridad = new Pqr_Controlador();
                                    $prioridad -> formulario_pqr_controlador("pqr_prioridad");
                           echo'</select>
                            </div>
                            <div class="form-group has-feedback">
                                <label class="control-label">Categoria</label>
                                <select id="crear_solicitud_categoria" class="form-control">
                                    <option value=""> Escoja una opción </option>';
                                    $categoria = new Pqr_Controlador();
                                    $categoria -> formulario_pqr_controlador("pqr_categoria");
                           echo'</select>
                            </div>
                            <div class="form-group has-feedback">
                                <label class="control-label">Subcategoria</label>
                                <select id="crear_solicitud_subcategoria" class="form-control">
                                    <option value=""> Escoja una opción </option>';
                                    $subcategoria = new Pqr_Controlador();
                                    $subcategoria -> formulario_pqr_controlador("pqr_subcategoria");
                           echo'</select>
                            </div>
                            <div class="form-group has-feedback">
                                <label class="control-label">Falla/Requerimiento</label>
                                <select id="crear_solicitud_incidente" class="form-control">
                                    <option value=""> Escoja una opción </option>';
                           echo'</select>
                            </div>';
            }
            echo '  <div class="form-group has-feedback">
                        <label class="control-label">Asunto</label>
                        <input type="text" id="crear_solicitud_asunto" class="form-control" placeholder="Asunto" required>
                        <span class="glyphicon glyphicon-pencil form-control-feedback"></span>
                        
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Mensaje</label>
                        <textarea id="crear_solicitud_mensaje" rows="10" cols="40" class="form-control" placeholder="Describa su solicitud" required></textarea>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Archivo</label>
                        <input type="file" accept=".jpg,.png,.pdf,.doc,.docx,.xlsx,.xls,.txt,.prn,.csv" id="crear_solicitud_archivo">
                    </div>
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                            <h2 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#crear_caso_ajuntar_mas_archivos" aria-expanded="false" aria-controls="collapseTwo">
                                Adjuntar más archivos
                                </button>
                            </h2>
                            </div>
                            <div id="crear_caso_ajuntar_mas_archivos" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                <div class="card-body">';
                                for ($archivo = 2; $archivo <= 5; $archivo ++) { 
                                    echo '<div class="form-group has-feedback">
                                            <label class="control-label">Archivo '.$archivo.'</label>
                                            <input type="file" accept=".jpg,.png,.pdf,.doc,.docx,.xlsx,.xls,.txt,.prn,.csv" id="crear_solicitud_archivo_'.$archivo.'">
                                        </div>
                                        <script type="text/javascript">
                                            $(document).on("change", "#crear_solicitud_archivo_'.$archivo.'", function () {
                                                var archivo = this.files[0];
                                                var archivo_principal = $("#crear_solicitud_archivo")[0].files[0];
                                                if (archivo["type"] != "text/plain" && archivo["type"] != "application/x-prn" && archivo["type"] != "application/vnd.ms-excel" && archivo["type"] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && archivo["type"] != "application/vnd.openxmlformats-officedocument.wordprocessingml.document" && archivo["type"] != "application/msword" && archivo["type"] != "application/pdf" && archivo["type"] != "application/msword" && archivo["type"] != "image/jpeg" && archivo["type"] != "image/png") {
                                                    swal({
                                                        title: "Error al subir el archivo '.$archivo.'",
                                                        text: "El archivo o el archivo debe estar en formato PDF, CSV, EXCEL, WORD, TXT, PRN, PNG o JPG!",
                                                        type: "error",
                                                        closeOnConfirm: false,
                                                        confirmButtonText: "Ok"
                                                    });
                                                    $("#crear_solicitud_archivo_'.$archivo.'").val("");
                                                }
                                                else if (archivo["size"] > 2000000) {
                                                    swal({
                                                        title: "Error al subir el archivo '.$archivo.'",
                                                        text: "El archivo no debe pesar más de 2MB!",
                                                        type: "error",
                                                        closeOnConfirm: false,
                                                        confirmButtonText: "Ok"
                                                    });
                                                    $("#crear_solicitud_archivo_'.$archivo.'").val("");
                                                }
                                                if(typeof(archivo_principal) == "undefined"){
                                                    swal({
                                                        title: "Error al subir el archivo '.$archivo.'",
                                                        text: "No has cargado el archivo 1!",
                                                        type: "error",
                                                        closeOnConfirm: false,
                                                        confirmButtonText: "Ok"
                                                    });
                                                    $("#crear_solicitud_archivo_'.$archivo.'").val("");
                                                }
                                            });modificar_
                                        </script>';
                                }
                            echo'</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>';
        }
    }

    static public function formulario_detalle_solicitud_controlador($datos){
        $consulta_detalle_solicitud = Pqr_Modelo::consulta_detalle_solicitud_modelo("pqr_solicitud", $datos["formulario_detalle_solicitud"]);
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        $bloqueo_edicion = 'disabled';
        if($_COOKIE["tipo_usuario"] == "funcionario"){
            $bloqueo_edicion = '';
        }
        $bloqueo_edicion_fecha_estimada = 'disabled';
        $color_fecha_estimada_fallida = '';
        $mensaje_fecha_estimada_fallida = '';
        echo '<div class="box-body">
                <form method="POST">
                    <div class="form-group has-feedback">
                        <input type="hidden" id="modificar_solicitud_id_caso" value="'.$datos["formulario_detalle_solicitud"].'">
                        <input type="hidden" id="modificar_solicitud_auditoria_usuario" value="'.$datos["formulario_detalle_auditoria_usuario"].'">
                        <label class="control-label">¿Quien atendera la solicitud?</label>
                        <select id="modificar_solicitud_id_asignado" disabled class="form-control">';
                            $datos_formulario = array("tabla"=> "usuario",
                            "columna"=>"nombres",
                            "excepcion"=> $consulta_detalle_solicitud["asignado"]);
                            $funcionario = new Pqr_Controlador();
                            $funcionario -> mostrar_formulario_con_excepcion_ordenado_controlador($datos_formulario);
                   echo'</select>
                    </div>';
                    if($consulta_detalle_solicitud["fecha_estimada_resuelto"] != NULL){
                        $fecha_estimada_resuelto = date_create($consulta_detalle_solicitud["fecha_estimada_resuelto"]);
                        if($fecha_hoy > $consulta_detalle_solicitud["fecha_estimada_resuelto"]  && $consulta_detalle_solicitud["estado"] != 4 && $consulta_detalle_solicitud["estado"] != 5){
                            $color_fecha_estimada_fallida = 'has-error';
                            $mensaje_fecha_estimada_fallida = '!La fecha actual es superior a la fecha estimada y el estado actual del caso no es cerrado ni resuelto! <br>';
                        }
                        $hora_estimada_resuelto = date_format($fecha_estimada_resuelto, "h:i");
                        $fecha_estimada_resuelto = date_format($fecha_estimada_resuelto, "Y-m-d");
                        if($_COOKIE["rol"] == 7){
                            $bloqueo_edicion_fecha_estimada = '';
                        }
                        echo '<div class="form-group has-feedback '.$color_fecha_estimada_fallida.'">
                                <label class="control-label">'.$mensaje_fecha_estimada_fallida .'Fecha estimada resuelto</label>
                                <input class="form-control" '.$bloqueo_edicion.$bloqueo_edicion_fecha_estimada.' type="date" id="modificar_solicitud_fecha_estimada_resuelto" value="'.$fecha_estimada_resuelto.'">
                            </div>
                            <div class="form-group has-feedback '.$color_fecha_estimada_fallida.'">
                                <label class="control-label">Hora estimada resuelto</label>
                                <input class="form-control" type="time" '.$bloqueo_edicion.$bloqueo_edicion_fecha_estimada.' id="modificar_solicitud_hora_estimada_resuelto" value="'.$hora_estimada_resuelto.'">
                            </div>';
                    }else{
                        echo '<div>
                                <input type="hidden" id="modificar_solicitud_fecha_estimada_resuelto" value=" ">
                                <input type="hidden" id="modificar_solicitud_hora_estimada_resuelto" value=" ">
                            </div>';
                    }
                echo'<div class="form-group has-feedback">
                        <label class="control-label">Prioridad</label>
                        <select id="modificar_solicitud_prioridad" disabled class="form-control">';
                            $datos_formulario = array("tabla"=> "pqr_prioridad",
                            "columna"=>"prioridad",
                            "excepcion"=> $consulta_detalle_solicitud["prioridad"]);
                            $prioridad = new Pqr_Controlador();
                            $prioridad -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                   echo'</select>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Categoria</label>
                        <select id="modificar_solicitud_categoria" '.$bloqueo_edicion.' class="form-control">';
                            $datos_formulario = array("tabla"=> "pqr_categoria",
                            "columna"=>"categoria",
                            "excepcion"=> $consulta_detalle_solicitud["categoria"]);
                            $categoria = new Pqr_Controlador();
                            $categoria -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                   echo'</select>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Subcategoria</label>
                        <select id="modificar_solicitud_subcategoria" disabled class="form-control">';
                            $datos_formulario = array("tabla"=> "pqr_subcategoria",
                            "columna"=>"subcategoria",
                            "excepcion"=> $consulta_detalle_solicitud["subcategoria"]);
                            $subcategoria = new Pqr_Controlador();
                            $subcategoria -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                   echo'</select>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Falla</label>
                        <select id="modificar_solicitud_incidente" disabled class="form-control">';
                            $datos_formulario = array("tabla"=> "pqr_incidente",
                            "columna"=>"incidente",
                            "excepcion"=> $consulta_detalle_solicitud["falla"]);
                            $incidente = new Pqr_Controlador();
                            $incidente -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                   echo'</select>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Asunto</label>
                        <input type="text" id="modificar_solicitud_asunto" disabled class="form-control" placeholder="Asunto" value="'.$consulta_detalle_solicitud["tema"].'" required>
                        <span class="glyphicon glyphicon-pencil form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Mensaje</label>
                        <textarea id="modificar_solicitud_mensaje" rows="10" disabled cols="40" class="form-control" placeholder="Describa su solicitud" required>'.$consulta_detalle_solicitud["mensaje"].'</textarea>
                    </div>
                </form>
            </div>';
    }

    static public function formulario_reabrir_solicitud_controlador($datos){
        $consulta_detalle_solicitud = Pqr_Modelo::consulta_detalle_solicitud_modelo("pqr_solicitud", $datos["formulario_reabrir_id_solicitud"]);
        $bloqueo_opcion = "";
        if($datos["formulario_reabrir_auditoria_usuario"] == 0){
            $bloqueo_opcion = "disabled";
            $mensaje_alerta = "Recuerda que al reabrirlo enviaras un correo al agente indicando que has reabierto el caso.";
        }else{
            $mensaje_alerta = "Recuerda que al reabrirlo enviaras un correo al cliente indicando el proceso que has realizado con el mismo.";
        }
        echo '<div class="alert alert-danger text-center">
                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                <h5>Vas a reabrir un caso.</h5>
                <h6>'.$mensaje_alerta.'</h6>
            </div>
            <div class="box-body>
                <form method="POST">
                    <div class="form-group has-feedback">
                        <input type="hidden" id="reabrir_id_solicitud" value="'.$datos["formulario_reabrir_id_solicitud"].'">
                        <input type="hidden" id="reabrir_auditoria_usuario" value="'.$datos["formulario_reabrir_auditoria_usuario"].'">
                        <input type="hidden" id="reabrir_auditoria_creado" value="'.$datos["formulario_reabrir_auditoria_creado"].'">
                        <label class="control-label">¿Quien atendera la solicitud?</label>
                        <select id="reabrir_id_asignado" '.$bloqueo_opcion.' class="form-control">';
                            $datos_formulario = array("tabla"=> "usuario",
                                    "columna"=>"nombres",
                                    "excepcion"=> $consulta_detalle_solicitud["asignado"]);
                            $funcionario = new Pqr_Controlador();
                            $funcionario -> mostrar_formulario_con_excepcion_ordenado_controlador($datos_formulario);
                    echo'</select>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Prioridad</label>
                        <select id="reabrir_prioridad" disabled class="form-control">';
                                $datos_formulario = array("tabla"=> "pqr_prioridad",
                                "columna"=>"prioridad",
                                "excepcion"=> $consulta_detalle_solicitud["prioridad"]);
                                $prioridad = new Pqr_Controlador();
                                $prioridad -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Categoria</label>
                            <select id="reabrir_categoria" disabled class="form-control">';
                                $datos_formulario = array("tabla"=> "pqr_categoria",
                                "columna"=>"categoria",
                                "excepcion"=> $consulta_detalle_solicitud["categoria"]);
                                $categoria = new Pqr_Controlador();
                                $categoria -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Subcategoria</label>
                            <select id="reabrir_subcategoria" disabled class="form-control">';
                                $datos_formulario = array("tabla"=> "pqr_subcategoria",
                                "columna"=>"subcategoria",
                                "excepcion"=> $consulta_detalle_solicitud["subcategoria"]);
                                $subcategoria = new Pqr_Controlador();
                                $subcategoria -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Falla/Incidente</label>
                            <select id="reabrir_incidente" disabled class="form-control">';
                                $datos_formulario = array("tabla"=> "pqr_incidente",
                                "columna"=>"incidente",
                                "excepcion"=> $consulta_detalle_solicitud["falla"]);
                                $incidente = new Pqr_Controlador();
                                $incidente -> mostrar_formulario_con_excepcion_controlador($datos_formulario);
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Justificación de reapertura</label>
                            <textarea id="reabrir_justificacion" name="reabrir_justificacion" rows="10" cols="40" class="form-control" placeholder="Indique el causal de la reapertura del caso..." requiried></textarea>
                        </div>
                </form>
            </div>';
    }

    static public function reabrir_solicitud_controlador($datos){
        $datos_colaborador = Pqr_Modelo::consultar_correo_modelo("usuario", $datos["reabrir_id_asignado"]);
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        if($datos["reabrir_auditoria_usuario"] == 0){
            $mensaje_asignacion = 'La solicitud fue reabierta por el cliente y fue asignada por defecto al agente '.$datos_colaborador["nombres"].'.';
        }else{
            $mensaje_asignacion = 'La solicitud fue reabierta y asignada al colaborador '.$datos_colaborador["nombres"].'.';
        }
        $datos_base = array ("solicitud" => $datos["reabrir_id_solicitud"],
                        "mensaje" => $mensaje_asignacion,
                        "auditoria_creado" => $fecha_hoy,
                        "auditoria_usuario" => $datos["reabrir_auditoria_usuario"],
                        "tipo" => "1",
                        "archivo" => "");
        $registro_historico = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        $datos_asignacion = array ("id_solicitud" => $datos["reabrir_id_solicitud"],
                        "auditoria_usuario" => $datos["reabrir_auditoria_usuario"],
                        "colaborador" => $datos["reabrir_id_asignado"],
                        "prioridad" => $datos["reabrir_prioridad"],
                        "categoria" => $datos["reabrir_categoria"],
                        "subcategoria" => $datos["reabrir_subcategoria"],
                        "incidente" => $datos["reabrir_incidente"]);
        $asignar = Pqr_Modelo::asignar_solicitud_modelo("pqr_solicitud", $datos_asignacion);
        $consulta_reapertura = Pqr_Modelo::consulta_reapertura_solicitud_modelo("pqr_solicitud", $datos);
        $contador_reapertura = $consulta_reapertura["reabierto"] + 1;
        $respuesta = Pqr_Modelo::reabrir_solicitud_modelo("pqr_solicitud", $datos, $contador_reapertura);
        date_default_timezone_set("America/Bogota");
        $fecha_reapertura = date("Y-m-d H:i:s");
        $datos_registro_fecha = array ("solicitud" => $datos["reabrir_id_solicitud"],
                        "fecha_reapertura" => $fecha_reapertura,
                        "fecha_resuelto" => NULL,
                        "justificacion" => $datos["reabrir_justificacion"]);
        $registro_fecha_reapertura = Pqr_Modelo::registrar_fecha_reapertura_modelo("pqr_reabierto", $datos_registro_fecha);
        if($respuesta == "Ok"){
            $datos_pqr = Pqr_Modelo::consultar_datos_pqr_modelo("pqr_solicitud", $datos["reabrir_id_solicitud"]);
            $fecha_creado = $datos["reabrir_auditoria_creado"];
            $fecha_creado = date("d/m/Y");
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
                $mail->addAddress($datos_colaborador["correo"]);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = '¡Tienes una nueva solicitud! | Portal Solicitantes Mesa de Ayuda';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">¡Tienes una nueva solicitud!</h2>
                                    <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_colaborador["nombres"].',<br>Te ha sido asignada la solicitud número '.$datos_pqr["id"].', con tema de '.$datos_pqr["tema"].' a nombre del cliente '.$datos_pqr["cliente_nombres"].'.</h4>
                                    <a href="http://mesadeservicio.croydon.com.co/listado-solicitud" target="_blank" style="text-decoration:none"><div style="line-height:60px; background:#005574; width:60%; color:white">Ir al sitio</div></a>
                                    <br>Si el botón no funciona, copia y pega el enlace en tu navegador: http://mesadeservicio.croydon.com.co/listado-solicitud
                                    <h4 style="font-weight:100; padding:0 20px">¡Ya está disponible en tu listado de solicitudes!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
            $enviar_notificacion = Pqr_Controlador::consultar_permiso_envio_notificacion_controlador(2, $datos_pqr["cliente_id"]);
            if($enviar_notificacion == "Si"){
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
                    $mail->addAddress($datos_pqr["cliente_correo"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = '¡Reabrimos tu solicitud! | Portal Solicitantes Mesa de Ayuda';
                    $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                    <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                    <center>
                                        <hr style="border:1px solid #005574; width:80%">
                                        <h2 style="font-weight:700">¿Quedó algo pendiente?</h2>
                                        <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_pqr["cliente_nombres"].',<br>No te preocupes, reabrimos tu solicitud número '.$datos_pqr["id"].', con tema "'.$datos_pqr["tema"].'" y se asignó al colaborador: <br>"'.$datos_colaborador["nombres"].'"</h4>
                                        <h4 style="font-weight:100; padding:0 20px">¡Estamos contigo!</h4>
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
        echo $respuesta;
    }
    
    static public function historial_respuesta_controlador($datos){
        echo '<ul class="timeline">';
        $respuesta = Pqr_Modelo::fecha_historial_respuesta_modelo("pqr_respuesta", $datos["historial_respuesta_id_cliente"]);
        foreach ($respuesta as $row => $dia_calendario){
            $fecha_dia = date_create($dia_calendario["fecha"]);
            $dia = date_format($fecha_dia, "d / m / Y");
            echo '<li class="time-label">
                    <span class="bg-green">
                        '.$dia.'
                    </span>
                </li>';
            $consulta_respuesta = Pqr_Modelo::consulta_respuesta_modelo("pqr_respuesta", $datos["historial_respuesta_id_cliente"], $dia_calendario["fecha"]);
            foreach ($consulta_respuesta as $fila => $item) {
                if($item["auditoria_usuario"] != 0){
                    $datos_colaborador = Pqr_Modelo::consultar_correo_modelo("usuario", $item["auditoria_usuario"]);
                    $informador = $datos_colaborador["nombres"];
                }else {
                    $informador = "Cliente";
                }
                if($item["tipo"] == 1){
                    $fecha_dia = date_create($item["auditoria_creado"]);
                    $dia = date_format($fecha_dia, "d / m / Y");
                    $fecha_hora = date_create($item["auditoria_creado"]);
                    $hora = date_format($fecha_hora, "g:i A");
                    echo '<li>
                            <i class="fa fa-user bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"> <i class="fa fa-clock-o"></i> '.$hora.'</span>
                                <h3 class="timeline-header"><strong>'.$informador.'</strong></h3>
                                <div class="timeline-body">
                                    <p> <strong> Acción: </strong> </p> <cite>"'.$item["respuesta"].'"<cite>
                                </div>
                            </div>
                        </li>';
                }else if($item["tipo"] == 2){
                    $fecha_dia = date_create($item["auditoria_creado"]);
                    $dia = date_format($fecha_dia, "d / m / Y");
                    $fecha_hora = date_create($item["auditoria_creado"]);
                    $hora = date_format($fecha_hora, "g:i A");
                    if ($item["respuesta"] != "") {
                        echo '<li>
                                <i class="fa fa-user bg-aqua"></i>
                                <div class="timeline-item">
                                    <span class="time"> <i class="fa fa-clock-o"></i> '.$hora.'</span>
                                    <h3 class="timeline-header"><strong>'.$informador.'</strong></h3>
                                    <div class="timeline-body">
                                        <p> <strong> Respuesta: </strong> </p> <cite>"'.$item["respuesta"].'"<cite> <br>
                                    </div>
                                    <div class="timeline-footer">
                                        <div class="btn-group">';
                                        if ($item["archivo"] != "") {
                                            echo '<a class="btn btn-info btn-md" href="'.$_COOKIE["raiz"].'vista/img/pqr_respuesta/'.$item["archivo"].'" target="_blank" title="Archivo adjunto"><i class="far fa-file-image"></i></a>';
                                        }
                            echo '  </div>
                                </div>		
                            </div>
                        </li>';
                    }
                }
            }
        }			
        echo '<li>
                <i class="fa fa-hourglass-end bg-gray"></i>
            </li>
        </ul>';
    }

    static public function crear_nueva_solicitud_controlador($datos) {
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        $consulta = Pqr_Modelo::consulta_maximo_id_existente_modelo("pqr_solicitud");
        $id_solicitud = $consulta["maximo_identificador_existente"] + 1;
        for ($archivo = 2; $archivo <= 5; $archivo++) { 
            if(isset($_FILES["crear_solicitud_archivo_".$archivo]["tmp_name"])){
                $fecha_creado = $datos["crear_solicitud_auditoria_creado"];
                $remplazar = array(':', ' ');
                $fecha_creado = str_replace($remplazar, "-", $fecha_creado);
                $nombre_archivo = $datos["crear_solicitud_id_solicitante"].'-'.$fecha_creado.'-'.$archivo;
                $tipo_archivo = $_FILES["crear_solicitud_archivo_".$archivo]["type"];
                if ($tipo_archivo == "application/pdf"){
                    $nombre_archivo = $nombre_archivo.'.pdf';
                }else if ($tipo_archivo == "image/jpeg"){
                    $nombre_archivo = $nombre_archivo.".jpg";
                }else if ($tipo_archivo == "image/png"){
                    $nombre_archivo = $nombre_archivo.".png";
                }else if ($tipo_archivo == "application/x-prn"){
                    $nombre_archivo = $nombre_archivo.".prn";
                }else if ($tipo_archivo == "application/x-prn"){
                    $nombre_archivo = $nombre_archivo.".prn";
                }else if ($tipo_archivo == "text/plain"){
                    $nombre_archivo = $nombre_archivo.".txt";
                }else if ($tipo_archivo == "application/vnd.ms-excel"){
                    $nombre_archivo = $nombre_archivo.".xls";
                }else if ($tipo_archivo == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                    $nombre_archivo = $nombre_archivo.".xlsx";
                }else if ($tipo_archivo == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                    $nombre_archivo = $nombre_archivo.".docx";
                }else if ($tipo_archivo == "application/msword"){
                    $nombre_archivo = $nombre_archivo.".doc";
                }
                move_uploaded_file($_FILES["crear_solicitud_archivo_".$archivo]["tmp_name"],"../img/pqr_solicitud/".$nombre_archivo);
                $crear_archivo_adicional = Pqr_Modelo::crear_solicitud_registro_archivo_adicional_modelo("pqr_solicitud_archivo", $nombre_archivo, $id_solicitud);
            }
        }
        if(isset($_FILES["crear_solicitud_archivo"]["tmp_name"])){
            $fecha_creado = $datos["crear_solicitud_auditoria_creado"];
            $remplazar = array(':', ' ');
            $fecha_creado = str_replace($remplazar, "-", $fecha_creado);
            $nombre_archivo = $datos["crear_solicitud_id_solicitante"].'-'.$fecha_creado;
            $tipo_archivo = $_FILES["crear_solicitud_archivo"]["type"];
            if ($tipo_archivo == "application/pdf"){
                $nombre_archivo = $nombre_archivo.'.pdf';
            }else if ($tipo_archivo == "image/jpeg"){
                $nombre_archivo = $nombre_archivo.".jpg";
            }else if ($tipo_archivo == "image/png"){
                $nombre_archivo = $nombre_archivo.".png";
            }else if ($tipo_archivo == "application/x-prn"){
                $nombre_archivo = $nombre_archivo.".prn";
            }else if ($tipo_archivo == "application/x-prn"){
                $nombre_archivo = $nombre_archivo.".prn";
            }else if ($tipo_archivo == "text/plain"){
                $nombre_archivo = $nombre_archivo.".txt";
            }else if ($tipo_archivo == "application/vnd.ms-excel"){
                $nombre_archivo = $nombre_archivo.".xls";
            }else if ($tipo_archivo == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                $nombre_archivo = $nombre_archivo.".xlsx";
            }else if ($tipo_archivo == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                $nombre_archivo = $nombre_archivo.".docx";
            }else if ($tipo_archivo == "application/msword"){
                $nombre_archivo = $nombre_archivo.".doc";
            }
            move_uploaded_file($_FILES["crear_solicitud_archivo"]["tmp_name"],"../img/pqr_solicitud/".$nombre_archivo);
        }else{
			$nombre_archivo = "";
		}
        $consulta_cliente = Pqr_Modelo::consulta_cliente_modelo("cliente", $datos["crear_solicitud_id_solicitante"]);
        if($datos["crear_solicitud_tipo_registro"] == "cliente"){
            $estado = 1;
        }elseif($datos["crear_solicitud_tipo_registro"] == "agente"){
            $estado = 2;
        }
        if($datos["crear_solicitud_auditoria_usuario"] == " " || $datos["crear_solicitud_auditoria_usuario"] == NULL){
            $auditoria_usuario_validado = 0;
        }else {
            $auditoria_usuario_validado = $datos["crear_solicitud_auditoria_usuario"];
        }
        $datos_crear_pqr = array ("crear_solicitud_asunto" => $datos["crear_solicitud_asunto"],
            "crear_solicitud_mensaje" => $datos["crear_solicitud_mensaje"],
            "crear_solicitud_id_solicitante" => $datos["crear_solicitud_id_solicitante"],
            "crear_solicitud_auditoria_creado" => $fecha_hoy,
            "crear_solicitud_tipo_registro" => $datos["crear_solicitud_tipo_registro"],
            "crear_solicitud_auditoria_usuario" => $auditoria_usuario_validado,
            "crear_solicitud_prioridad" => $datos["crear_solicitud_prioridad"],
            "crear_solicitud_categoria" => $datos["crear_solicitud_categoria"],
            "crear_solicitud_subcategoria" => $datos["crear_solicitud_subcategoria"],
            "crear_solicitud_incidente" => $datos["crear_solicitud_incidente"],
            "crear_solicitud_id_asignado" => $datos["crear_solicitud_id_asignado"],
            "crear_solicitud_archivo" => $nombre_archivo,
            "crear_solicitud_area" => $consulta_cliente["area"],
            "crear_solicitud_estado" => $estado);
        $respuesta = Pqr_Modelo:: crear_nueva_solicitud_modelo("pqr_solicitud", $datos_crear_pqr);
        if($datos["crear_solicitud_tipo_registro"] == "cliente"){
            $mensaje_asignacion = 'El caso fue registrado por el cliente.';
            $correo_cuerpo_mensaje = 'Hola '.$consulta_cliente["nombres"].',<br>No te preocupes, ya recibimos tu caso número '.$id_solicitud.', con tema "'.$datos["crear_solicitud_asunto"].'".<br>Te informaremos cuando sea asignado a un colaborador.';
            
        }else{
            $datos_informador = Pqr_Modelo::consultar_correo_modelo("usuario", $datos["crear_solicitud_auditoria_usuario"]);
            $datos_colaborador = Pqr_Modelo::consultar_correo_modelo("usuario", $datos["crear_solicitud_id_asignado"]);
            $mensaje_asignacion = 'El caso fue registrado por '.$datos_informador["nombres"].' y fue asignado al colaborador '.$datos_colaborador["nombres"].'.';
            $correo_cuerpo_mensaje = 'Hola '.$consulta_cliente["nombres"].',<br>No te preocupes, ya recibimos tu caso número '.$id_solicitud.', con tema "'.$datos["crear_solicitud_asunto"].'" y se asignó al colaborador: <br>"'.$datos_colaborador["nombres"].'".';
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
                $mail->addAddress($datos_colaborador["correo"]);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = '¡Tienes una nueva solicitud! | Portal Usuarios Croydon';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">¡Tienes una nueva solicitud!</h2>
                                    <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_colaborador["nombres"].',<br>Te ha sido asignada una nueva solicitud con el motivo de '.$datos["crear_solicitud_asunto"].' a nombre del cliente '.$consulta_cliente["nombres"].'.</h4>
                                    <a href="http://mesadeservicio.croydon.com.co/listado-solicitud" target="_blank" style="text-decoration:none"><div style="line-height:60px; background:#005574; width:60%; color:white">Ir al sitio</div></a>
                                    <br>Si el botón no funciona, copia y pega el enlace en tu navegador: http://mesadeservicio.croydon.com.co/listado-solicitud
                                    <h4 style="font-weight:100; padding:0 20px">¡Ya está disponible en tu listado de solicitudes!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
        }
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        $datos_base = array ("solicitud" => $id_solicitud,
                "mensaje" => $mensaje_asignacion,
                "auditoria_creado" => $fecha_hoy,
                "auditoria_usuario" => $datos["crear_solicitud_auditoria_usuario"],
                "tipo" => "1",
                "archivo" => "");
        $registro_asignacion = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        $enviar_notificacion = Pqr_Controlador::consultar_permiso_envio_notificacion_controlador(1, $datos["crear_solicitud_id_solicitante"]);
        if($respuesta == "Ok" && $enviar_notificacion == "Si"){
            if($consulta_cliente["correo"] != ""){
                $fecha_creado = $fecha_hoy;
                $fecha_creado = date("d/m/Y");
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
                    $mail->addAddress($consulta_cliente["correo"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Creaste un caso | Portal Solicitantes Mesa de Ayuda';
                    $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                    <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                    <center>
                                        <hr style="border:1px solid #005574; width:80%">
                                        <h2 style="font-weight:700">¿Necesitas nuestra ayuda?</h2>
                                        <h4 style="font-weight:100; padding:0 20px">'.$correo_cuerpo_mensaje.'</h4>
                                        <h4 style="font-weight:100; padding:0 20px">¡Estamos contigo!</h4>
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
        echo $respuesta;
    }

    static public function historial_respuesta_pqr_controlador($datos){
        $respuesta = Pqr_Modelo::consulta_respuesta_solicitud_modelo("pqr_respuesta", $datos['historial_respuesta_soporte_id_solicitud']);
        echo '<div class="table-responsive">
                <table class="table no-margin">
                    <thead>
                        <tr>
                            <th>Respuesta</th>
                            <th style="text-align: center;">Archivo</th>
                            <th style="text-align: center;">Fecha de contestado</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($respuesta as $fila => $item) {
                if($item["archivo"] == NULL){
                    $boton_archivo = 'Sin adjunto';
                }else{
                    $boton_archivo = '<a class="btn btn-info" href="vista/img/pqr_respuesta/'.$item["archivo"].'" title="Imagen" target="_blank"><i class="far fa-file-image"></i></a>';
                }
                $fecha_creado = date_create($item["auditoria_creado"]);
                $hora_creado = date_format($fecha_creado, "g:i:A");
                $fecha_creado = date_format($fecha_creado, "d / m / Y");
                echo '
                    <tr>
                        <td style="text-align: left;">'.$item["respuesta"].'</td>
                        <td>'.$boton_archivo.'</td>
                        <td>'.$fecha_creado.' a las '.$hora_creado.'</td>
                    </tr>';
            }
            echo '</tbody>
                </table>
            </div>';
    }

    static public function listado_solicitud_asignada_controlador(){
        $fecha_servidor = $GLOBALS["fecha"];
        $respuesta = Pqr_Modelo::fecha_listado_solicitud_asignada_modelo("pqr_solicitud", $_SESSION["id"]);
        foreach ($respuesta as $row => $dia_calendario){
        $fecha_dia = date_create($dia_calendario["fecha_solicitud"]);
        $dia = date_format($fecha_dia, "d / m / Y");
        echo '<li class="time-label">
                <span class="bg-green">
                    '.$dia.'
                </span>
            </li>';
        $consulta_solicitud = Pqr_Modelo::descripcion_listado_solicitud_asignada_modelo("pqr_solicitud", $dia_calendario["fecha_solicitud"], $_SESSION["id"]);
            foreach ($consulta_solicitud as $fila => $item) {
                $fecha_hora = date_create($item["auditoria_creado"]);
                $hora = date_format($fecha_hora, "g:i:s");
                if($item["prioridad"] == "Baja"){
                    $prioridad = 'alert-info';
                }else if($item["prioridad"] == "Media"){
                    $prioridad = 'alert-info';
                }else if($item["prioridad"] == "Alta"){
                    $prioridad = 'alert-warning';
                }else if($item["prioridad"] == "Critica"){
                    $prioridad = 'alert-danger';
                } 
                $bloqueo_botones = "";
                $mensaje_en_espera = "";
                if($item["estado"] == "En espera"){
                    $bloqueo_botones = "disabled";
                    $mensaje_en_espera = '<h4>Esta solicitud esta en estado de espera, comunicate con el adiminstrador para cambiar su estado</h4>';
                }
                if($item["archivo"] != NULL){
                    $boton_archivo = "<button type='button' formulario_archivo_adjunto_solicitud='".$item["identificador"]."' class='btn btn-primary btn-sm formulario-adjunto-solicitud' title='Archivos ajuntos' data-toggle='modal' data-target='#modal_adjunto_solicitud'><i class='fas fa-folder-open'></i></button>";
                }else{
                    $boton_archivo = "";
                }
                echo '<li>
                    <i class="fa fa-bell '.$prioridad.'"></i>
                    <div class="timeline-item">
                        <span class="time '.$prioridad.'"> <i class="fa fa-clock-o"></i> '.$hora.'</span>
                        <h3 class="timeline-header '.$prioridad.'"> <strong> '.$item["tema"].'</strong> <small class="'.$prioridad.'">'.strtoupper($item["categoria"]).' - '.strtoupper($item["subcategoria"]).' - '.strtoupper($item["falla"]).' - '.strtoupper($item["prioridad"]).'</small> </h3>
                        <div class="timeline-body">
                            <cite> "'.$item["mensaje"].'" </cite> <b>('.$item["cliente_nombres"].' - '.$item["cliente_area"].')</b> <br>';
                            if($item["cliente_correo"] != ""){
                                echo '<small> Contacto: '.$item["cliente_telefono"].' - Correo: '.$item["cliente_correo"].' </small> <br>';
                            }
                        $consulta_respuesta = Pqr_Modelo::consulta_caso_respuesta_modelo("pqr_respuesta", $item["identificador"]);
                        $botones_solicitud = "";
                        // <button '.$bloqueo_botones.' type="button" formulario_cambiar_estado_solicitud_soporte_estado="6" formulario_cambiar_estado_solicitud_soporte_id="'.$item["identificador"].'" formulario_cambiar_estado_solicitud_soporte_auditoria_usuario="'.$_SESSION["id"].'" formulario_cambiar_estado_solicitud_soporte_fecha="'.$GLOBALS["fecha"].'" class="btn btn-warning btn-sm cambiar-estado-solicitud" title="Cambiar estado a En espera" data-toggle="modal" data-target="#modal_cambiar_estado_solicitud_soporte"><i class="fa fa-pause"></i></button>
                        if(count($consulta_respuesta) >= 2){
                            $botones_solicitud = '<button '.$bloqueo_botones.' type="button" formulario_cambiar_estado_solicitud_soporte_estado="4" formulario_cambiar_estado_solicitud_soporte_id="'.$item["identificador"].'" formulario_cambiar_estado_solicitud_soporte_auditoria_usuario="'.$_SESSION["id"].'" formulario_cambiar_estado_solicitud_soporte_fecha="'.$GLOBALS["fecha"].'" class="btn btn-info btn-sm cambiar-estado-solicitud" title="Cambiar estado a resuelto" data-toggle="modal" data-target="#modal_cambiar_estado_solicitud_soporte"><i class="fa fa-check-circle"></i></button>';
                        }
                        if($item["informador"] != 0){
                            $datos_colaborador = Pqr_Modelo::consultar_correo_modelo("usuario", $item["informador"]);
                            $informador = $datos_colaborador["nombres"];
                        }else {
                            $informador = "Cliente";
                        }
                        echo $mensaje_en_espera.'
                             <small> Informador: '.$informador.' </small> <br>
                             <small> ID solicitud: '.$item["identificador"].' </small> <br>
                        </div>
                        <div class="timeline-footer">
                            <div class="btn-group">
                                <button '.$bloqueo_botones.' type="button" formulario_respuesta_id_solicitud="'.$item["identificador"].'" formulario_respuesta_auditoria_usuario="'.$_SESSION["id"].'" formulario_respuesta_auditoria_creado="'.$fecha_servidor.'" formulario_respuesta_id_archivo="'.$item["archivo"].'" formulario_respuesta_raiz_archivo="'.$GLOBALS["raiz"].'" class="btn btn-success btn-sm respuesta-solicitud-soporte" title="Responder" data-toggle="modal" data-target="#modal_responder_solicitud_soporte"><i class="fas fa-reply-all"></i></button>
                                '.$botones_solicitud.$boton_archivo;
                                if($_SESSION["rol"] == 1 || $_SESSION["rol"] == 4){
                                    echo '<button type="button" formulario_asignar_auditoria_creado="'.$fecha_servidor.'" formulario_asignar_id_solicitud="'.$item["identificador"].'" formulario_asignar_auditoria_usuario="'.$_SESSION["id"].'" class="btn btn-warning btn-sm asignar-solicitud-pqr" title="Re-asignar" data-toggle="modal" data-target="#modal_asignar_solicitud"><i class="fas fa-people-carry"></i></button>';
                                }
                        echo'</div>
                        </div>
                    </div>
                </li>';
                foreach ($consulta_respuesta as $fila => $item) {
                    $fecha_dia = date_create($item["auditoria_creado"]);
                    $dia = date_format($fecha_dia, "d / m / Y");
                    $fecha_hora = date_create($item["auditoria_creado"]);
                    $hora = date_format($fecha_hora, "g:i A");
                    if($item["empleado"] != 0){
                        $datos_colaborador = Pqr_Modelo::consultar_correo_modelo("usuario", $item["empleado"]);
                        $informador = $datos_colaborador["nombres"];
                    }else {
                        $informador = "Cliente";
                    }
                    if($item["tipo"] == 2){
                        echo '<li>
                            <i class="fa fa-user bg-aqua"></i>
                            <div class="timeline-item">
                                <span class="time"> <i class="fa fa-calendar-check"></i>  '.$dia.' <i class="fa fa-clock-o"></i> '.$hora.'</span>
                                <h3 class="timeline-header"><strong>'.$informador.'</strong></h3>
                                <div class="timeline-body">
                                    <cite>"'.$item["respuesta"].'"<cite>
                                </div>
                                <div class="timeline-footer">';
                                    if ($item["archivo"] != "") {
                                        echo 'Archivo adjunto:
                                        <div class="btn-group">
                                            <a class="btn btn-info btn-xs" href="vista/img/pqr_respuesta/'.$item["archivo"].'" target="_blank"><i class="far fa-file-image"></i></a>
                                        </div>';
                                    }
                                echo '
                                </div>		
                            </div>
                        </li>';
                    }else{
                        echo '<li>
                            <i class="fa fa-user bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"> <i class="fa fa-calendar-check"></i>  '.$dia.' <i class="fa fa-clock-o"></i> '.$hora.'</span>
                                <h3 class="timeline-header"><strong>'.$informador.'</strong></h3>
                                <div class="timeline-body">
                                    <cite>"'.$item["respuesta"].'"<cite>
                                </div>
                            </div>
                        </li>';
                    }
                }
            }
        }
    }
    
    static public function formulario_asignar_solicitud_controlador($datos){
        $consulta_detalle_solicitud = Pqr_Modelo::consulta_detalle_solicitud_modelo("pqr_solicitud", $datos["formulario_asignar_id_solicitud"]);
        $consulta_detalle_cliente = Pqr_Modelo::consulta_cliente_modelo("cliente", $consulta_detalle_solicitud["cliente"]);
        echo '<div class="box-body>
                <form method="POST" class="form-horizontal">
                    <div class="form-group has-feedback">
                        <input type="hidden" id="asignar_id_solicitud" value="'.$datos["formulario_asignar_id_solicitud"].'">
                        <input type="hidden" id="asignar_auditoria_creado" value="'.$datos["formulario_asignar_auditoria_creado"].'">
                        <input type="hidden" id="asignar_auditoria_usuario" value="'.$datos["formulario_asignar_auditoria_usuario"].'">
                        <label class="control-label">¿A quien vas a asignar?</label>
                        <select id="asignar_colaborador" class="form-control">';
                            $datos_formulario = array("tabla"=> "usuario",
                                    "columna"=>"nombres",
                                    "excepcion"=> $consulta_detalle_solicitud["asignado"]);
                            $funcionario = new Pqr_Controlador();
                            $funcionario -> mostrar_formulario_con_excepcion_ordenado_controlador($datos_formulario);
                   echo'</select>
                   </div>';
                   if($consulta_detalle_solicitud["prioridad"] == 0 && $consulta_detalle_solicitud["categoria"] == 0 && $consulta_detalle_solicitud["subcategoria"] == 0 && $consulta_detalle_solicitud["falla"] == 0){
                        echo ' <div class="form-group has-feedback">
                            <label class="control-label">Prioridad</label>
                            <select id="asignar_prioridad" class="form-control">';
                                $palabras_nombre_cargo = explode(" ", $consulta_detalle_cliente["cargo"]);
                                $cargo = $palabras_nombre_cargo[0];
                                if($consulta_detalle_cliente["area"] == 11100){
                                    echo '<option value="3"> Critica - PRESIDENCIA </option>';
                                }elseif($consulta_detalle_cliente["vip"] == "SI"){
                                    echo '<option value="2"> Alta - CLIENTE VIP </option>';
                                }elseif($cargo == "DIRECTOR" || $cargo == "DIREC."){
                                    echo '<option value="3"> Critica - DIRECTOR </option>';
                                }else{
                                    echo '<option value=""> Escoja una opción </option>';
                                    $prioridad = new Pqr_Controlador();
                                    $prioridad -> formulario_pqr_controlador("pqr_prioridad");
                                }
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Categoria</label>
                            <select id="asignar_categoria" class="form-control">
                                <option value=""> Escoja una opción </option>';
                                    $categoria = new Pqr_Controlador();
                                    $categoria -> formulario_pqr_controlador("pqr_categoria");
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Subcategoria</label>
                            <select id="asignar_subcategoria" class="form-control">
                                <option value=""> Escoja una opción </option>';
                                    $subcategoria = new Pqr_Controlador();
                                    $subcategoria -> formulario_pqr_controlador("pqr_subcategoria");
                        echo'</select>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Falla</label>
                            <select id="asignar_incidente" class="form-control">
                            </select>
                        </div>';
                    }else{
                        echo '<div class="form-group has-feedback">
                            <input type="hidden" id="asignar_prioridad" value="'.$consulta_detalle_solicitud["prioridad"].'">
                            <input type="hidden" id="asignar_categoria" value="'.$consulta_detalle_solicitud["categoria"].'">
                            <input type="hidden" id="asignar_subcategoria" value="'.$consulta_detalle_solicitud["subcategoria"].'">
                            <input type="hidden" id="asignar_incidente" value="'.$consulta_detalle_solicitud["falla"].'">
                        </div>';
                    }
			echo'</form>
            </div>';
    }

    static public function asignar_solicitud_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        $consultar_colaborador_anterior = Pqr_Modelo::consultar_datos_pqr_modelo("pqr_solicitud", $datos["asignar_id_solicitud"]);
        if($consultar_colaborador_anterior == false){
            $consultar_colaborador_anterior = Pqr_Modelo::consultar_pqr_no_asignado_modelo("pqr_solicitud", $datos["asignar_id_solicitud"]);
        }
        $datos_colaborador_anterior = Pqr_Modelo::consultar_correo_modelo("usuario", $consultar_colaborador_anterior["asignado"]);
        $datos_colaborador_asignado = Pqr_Modelo::consultar_correo_modelo("usuario", $datos["asignar_colaborador"]);
        $mensaje_asignacion = 'La solicitud fue asignada al colaborador '.$datos_colaborador_asignado["nombres"].'.';
        $datos_base = array ("solicitud" => $datos["asignar_id_solicitud"],
                        "mensaje" => $mensaje_asignacion,
                        "auditoria_creado" => $fecha_hoy,
                        "auditoria_usuario" => $datos["asignar_auditoria_usuario"],
                        "tipo" => "1",
                        "archivo" => "");
        $registro_asignacion = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        $datos_asignacion = array ("id_solicitud" => $datos["asignar_id_solicitud"],
                        "auditoria_usuario" => $datos["asignar_auditoria_usuario"],
                        "colaborador" => $datos["asignar_colaborador"],
                        "prioridad" => $datos["asignar_prioridad"],
                        "categoria" => $datos["asignar_categoria"],
                        "subcategoria" => $datos["asignar_subcategoria"],
                        "incidente" => $datos["asignar_incidente"]);
        $respuesta = Pqr_Modelo::asignar_solicitud_modelo("pqr_solicitud", $datos_asignacion);
        if($respuesta == "Ok"){
            $fecha_creado = date_create($datos["asignar_auditoria_creado"]);
            $fecha_creado = date("d/m/Y");
            $datos_cliente = Pqr_Modelo::consultar_correo_cliente_modelo("cliente", $consultar_colaborador_anterior["cliente"]);
            // if($datos_colaborador_anterior["correo"] != "" || $datos_colaborador_anterior["correo"] != NULL || $datos_colaborador_anterior != false){
            if($datos_colaborador_anterior != NULL){
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
                    $mail->addAddress($datos_colaborador_anterior["correo"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = '¡Tu solicitud fue reasignada! | Portal Solicitantes Mesa de Ayuda';
                    $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                    <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                    <center>
                                        <hr style="border:1px solid #005574; width:80%">
                                        <h2 style="font-weight:700">¡Tu solicitud fue reasignada!</h2>
                                        <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_colaborador_anterior["nombres"].',<br>La solicitud número '.$datos["asignar_id_solicitud"].', con el motivo de '.$consultar_colaborador_anterior["tema"].' que perteneciente a '.$datos_cliente["nombres"].' fue reasignada.</h4>
                                        <h4 style="font-weight:100; padding:0 20px">¡Ya no la veras más en tu listado de solicitudes!</h4>
                                        <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                    </center>
                                    </div>
                                </div>';
                    $mail->send();
                } catch (Exception $e) {
                    echo 'Ha ocurrido un error inesperado!';
                }
            }
            $enviar_notificacion = Pqr_Controlador::consultar_permiso_envio_notificacion_controlador(3, $consultar_colaborador_anterior["cliente"]);
            if($enviar_notificacion == "Si"){
                if($datos_cliente["correo"] != "" || $datos_cliente["correo"] != NULL){
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
                        $mail->addAddress($datos_cliente["correo"]);
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = 'Tu caso ha sido asignado | Portal Solicitantes Mesa de Ayuda';
                        $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                        <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                        <center>
                                            <hr style="border:1px solid #005574; width:80%">
                                            <h2 style="font-weight:700">¡Asignamos tu caso!</h2>
                                            <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_cliente["nombres"].',<br>No te preocupes, ya asignamos tu solicitud "'.$consultar_colaborador_anterior["tema"].'" al colaborador:<br>'.$datos_colaborador_asignado["nombres"].'</h4>
                                            <h4 style="font-weight:100; padding:0 20px">¡Pronto te contactaremos!</h4>
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
                $mail->addAddress($datos_colaborador_asignado["correo"]);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = '¡Tienes una nueva solicitud! | Portal Usuarios Croydon';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">¡Tienes una nueva solicitud!</h2>
                                    <h4 style="font-weight:100; padding:0 20px">Hola '.$datos_colaborador_asignado["nombres"].',<br>Te ha sido asignada la solicitud número '.$datos["asignar_id_solicitud"].', con el motivo de "'.$consultar_colaborador_anterior["tema"].'" a nombre del cliente '.$datos_cliente["nombres"].'.</h4>
                                    <a href="http://mesadeservicio.croydon.com.co/listado-solicitud" target="_blank" style="text-decoration:none"><div style="line-height:60px; background:#005574; width:60%; color:white">Ir al sitio</div></a>
                                    <br>Si el botón no funciona, copia y pega el enlace en tu navegador: http://mesadeservicio.croydon.com.co/listado-solicitud
                                    <h4 style="font-weight:100; padding:0 20px">¡Ya está disponible en tu listado de solicitudes!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
        }
        echo $respuesta;
    }
        
    static public function formulario_respuesta_solicitud_controlador($datos){
        $formulario_respuesta_raiz_archivo = $datos["formulario_respuesta_raiz_archivo"];
        $formulario_respuesta_auditoria_creado = $datos["formulario_respuesta_auditoria_creado"];
        $formulario_respuesta_auditoria_usuario = $datos["formulario_respuesta_auditoria_usuario"];
        $formulario_respuesta_id_solicitud = $datos["formulario_respuesta_id_solicitud"];
        $formulario_respuesta_id_archivo = $datos["formulario_respuesta_id_archivo"];
        $consulta = Pqr_Modelo::consulta_detalle_solicitud_modelo("pqr_solicitud", $formulario_respuesta_id_solicitud);
        echo '<div class="box-body">
                <form method="POST">
                    <div class="form-group has-feedback">';
                        if ($formulario_respuesta_id_archivo != "") {
                            echo '<label class="control-label">Adjunto:</label>
                            <a class="btn btn-info" href="'.$_COOKIE["raiz"].'vista/img/pqr_solicitud/'.$formulario_respuesta_id_archivo.'" title="Imagen" target="_blank"><i class="far fa-file-image"></i></a>';
                        }
                    echo '</div>';
                    if ($consulta["fecha_estimada_resuelto"] == NULL) {
                        echo '
                            <div class="alert alert-danger text-center">
                                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                                <h5>Ingrese fecha y hora en la que estima dar el caso por resuelto</h5>
                                <div class="form-group has-feedback">
                                    <label class="control-label">Fecha</label>
                                    <input class="form-control" type="date" id="respuesta_fecha_estimada_resuelto">
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label">Hora</label>
                                    <input class="form-control" type="time" id="respuesta_hora_estimada_resuelto">
                                </div>
                            </div>
                                ';
                    }else{
                        echo ' <div class="form-group has-feedback">
                                    <input type="hidden" id="respuesta_fecha_estimada_resuelto" value="'.$formulario_respuesta_auditoria_creado.'">
                                    <input type="hidden" id="respuesta_hora_estimada_resuelto" value="'.$formulario_respuesta_auditoria_creado.'">
                                </div>';
                    }
                echo'<div class="form-group has-feedback">
                        <label class="control-label">Respuesta</label>
                        <textarea id="respuesta_mensaje" name="mensaje" rows="10" cols="40" class="form-control" placeholder="No olvide indicar su diagnostico y la solución..." requiried>Nombre Equipo:

Diagnostico:

Solución:</textarea>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Archivo</label>
                        <input type="file" id="respuesta_archivo" name="archivo">
                        <p class="help-block">Tamaño recomendado 800px * 600px</p>
                        <input type="hidden" id="respuesta_id_solicitud" value="'.$formulario_respuesta_id_solicitud.'">
                        <input type="hidden" id="respuesta_auditoria_usuario" value="'.$formulario_respuesta_auditoria_usuario.'">
                        <input type="hidden" id="respuesta_auditoria_creado" value="'.$formulario_respuesta_auditoria_creado.'">
                    </div>
                </form>
            </div>';
    }
        
    static public function respuesta_solicitud_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        if(isset($_FILES["respuesta_archivo"]["tmp_name"])){
            $fecha_creado = $datos["respuesta_auditoria_creado"];
            $remplazar = array(':', ' ');
            $fecha_creado = str_replace($remplazar, "-", $fecha_creado);
            $respuesta_nombre_archivo = $datos["respuesta_id_solicitud"].'-'.$fecha_creado;
            $tipo_archivo = $_FILES["respuesta_archivo"]["type"];
            if ($tipo_archivo == "application/pdf"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.'.pdf';
            }else if ($tipo_archivo == "image/jpeg"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".jpg";
            }else if ($tipo_archivo == "image/png"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".png";
            }else if ($tipo_archivo == "application/x-prn"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".prn";
            }else if ($tipo_archivo == "text/plain"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".txt";
            }else if ($tipo_archivo == "application/vnd.ms-excel"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".xls";
            }else if ($tipo_archivo == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".xlsx";
            }else if ($tipo_archivo == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".docx";
            }else if ($tipo_archivo == "application/msword"){
                $respuesta_nombre_archivo = $respuesta_nombre_archivo.".doc";
            }
            move_uploaded_file($_FILES["respuesta_archivo"]["tmp_name"],"../img/pqr_respuesta/".$respuesta_nombre_archivo);
        }else{
			$respuesta_nombre_archivo = "";
		}
        $datos_base = array ("solicitud" => $datos["respuesta_id_solicitud"],
                        "mensaje" => $datos["respuesta_mensaje"],
                        "auditoria_creado" => $fecha_hoy,
                        "auditoria_usuario" => $datos["respuesta_auditoria_usuario"],
                        "archivo" => $respuesta_nombre_archivo,
                        "tipo" => "2");
        $respuesta = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        $fecha_creado = $datos["respuesta_auditoria_creado"];
        $fecha_creado = date("d/m/Y");
        if($respuesta == "Ok"){
            $consulta_pqr = Pqr_Modelo::consultar_datos_pqr_modelo("pqr_solicitud", $datos["respuesta_id_solicitud"]);
            if($consulta_pqr["fecha_estimada_resuelto"] == NULL){
                $fecha_estimado = $datos["respuesta_fecha_estimada_resuelto"].' '.$datos["respuesta_hora_estimada_resuelto"].':00';
                $registrar_fecha_estimado = Pqr_Modelo::registrar_fecha_estimada_resuelto_modelo("pqr_solicitud",  $fecha_estimado, $datos["respuesta_id_solicitud"]);
                $fecha_estimada = date_create($fecha_estimado);
                $hora_estiamda = date_format($fecha_estimada, "H:iA");
                $fecha_estimada = date_format($fecha_estimada, "d/m/Y");
                $mensaje_respuesta = "El agente ha estimado el tiempo de resolución de este caso para el ".$fecha_estimada.' a las '.$hora_estiamda.'.';
                $datos_base = array ("solicitud" => $datos["respuesta_id_solicitud"],
                    "mensaje" => $mensaje_respuesta,
                    "auditoria_creado" => $fecha_hoy,
                    "auditoria_usuario" => $datos["respuesta_auditoria_usuario"],
                    "archivo" => "",
                    "tipo" => "1");
                $respuesta = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
            }
            $enviar_notificacion = Pqr_Controlador::consultar_permiso_envio_notificacion_controlador(4, $consulta_pqr["cliente_id"]);
            if($enviar_notificacion == "Si"){
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
                    $mail->addAddress($consulta_pqr["cliente_correo"]);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = '¡Tienes una respuesta! | Portal Solicitantes Mesa de Ayuda';
                    $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                    <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                    <center>
                                        <hr style="border:1px solid #005574; width:80%">
                                        <h2 style="font-weight:700">¿Necesitas nuestra ayuda?</h2>
                                        <h4 style="font-weight:100; padding:0 20px">Hola '.$consulta_pqr["cliente_nombres"].',<br>No te preocupes, tenemos una respuesta para tu solicitud número '.$datos["respuesta_id_solicitud"].' con tema "'.$consulta_pqr["tema"].'":</h4>
                                        <h4 style="font-weight:100; padding:0 20px">'.$datos["respuesta_mensaje"].'</h4>
                                        <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                    </center>
                                    </div>
                                </div>';
                    if($respuesta_nombre_archivo != ""){
                        $mail->AddAttachment("../img/pqr_respuesta/$respuesta_nombre_archivo");
                    }
                    $mail->send();
                } catch (Exception $e) {
                    echo 'Ha ocurrido un error inesperado!';
                }
            }
        }
        echo $respuesta;
    }
    
    static public function formulario_cambiar_estado_solicitud_soporte_controlador($datos){
        if($datos["formulario_cambiar_estado_solicitud_soporte_estado"] == 4){
            $color_alerta = "info";
            $mensaje_principal_alerta = 'Al cambiar de estado este caso a "Resuelto" se enviará un correo al cliente indicando que ya fue atendida su solicitud y al administrador solicitando que cambie el estado del caso a cerrado.';
            $mensaje_secundario_alerta = 'Recuerda que para volver a verla en tu programación deberas solicitar al administrador que cambie el estado a "En ejecución".';
        }elseif($datos["formulario_cambiar_estado_solicitud_soporte_estado"] == 3){
            $color_alerta = "success";
            $mensaje_principal_alerta = 'Al reactivar esta caso se enviará un correo al cliente indicando que su caso número '.$datos["formulario_cambiar_estado_solicitud_soporte_id"].' ha sido reactivado y al colaborador que lo tenga asignado le será habilitada la opción de responder.';
            $mensaje_secundario_alerta = 'Recuerda que para volver a contestar este caso deberas solicitar al administrador que cambie el estado a "En ejecución".';
        }elseif($datos["formulario_cambiar_estado_solicitud_soporte_estado"] == 6){
            $color_alerta = "warning";
            $mensaje_principal_alerta = 'Al cambiar de estado este caso a "En espera" se enviará un correo al cliente indicando que su caso quedo en espera por el motivo mencionado en la respuesta anterior (Debes mencionar el motivo en una respuesta antes de cambiar a este estado para que se vea la trasabilidad).';
            $mensaje_secundario_alerta = 'Recuerda que para volver a contestar este caso deberas solicitar al administrador que cambie el estado a "En ejecución".';
        }elseif($datos["formulario_cambiar_estado_solicitud_soporte_estado"] == 7){
            $color_alerta = "danger";
            $mensaje_principal_alerta = 'Al cambiar de estado este caso a "Cancelado" se enviará un correo al cliente indicando que su caso fue cancelado por el motivo mencionado en la respuesta anterior (Debes mencionar el motivo en una respuesta antes de cambiar a este estado para que se vea la trasabilidad).';
            $mensaje_secundario_alerta = '';
        }
        echo '<div class="alert alert-'.$color_alerta.' text-center">
                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                <h5>'.$mensaje_principal_alerta.'</h5>
                <h6>'.$mensaje_secundario_alerta.'</h6>
                <form method="post" class="form-horizontal">
                    <input type="hidden" id="cambiar_estado_solicitud_soporte_estado" value="'.$datos["formulario_cambiar_estado_solicitud_soporte_estado"].'">
                    <input type="hidden" id="cambiar_estado_solicitud_soporte_id" value="'.$datos["formulario_cambiar_estado_solicitud_soporte_id"].'">
                    <input type="hidden" id="cambiar_estado_solicitud_soporte_auditoria_usuario" value="'.$datos["formulario_cambiar_estado_solicitud_soporte_auditoria_usuario"].'">
                    <input type="hidden" id="cambiar_estado_solicitud_soporte_fecha" value="'.$datos["formulario_cambiar_estado_solicitud_soporte_fecha"].'">
                </form>
            </div>';
    }

    static public function cambiar_estado_solicitud_soporte_controlador($datos){
        $consulta_pqr = Pqr_Modelo::consultar_datos_pqr_modelo("pqr_solicitud", $datos["cambiar_estado_solicitud_soporte_id"]);
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        if($datos["cambiar_estado_solicitud_soporte_estado"] == "4"){
            $mensaje_asignacion = 'El estado fue cambiado a Resuelto';
        }elseif($datos["cambiar_estado_solicitud_soporte_estado"] == "3"){
            $mensaje_asignacion = 'El caso fue reactivado';
        }elseif($datos["cambiar_estado_solicitud_soporte_estado"] == "6"){
            $mensaje_asignacion = 'El estado fue cambiado a En espera';
        }elseif($datos["cambiar_estado_solicitud_soporte_estado"] == "7"){
            $mensaje_asignacion = 'El caso fue Canceldo';
        }
        $datos_base = array ("solicitud" => $datos["cambiar_estado_solicitud_soporte_id"],
        "mensaje" => $mensaje_asignacion,
        "auditoria_creado" => $fecha_hoy,
        "auditoria_usuario" => $datos["cambiar_estado_solicitud_soporte_auditoria_usuario"],
        "tipo" => "1",
        "archivo" => "");
        $registro_asignacion = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        $respuesta = Pqr_Modelo::cambiar_estado_solicitud_soporte_modelo("pqr_solicitud", $datos);
        $fecha_creado = $datos["cambiar_estado_solicitud_soporte_fecha"];
        date_default_timezone_set("America/Bogota");
        $fecha_creado = date("d/m/Y");
        $fecha_resuelto = date("Y-m-d H:i:s");
        $datos_correo_cliente = Pqr_Modelo::consultar_correo_cliente_modelo("cliente", $consulta_pqr["cliente"]);
        if($datos["cambiar_estado_solicitud_soporte_estado"] == "4"){
            if($consulta_pqr["reabierto"] > 0){
                $registrar_fecha_resuelto = Pqr_Modelo::registrar_fecha_resuelto_caso_reabierto_modelo("pqr_reabierto", $datos["cambiar_estado_solicitud_soporte_id"], $fecha_resuelto);
            }else{
                $registrar_fecha_resuelto = Pqr_Modelo::registrar_fecha_resuelto_modelo("pqr_solicitud", $datos["cambiar_estado_solicitud_soporte_id"], $fecha_resuelto);
            }
            $asunto_correo = 'Caso resuelto';
            $titulo_mensaje_correo = 'Tu caso fue resuelto';
            $cuerpo_mensaje_correo = 'Hola '.$datos_correo_cliente["nombres"].',<br>Tu caso "'.$consulta_pqr["tema"].'" ha sido resuelto.<br>Necesitamos de tu colaboración para continuar mejorando nuestro servicio, por favor ingresa a la plataforma, da clic en el botón "Cerrar Caso" y responde una encuesta de satisfacción.<br>Haz clic en el botón para ingresar a la plataforma.<br> 
            <a href="http://mesadeservicio.croydon.com.co/login-cliente" target="_blank" style="text-decoration:none"><div style="line-height:60px; background:#005574; width:60%; color:white">Calificar servicio</div></a>
            <br>Si el botón no funciona, copia y pega el enlace en tu navegador: http://mesadeservicio.croydon.com.co/login-cliente';
            $cantidad_correos = 2;
        }elseif($datos["cambiar_estado_solicitud_soporte_estado"] == "3"){
            $asunto_correo = 'Caso reactivado';
            $titulo_mensaje_correo = 'Tu caso ha sido reactivado';
            $cuerpo_mensaje_correo = 'Hola '.$datos_correo_cliente["nombres"].',<br>Tu caso número '.$datos["cambiar_estado_solicitud_soporte_id"].' con tema "'.$consulta_pqr["tema"].'" ha sido reactivado<br>Lo atenderemos lo más pronto posible.';
            $cantidad_correos = 1;
        }elseif($datos["cambiar_estado_solicitud_soporte_estado"] == "6"){
            $asunto_correo = 'Caso en espera';
            $titulo_mensaje_correo = 'Tu caso esta en espera';
            $cuerpo_mensaje_correo = 'Hola '.$datos_correo_cliente["nombres"].',<br>Tu caso número '.$datos["cambiar_estado_solicitud_soporte_id"].' con tema "'.$consulta_pqr["tema"].'" ha quedado en espera por el motivo mencionado en la respuesta anterior.';
            $cantidad_correos = 1;
        }elseif($datos["cambiar_estado_solicitud_soporte_estado"] == "7"){
            $asunto_correo = 'Caso cancelado';
            $titulo_mensaje_correo = 'Tu caso fue cancelado';
            $cuerpo_mensaje_correo = 'Hola '.$datos_correo_cliente["nombres"].',<br>Tu caso número '.$datos["cambiar_estado_solicitud_soporte_id"].' con tema "'.$consulta_pqr["tema"].'" fue cancelado por el motivo mencionado en la respuesta anterior.';
            $cantidad_correos = 1;
        }
        $enviar_notificacion = Pqr_Controlador::consultar_permiso_envio_notificacion_controlador(5, $consulta_pqr["cliente_id"]);
        if($enviar_notificacion == "Si" && $respuesta == "Ok" && $datos_correo_cliente["correo"] != ""){
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
                $mail->addAddress($datos_correo_cliente["correo"]);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = ''.$asunto_correo.' | Portal Solicitantes Mesa de Ayuda';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">'.$titulo_mensaje_correo.'</h2>
                                    <h4 style="font-weight:100; padding:0 20px">'.$cuerpo_mensaje_correo.'</h4>
                                    <h4 style="font-weight:100; padding:0 20px">¡Encantados de servirte!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
        }
        echo $respuesta;
    }

    static public function formulario_finalizar_solicitud_controlador($datos){
        $finalizar_id_solicitud = $datos["formulario_finalizar_id_solicitud"];
        $finalizar_auditoria_usuario = $datos["formulario_finalizar_auditoria_usuario"];
        $finalizar_auditoria_modificado = $datos["formulario_finalizar_auditoria_modificado"];
        $formulario_finalizar_tipo_usuario = $datos["formulario_finalizar_tipo_usuario"];
        echo '<div class="alert alert-danger text-center">
                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                <h5>Antes de cerrar el caso debes llenar esta encuesta de satisfacción:</h5>
                <h6>Si deseas reabrir el caso deberas contactar al administrador o crearlo nuevamente.</h6>
             </div>
             <div class="box-body">
                <form method="post" class="form-horizontal">
                    <input type="hidden" id="finalizar_id_solicitud" value="'.$finalizar_id_solicitud.'">
                    <input type="hidden" id="finalizar_auditoria_usuario" value="'.$finalizar_auditoria_usuario.'">
                    <input type="hidden" id="finalizar_auditoria_modificado" value="'.$finalizar_auditoria_modificado.'">
                    <input type="hidden" id="finalizar_tipo_usuario" value="'.$formulario_finalizar_tipo_usuario.'">
                    <div class="form-group has-feedback">
                        <label class="control-label">Satisfacción con respecto a la calidad del servicio</label>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="btn btn-danger">
                            <br><i class="far fa-angry fa-lg fa-2x"></i><br><input type="radio" value="1" name="finalizar_calificacion_calidad" id="finalizar_calificacion_calidad_opcion_1" autocomplete="off">
                        </label>
                        <label class="btn btn-danger">
                            <br><i class="fa fa-frown-o fa-lg fa-2x"></i><br><input type="radio" value="2" name="finalizar_calificacion_calidad" id="finalizar_calificacion_calidad_opcion_2" autocomplete="off">
                        </label>
                        <label class="btn btn-warning active">
                            <br><i class="fa fa-meh-o fa-lg fa-2x"></i><br><input type="radio" value="3" name="finalizar_calificacion_calidad" id="finalizar_calificacion_calidad_opcion_3" autocomplete="off">
                        </label>
                        <label class="btn btn-success">
                            <br><i class="fa fa-smile-o fa-lg fa-2x"></i><br><input type="radio" value="4" name="finalizar_calificacion_calidad" id="finalizar_calificacion_calidad_opcion_4" autocomplete="off">
                        </label>
                        <label class="btn btn-success">
                            <br><i class="far fa-grin-wink fa-lg fa-2x"></i><br><input type="radio" value="5" name="finalizar_calificacion_calidad" id="finalizar_calificacion_calidad_opcion_5" autocomplete="off" checked>
                        </label>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Satisfacción con respecto al <b>cumplimiento</b></label>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="btn btn-danger">
                            <br><i class="far fa-angry fa-lg fa-2x"></i><br><input type="radio" value="1" name="finalizar_calificacion_cumplimiento" id="finalizar_calificacion_cumplimiento_opcion_1" autocomplete="off">
                        </label>
                        <label class="btn btn-danger">
                            <br><i class="fa fa-frown-o fa-lg fa-2x"></i><br><input type="radio" value="2" name="finalizar_calificacion_cumplimiento" id="finalizar_calificacion_cumplimiento_opcion_2" autocomplete="off">
                        </label>
                        <label class="btn btn-warning active">
                            <br><i class="fa fa-meh-o fa-lg fa-2x"></i><br><input type="radio" value="3" name="finalizar_calificacion_cumplimiento" id="finalizar_calificacion_cumplimiento_opcion_3" autocomplete="off">
                        </label>
                        <label class="btn btn-success">
                            <br><i class="fa fa-smile-o fa-lg fa-2x"></i><br><input type="radio" value="4" name="finalizar_calificacion_cumplimiento" id="finalizar_calificacion_cumplimiento_opcion_4" autocomplete="off">
                        </label>
                        <label class="btn btn-success">
                            <br><i class="far fa-grin-wink fa-lg fa-2x"></i><br><input type="radio" value="5" name="finalizar_calificacion_cumplimiento" id="finalizar_calificacion_cumplimiento_opcion_5" autocomplete="off" checked>
                        </label>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Observación</label>
                        <textarea id="finalizar_observacion" rows="10" cols="40" class="form-control" placeholder="Observación...." required></textarea>
                    </div>
                </form>
            </div>';
    }
        
    static public function finalizar_solicitud_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        $consulta_pqr = Pqr_Modelo::consultar_datos_pqr_modelo("pqr_solicitud", $datos["finalizar_id_solicitud"]);
        if($datos["finalizar_tipo_usuario"] == "funcionario"){
            $datos_colaborador_asignado = Pqr_Modelo::consultar_correo_modelo("usuario", $consulta_pqr["asignado"]);
            $mensaje_asignacion = 'El colaborador '.$datos_colaborador_asignado["nombres"].' ha cerrado el caso.';
        }elseif($datos["finalizar_tipo_usuario"] == "cliente"){
            $mensaje_asignacion = 'El cliente ha cerrado el caso.';
        }
        $datos_estado = array ("cambiar_estado_solicitud_soporte_id" => $datos["finalizar_id_solicitud"],
            "cambiar_estado_solicitud_soporte_estado" => 5,
            "cambiar_estado_solicitud_soporte_auditoria_usuario" => $datos["finalizar_auditoria_usuario"]);
        $respuesta_estado = Pqr_Modelo::cambiar_estado_solicitud_soporte_modelo("pqr_solicitud", $datos_estado);
        $datos_base = array ("solicitud" => $datos["finalizar_id_solicitud"],
            "mensaje" => $mensaje_asignacion,
            "auditoria_creado" => $fecha_hoy,
            "auditoria_usuario" => 0,
            "tipo" => "1",
            "archivo" => "");
        $registro_asignacion = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        $respuesta = Pqr_Modelo::finalizar_solicitud_modelo("pqr_cerrado", $datos);
        $fecha_creado = $datos["finalizar_auditoria_modificado"];
        $fecha_creado = date("d/m/Y");
        if($respuesta == "Ok" && $datos["finalizar_calificacion_calidad"] == 1 && $datos["finalizar_calificacion_cumplimiento"] == 1){
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
                $mail->addAddress("coordinadorsoporte@croydon.com.co");
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Caso cerrado con mala calificación | Portal Administradores Mesa de Ayuda';
                $mail->Body    = '<div style="width:100%; background:#005574; position:relative; font-family:Open Sans, san-serif; padding-bottom:40px">
                                <div style="font-size:1.2em; position:relative; margin:auto; width:500px; background:white; padding:20px">
                                <center>
                                    <hr style="border:1px solid #005574; width:80%">
                                    <h2 style="font-weight:700">¡Un caso ha sido calificado como "Todo estuvo mal"!</h2>
                                    <h4 style="font-weight:100; padding:0 20px">Hola Administrador<br>El caso número '.$datos["finalizar_id_solicitud"].' con tema "'.$consulta_pqr["tema"].'" del cliente '.$consulta_pqr["cliente_nombres"].' fue calificado en todos los items de la encuesta de satisfacción como "Todo estuvo mal"!</h4>
                                    <h4 style="font-weight:100; padding:0 20px">¡Esperamos tu revisión para reabrir el caso!</h4>
                                    <h4 style="font-weight:100; padding:0 20px"><cite>Mesa de servicio</cite></h4>
                                </center>
                                </div>
                            </div>';
                $mail->send();
            } catch (Exception $e) {
                echo 'Ha ocurrido un error inesperado!';
            }
        }
        echo $respuesta;
    }

    static public function consulta_calificacion_controlador($datos){
        $respuesta = Pqr_Modelo::consulta_calificacion_modelo("pqr_cerrado", $datos["consulta_calificacion_id"]);
        $opcion_calificacion = Pqr_Modelo::consulta_opcion_calificacion_modelo("pqr_calificacion");
        if($respuesta != null){
            foreach($respuesta as $cantidad_calificacion => $respuesta_formulario){
                $fecha_registro = date_create($respuesta_formulario["fecha"]);
                $fecha = date_format($fecha_registro, "d / m / Y");
                echo '<div class="box-body">
                        <form method="post" class="form-horizontal">
                            <h4 class="bg-green btn">Fecha de calificación: '.$fecha.'</h4>
                            <div class="form-group has-feedback">
                                <label class="control-label">Satisfacción en la calidad del servicio</label>
                            </div>
                            <div class="form-group has-feedback">';
                                foreach ($opcion_calificacion as $calificacion => $item){
                                    if($respuesta_formulario["calificacion_calidad"] == ($calificacion + 1)){
                                        $color = $item["color"];
                                    }else{
                                        $color = "grey disabled";
                                    }
                                    echo '<label class="btn btn-'.$color.'">
                                            <input type="radio" disabled value="1" name="calificacion_servicio_'.$item["id"].'" autocomplete="off"> <i class="'.$item["icono"].' fa-lg fa-2x"></i>
                                        </label>';
                                }                            
                        echo'</div>
                            <div class="form-group has-feedback">
                                <label class="control-label">Satisfacción en el cumplimiento</label>
                            </div>
                            <div class="form-group has-feedback">';
                                foreach ($opcion_calificacion as $calificacion => $item){
                                    if($respuesta_formulario["calificacion_cumplimiento"] == ($calificacion + 1)){
                                        $color = $item["color"];
                                    }else{
                                        $color = "grey disabled";
                                    }
                                    echo '<label class="btn btn-'.$color.'">
                                            <input type="radio" disabled value="1" name="calificacion_tiempo_'.$item["id"].'" autocomplete="off"> <i class="'.$item["icono"].' fa-lg fa-2x"></i>
                                        </label>';
                                }                            
                        echo'</div>
                            <div class="form-group has-feedback">
                                <label class="control-label">Observación</label>
                                <textarea id="finalizar_observacion" rows="4" cols="40" class="form-control" placeholder="Observación...." disabled>'.$respuesta_formulario["observacion"].'</textarea>
                            </div>
                    </form>
                </div>';
            }
        }else{
            echo '<div class="alert alert-danger text-center">
                    <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                    <h5>Este caso fue cerrado sin llenar encuesta de satisfacción</h5>
                    <h6>Contacta al administrador y solicita que cambie el estado del caso a "Resuelto" para poder contestar la encuesta.</h6>
                </div>';
        }
    }

    static public function formulario_archivo_adjunto_solicitud_controlador($datos){
        $consulta_archivos_adjuntos = Pqr_Modelo::consultar_archivo_adjunto_solicitud_modelo("pqr_solicitud_archivo", $datos["formulario_archivo_adjunto_solicitud"]);
        $archivo_principal = Pqr_Modelo::consultar_archivo_principal_adjunto_solicitud_modelo("pqr_solicitud", $datos["formulario_archivo_adjunto_solicitud"]);
        echo '<div class="box-body>
                <form method="POST">
                    <div class="row">';
                if($archivo_principal["archivo_principal"] != NULL){
                    $extension = explode(".", $archivo_principal["archivo_principal"]);
                    if($extension[1] == "csv"){
                        $nombre_archivo = "CSV";
                        $color = "green";
                        $icono = "fas fa-file-csv";
                    }elseif($extension[1] == "xls" || $extension[1] == "xlsx"){
                        $nombre_archivo = "Excel";
                        $color = "green";
                        $icono = "fas fa-file-excel";
                    }elseif($extension[1] == "pdf"){
                        $nombre_archivo = "PDF";
                        $color = "red";
                        $icono = "fas fa-file-pdf";
                    }elseif($extension[1] == "doc" || $extension[1] == "docx"){
                        $nombre_archivo = "Word";
                        $color = "blue";
                        $icono = "fas fa-file-word";
                    }elseif($extension[1] == "txt"){
                        $nombre_archivo = "Txt";
                        $color = "grey";
                        $icono = "fas fa-file-alt";
                    }elseif($extension[1] == "prn"){
                        $nombre_archivo = "Prn";
                        $color = "grey";
                        $icono = "fas fa-file-alt";
                    }elseif($extension[1] == "jpg" || $extension[1] == "png"){
                        $nombre_archivo = "Imagen";
                        $color = "yellow";
                        $icono = "fas fa-photo-video";
                    }
                    $archivo = "<a class='btn btn-lg card-img-top' href='".$_COOKIE["raiz"]."vista/img/pqr_solicitud/".$archivo_principal["archivo_principal"]."' title='".$nombre_archivo."' target='_blank'><i class='bg-".$color." ".$icono." fa-4x'></i></a>";
                    
                    echo'<div class="card col-md-4">
                            '.$archivo.'
                            <div class="card-body">
                                <b class="card-title">'.$nombre_archivo.'</b>
                            </div>
                        </div>';
                }else {
                    echo '<div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i><strong>ATENCIÓN:</strong><br>
                            <h5>No hay archivos adjuntos para este caso</h5>
                        </div>';
                }
                foreach ($consulta_archivos_adjuntos as $archivos_adjuntos => $archivo_adjunto){
                    $extension = explode(".", $archivo_adjunto["archivo"]);
                    if($extension[1] == "csv"){
                        $nombre_archivo = "CSV";
                        $color = "green";
                        $icono = "fas fa-file-csv";
                    }elseif($extension[1] == "xls"){
                        $nombre_archivo = "Excel";
                        $color = "green";
                        $icono = "fas fa-file-excel";
                    }elseif($extension[1] == "pdf"){
                        $nombre_archivo = "PDF";
                        $color = "red";
                        $icono = "fas fa-file-pdf";
                    }elseif($extension[1] == "doc" || $extension[1] == "docx"){
                        $nombre_archivo = "Word";
                        $color = "blue";
                        $icono = "fas fa-file-word";
                    }elseif($extension[1] == "txt"){
                        $nombre_archivo = "Txt";
                        $color = "grey";
                        $icono = "fas fa-file-alt";
                    }elseif($extension[1] == "prn"){
                        $nombre_archivo = "Prn";
                        $color = "grey";
                        $icono = "fas fa-file-alt";
                    }elseif($extension[1] == "jpg" || $extension[1] == "png"){
                        $nombre_archivo = "Imagen";
                        $color = "yellow";
                        $icono = "fas fa-photo-video";
                    }
                    $archivo = "<a class='btn btn-lg card-img-top' href='".$_COOKIE["raiz"]."vista/img/pqr_solicitud/".$archivo_adjunto["archivo"]."' title='".$nombre_archivo."' target='_blank'><i class='bg-".$color." ".$icono." fa-4x'></i></a>";
                    
                    echo'<div class="card col-md-4">
                            '.$archivo.'
                            <div class="card-body">
                                <b class="card-title">'.$nombre_archivo.'</b>
                            </div>
                        </div>';
                }   
                echo'</div>
                </form>
            </div>';
    }
    
    static public function modificar_solicitud_controlador($datos) {
        $datos_solicitud = array("solicitud"=>  $datos["modificar_solicitud_id_caso"],
            "auditoria_usuario"=>  $datos["modificar_solicitud_auditoria_usuario"],
            "categoria"=> $datos["modificar_solicitud_categoria"]);
        if ($datos["modificar_solicitud_fecha_estimada_resuelto"] != " ") {
            $fecha_estimada_resuelto = $datos["modificar_solicitud_fecha_estimada_resuelto"].' '.$datos["modificar_solicitud_hora_estimada_resuelto"].':00';
            $consulta_detalle_solicitud = Pqr_Modelo::consulta_detalle_solicitud_modelo("pqr_solicitud", $datos["modificar_solicitud_id_caso"]);
            if($fecha_estimada_resuelto > $consulta_detalle_solicitud["fecha_estimada_resuelto"]){
                $modificar_fecha_estimada = Pqr_Modelo::modificar_fecha_estimada_solicitud_modelo("pqr_solicitud", $datos_solicitud, $fecha_estimada_resuelto);
                $fecha_estimada = date_create($fecha_estimada_resuelto);
                $hora_estiamda = date_format($fecha_estimada, "H:iA");
                $fecha_estimada = date_format($fecha_estimada, "d/m/Y");
                $mensaje_respuesta = "El director de tecnologia ha estimado el tiempo de resolución de este caso para el ".$fecha_estimada.' a las '.$hora_estiamda.'.';
                date_default_timezone_set("America/Bogota");
                $fecha_registro = date("Y-m-d H:i:s");
                $datos_base = array ("solicitud" => $datos["modificar_solicitud_id_caso"],
                    "mensaje" => $mensaje_respuesta,
                    "auditoria_creado" => $fecha_registro,
                    "auditoria_usuario" => $datos["modificar_solicitud_auditoria_usuario"],
                    "archivo" => "",
                    "tipo" => "1");
                $registro_historial = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
            }
        }
        $respuesta = Pqr_Modelo::modificar_solicitud_modelo("pqr_solicitud", $datos_solicitud);
        return $respuesta;
    }

    static public function motor_busqueda_casos_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $fecha = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $tipo_usuario = $_COOKIE["tipo_usuario"];
        $fecha_inicial = $datos["motor_busqueda_casos_fecha_desde"].' '.$datos["motor_busqueda_casos_hora_desde"].':00';
        $fecha_final = $datos["motor_busqueda_casos_fecha_hasta"].' '.$datos["motor_busqueda_casos_hora_hasta"].':00';
        $caso_reabierto = "";
        $datos_busqueda = array("tabla"=> "pqr_prioridad",
                            "fecha_inicial"=>$fecha_inicial,
                            "fecha_final"=>$fecha_final,
                            "estado"=>$datos["motor_busqueda_casos_tipo"],
                            "solicitud"=>$datos["motor_busqueda_casos_id"]);
        if($datos["motor_busqueda_casos_tipo"] == " "){
            $respuesta = Pqr_Modelo::motor_busqueda_completo_modelo("pqr_solicitud", $datos_busqueda);
        }else{
            $respuesta = Pqr_Modelo::motor_busqueda_modelo("pqr_solicitud", $datos_busqueda);
        }
        echo '  <div class="box-header with-border">
                    <h3 class="box-title">Resultado de búsqueda</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id ="tabla-motor-busqueda" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID solicitud</th>
                                    <th>Cliente</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
        foreach($respuesta as $fila => $item){
            $cerrar_caso = "";
            $historial = "";
            $reabrir = "";
            $reactivar = "";
            $calificacion = "";
            $cancelar = "";
            $asignar = "<button type='button' formulario_asignar_auditoria_creado='".$fecha."' formulario_asignar_id_solicitud='".$item["id"]."' formulario_asignar_auditoria_usuario='".$auditoria_usuario."' class='btn btn-warning btn-sm asignar-solicitud-pqr' title='Asignar' data-toggle='modal' data-target='#modal_asignar_solicitud'><i class='fas fa-people-carry'></i></button>";
            $ver_detalle = "<button type='button' formulario_detalle_solicitud='".$item["id"]."' formulario_detalle_auditoria_usuario='".$auditoria_usuario."' class='btn btn-primary btn-sm formulario-detalle-solicitud' title='Ver detalle' data-toggle='modal' data-target='#modal_detalle_solicitud'><i class='fas fa-edit'></i></button>";
            $historial = "<button type='button' historial_respuesta_id_cliente='".$item["id"]."' class='btn btn-info btn-sm consulta-cliente-solicitud' title='Historial de respuestas' data-toggle='modal' data-target='#modal_historial_respuesta'><i class='fas fa-list'></i></button>";
            if($item["estado"] == "Sin asignar"){
                $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
            }elseif($item["estado"] == "Asignado"){
                $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                $cancelar = "<button type='button' formulario_cambiar_estado_solicitud_soporte_estado='7' formulario_cambiar_estado_solicitud_soporte_id='".$item["id"]."' formulario_cambiar_estado_solicitud_soporte_auditoria_usuario='".$auditoria_usuario."' formulario_cambiar_estado_solicitud_soporte_fecha='".$fecha."' class='btn btn-danger btn-sm cambiar-estado-solicitud' title='Cancelar caso' data-toggle='modal' data-target='#modal_cambiar_estado_solicitud'><i class='fas fa-ban'></i></button>";
            }elseif($item["estado"] == "En espera"){
                $estado = "<a class='btn btn-warning disabled'> ".$item["estado"]." </a>";
                $reactivar = "<button type='button' formulario_cambiar_estado_solicitud_soporte_estado='3' formulario_cambiar_estado_solicitud_soporte_id='".$item["id"]."' formulario_cambiar_estado_solicitud_soporte_auditoria_usuario='".$auditoria_usuario."' formulario_cambiar_estado_solicitud_soporte_fecha='".$fecha."' class='btn btn-success btn-sm cambiar-estado-solicitud' title='Reactivar caso' data-toggle='modal' data-target='#modal_cambiar_estado_solicitud_soporte'><i class='fas fa-play'></i></button>";
            }else if($item["estado"] == "En ejecución"){
                $estado = "<a class='btn btn-info disabled'> ".$item["estado"]." </a>"; 
            }else if($item["estado"] == "Resuelto"){
                $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                $asignar = "";
            }else if($item["estado"] == "Cerrado"){
                $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                $reabrir = "<button type='button' formulario_reabrir_auditoria_creado='".$fecha."' formulario_reabrir_auditoria_usuario='".$auditoria_usuario."' formulario_reabrir_id_solicitud='".$item["id"]."' class='btn btn-success btn-sm reabrir-solicitud' title='Reabrir solicitud' data-toggle='modal' data-target='#modal_reabrir_solicitud'><i class='fas fa-redo-alt'></i></button>";
                $asignar = "";
            }
            if($item["estado"] == "Cerrado"){
                $estado = "<a class='btn btn-success disabled'> ".$item["estado"]." </a>";
                $asignar = "";
                $calificacion = "<button type='button' consulta_calificacion_id='".$item["id"]."' class='btn btn-primary btn-sm consulta-calificacion' title='Consultar resultados encuesta' data-toggle='modal' data-target='#modal_consulta_calificacion'><i class='fas fa-clipboard-check'></i></button>";
            }elseif($item["estado"] == "Cancelado"){
                $estado = "<a class='btn btn-danger disabled'> ".$item["estado"]." </a>";
            }
            $tema = substr($item["tema"], 0, 20);
            if(strlen($item["tema"]) > 20){
                $tema = $tema.'...';
            }
            if($item["archivo"] != NULL){
                $archivo = "<button type='button' formulario_archivo_adjunto_solicitud='".$item["id"]."' class='btn btn-warning btn-sm formulario-adjunto-solicitud' title='Archivos ajuntos' data-toggle='modal' data-target='#modal_adjunto_solicitud'><i class='fas fa-folder-open'></i></button>";
            }else{
                $archivo = "";
            }
            $fecha_creado = date_create($item["auditoria_creado"]);
            $fecha_creado = date_format($fecha_creado, "d/m/Y");
            $acciones = "<div class='btn-group'>".$calificacion.$reactivar.$historial.$archivo.$cerrar_caso.$asignar.$reabrir.$ver_detalle.$cancelar."</div>";
            echo'<tr>
                    <td>'.$item["id"].'</td>
                    <td>'.$item["nombres"].'</td>
                    <td>'.$item["tema"].'</td>
                    <td>'.$estado.'</td>
                    <td>'.$acciones.'</td>
                </tr>';
        }
            echo '</tbody>
            </table>
        </div>';
        echo " <script type='text/javascript'>
            $(document).ready(function() {
                $('#tabla-motor-busqueda thead tr').clone(true).appendTo( '#tabla-motor-busqueda thead' );
                $('#tabla-motor-busqueda thead tr:eq(1) th').each( function (i) {
                    var title = $(this).text();
                    $(this).html( '<input type="; echo 'text" placeholder="Filtro'; echo " '+title+'"; echo ' " '; echo "/>'"; echo ");
            
                    $( 'input', this ).on( 'keyup change', function () {
                        if ( table.column(i).search() !== this.value ) {
                            table
                                .column(i)
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );
                var table = $('#tabla-motor-busqueda').DataTable( {
                    orderCellsTop: true,
                    fixedHeader: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'pdfHtml5'
                    ],
                    deferRender: true,
                    retrieve: true,
                    processing: true,";
                echo 'language: {
                    sProcessing: "Procesando...",
                    sLengthMenu: "Mostrar _MENU_ registros",
                    sZeroRecords: "No se encontraron resultados",
                    sEmptyTable: "Ningún dato disponible en esta tabla",
                    sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                    sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0",
                    sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                    sInfoPostFix: "",
                    sSearch: "Buscar:",
                    sUrl: "",
                    sInfoThousands: ",",
                    sLoadingRecords: "Cargando...",
                    oPaginate: {
                        sFirst: "Primero",
                        sLast: "Último",
                        sNext: "Siguiente",
                        sPrevious: "Anterior"
                    },oAria: {
                        sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                        sSortDescending: ": Activar para ordenar la columna de manera descendente"
                    }
                }';
            echo "} );
            } );
        </script>";
    }

    static public function formulario_establecer_fecha_resolucion_controlador($id_solicitud){
        $consulta_solicitud = Pqr_Modelo::consultar_fecha_estimada_resuelto_modelo("pqr_solicitud", $id_solicitud);
        if($consulta_solicitud["fecha_resuelto"] == NULL){
            echo '<form method="POST">
                    <div class="alert alert-danger text-center">
                        <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                        <h5>Ingrese fecha y hora en la que estima dar el caso por resuelto</h5>
                        <div class="form-group has-feedback">
                            <label class="control-label">Fecha</label>
                            <input type="hidden" id="establecer_fecha_resolucion_id_solicitud" value="'.$id_solicitud.'">
                            <input class="form-control" type="date" id="establecer_fecha_resolucion">
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label">Hora</label>
                            <input class="form-control" type="time" id="establecer_fecha_resolucion_hora">
                        </div>
                    </div>
                </form>';
        }else{
            $fecha_estimada = date_create($consulta_solicitud["fecha_resuelto"]);
            $hora_estiamda = date_format($fecha_estimada, "H:iA");
            $fecha_estimada = date_format($fecha_estimada, "d/m/Y");
            echo '<div class="alert alert-danger text-center">
                    <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                    <h5>Has estimado la fecha de resolución para el '.$fecha_estimada.'a las '.$hora_estiamda.'.</h5>
                </div>';
        }
    }

    static public function establecer_fecha_resolucion_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $fecha_hoy = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"];
        $fecha_estimado = $datos["establecer_fecha_resolucion"];
        $registrar_fecha_estimado = Pqr_Modelo::registrar_fecha_estimada_resuelto_modelo("pqr_solicitud",  $datos["establecer_fecha_resolucion"], $datos["establecer_fecha_resolucion_id_solicitud"]);
        $fecha_estimada = date_create($fecha_estimado);
        $hora_estiamda = date_format($fecha_estimada, "H:iA");
        $fecha_estimada = date_format($fecha_estimada, "d/m/Y");
        $mensaje_respuesta = "El supervisor ha estimado el tiempo de resolución de este caso para el ".$fecha_estimada.' a las '.$hora_estiamda.'.';
        $datos_base = array ("solicitud" => $datos["establecer_fecha_resolucion_id_solicitud"],
            "mensaje" => $mensaje_respuesta,
            "auditoria_creado" => $fecha_hoy,
            "auditoria_usuario" => $auditoria_usuario,
            "archivo" => "",
            "tipo" => "1");
        $respuesta = Pqr_Modelo::respuesta_solicitud_modelo("pqr_respuesta", $datos_base);
        return $respuesta;
    }
}