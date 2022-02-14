<?php
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/
class Grupo_Trabajo_Controlador{
    static public function lista_grupo_trabajo_controlador(){
        $auditoria_usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $respuesta = Grupo_Trabajo_Modelo::lista_grupo_trabajo_modelo("grupo_trabajo");
        if($respuesta == null){
            $respuesta_json = '{"data": []}';
        echo $respuesta_json;
        }else{
            $respuesta_json = '{
            "data": [';
            for($i = 0; $i<count($respuesta); $i++){
                $editar_grupo = "<button type='button' formulario_modificar_grupo_trabajo_id='".$respuesta[$i]["id"]."' formulario_modificar_grupo_trabajo_auditoria_usuario='".$auditoria_usuario."' class='btn btn-success btn-sm formulario-modificar-grupo-trabajo' title='Editar grupo' data-toggle='modal' data-target='#modal_modificar_grupo_trabajo'><i class='fas fa-edit'></i></button>";
                $configurar_supervisor = "<button type='button' formulario_establecer_supervisor_grupo_trabajo_id='".$respuesta[$i]["id"]."' formulario_establecer_supervisor_grupo_trabajo_auditoria_usuario='".$auditoria_usuario."' class='btn btn-primary btn-sm formulario-establecer-grupo-trabajo-supervisor' title='Establecer supervisor' data-toggle='modal' data-target='#modal_establecer_supervisor_grupo_trabajo'><i class='fas fa-glasses'></i></button>";
                $configurar_integrantes = "<button type='button' formulario_agregar_integrante_grupo_trabajo_id='".$respuesta[$i]["id"]."' formulario_agregar_integrante_grupo_trabajo_auditoria_usuario='".$auditoria_usuario."' class='btn btn-warning btn-sm formulario-agregar-grupo-trabajo-integrante' title='Agregar integrantes' data-toggle='modal' data-target='#modal_agregar_integrante_grupo_trabajo'><i class='fas fa-user-friends'></i></button>";
                $eliminar = "<button type='button' formulario_eliminar_grupo_trabajo_id='".$respuesta[$i]["id"]."' formulario_eliminar_grupo_trabajo_auditoria_usuario='".$auditoria_usuario."' class='btn btn-danger btn-sm formulario-eliminar-grupo-trabajo' title='Eliminar' data-toggle='modal' data-target='#modal_eliminar_grupo_trabajo'><i class='fas fa-trash-alt'></i></button>";
                if($respuesta[$i]["estado"] == "Activo"){
                    $estado = "<a class='btn btn-success disabled'> ".$respuesta[$i]["estado"]." </a>";
                }elseif($respuesta[$i]["estado"] == "Inactivo"){
                    $estado = "<a class='btn btn-warning disabled'> ".$respuesta[$i]["estado"]." </a>";
                }
                $acciones = "<div class='btn-group'>".$editar_grupo.$configurar_supervisor.$configurar_integrantes.$eliminar."</div>";
                $respuesta_json .='[
                    "'.$respuesta[$i]["grupo_trabajo"].'",
                    "'.$respuesta[$i]["tipo"].'",
                    "'.$estado.'",
                    "'.$acciones.'"
                ],';
            }
        $respuesta_json = substr($respuesta_json, 0, -1);
		$respuesta_json .= '] 
        }';
        echo $respuesta_json;
        }
    }

    static public function formulario_establecer_supervisor_grupo_trabajo_controlador($datos){
        $auditoria_usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $supervisor_actual_grupo = Grupo_Trabajo_Modelo::consultar_supervisor_grupo_trabajo_modelo("grupo_trabajo", $datos["formulario_establecer_supervisor_grupo_trabajo_id"]);
        if($supervisor_actual_grupo != NULL){
            echo '<div class="alert alert-danger text-center">
                    <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                    <h5>El supervisor actual de grupo es '.$supervisor_actual_grupo["nombres"].'!</h5>
                    <h6>Si establece otro supervisor le serán revocados los permisos de administrador del grupo a '.$supervisor_actual_grupo["nombres"].'.</h6>
                </div>';
        }else{
            $supervisor_actual_grupo = Grupo_Trabajo_Modelo::consultar_grupo_trabajo_sin_supervisor_modelo("grupo_trabajo", $datos["formulario_establecer_supervisor_grupo_trabajo_id"]);
        }
        if($supervisor_actual_grupo["tipo"] == 1){
            $respuesta = Grupo_Trabajo_Modelo::lista_usuario_modelo("usuario");
        }else{
            $respuesta = Grupo_Trabajo_Modelo::lista_usuario_modelo("cliente");
        }
        echo ' <div class="table-responsive">
                    <table id ="tabla-establecer-supervisor" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Nombres</th>
                                <th>Cargo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
                        foreach($respuesta as $fila => $item){
                            $avatar = "<img src='".$raiz."/vista/img/avatar/".$item["avatar"]."' width='50' height='50' class='img-circle' alt='Avatar'>";
                            $establecer_supervisor = "<button type='button' establecer_supervisor_grupo_trabajo_id_supervisor='".$item["id"]."' establecer_supervisor_grupo_trabajo_id='".$datos["formulario_establecer_supervisor_grupo_trabajo_id"]."' establecer_supervisor_grupo_trabajo_auditoria_usuario='".$auditoria_usuario."' class='btn btn-success btn-sm establecer-grupo-trabajo-supervisor' title='Establecer'>Establecer</button>";
                            $acciones = "<div class='btn-group'>".$establecer_supervisor."</div>";
                            echo'<tr>
                                    <td>'.$avatar.'</td>
                                    <td>'.$item["nombres"].'</td>
                                    <td>'.ltrim(rtrim($item["cargo"])).'</td>
                                    <td>'.$acciones.'</td>
                                </tr>';
                        }
                echo '</tbody>
                </table>
            </div>';
        echo " <script type='text/javascript'>
                $(document).ready(function() {
                    var table = $('#tabla-establecer-supervisor').DataTable( {
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

    static public function formulario_editar_grupo_agente_controlador($datos){
        $consulta = Grupo_Trabajo_Modelo::consulta_grupo_modelo("grupo_trabajo");
        echo '<div class="box-body">
                <form method="post">
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Grupo</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody class="text-left">';
        foreach ($consulta as $fila => $item) {
            $consulta_grupo_agente = Grupo_Trabajo_Modelo::consulta_grupo_usuario_modelo("usuario_grupo_trabajo", $datos["formulario_editar_grupo_agente_id"], $item["id"]);
            $boton_estado = "<a href='#' class='btn btn-success btn-xs modificar-estado-grupo-agente' id='modificar-estado-grupo-agente-".$item["id"]."' modificar_grupo_agente_id_agente='".$datos["formulario_editar_grupo_agente_id"]."' modificar_grupo_agente_grupo='".$item["id"]."' modificar_grupo_agente_registro='0' modificar_grupo_agente_auditoria_usuario='".$datos["formulario_editar_grupo_agente_auditoria_usuario"]."' modificar_grupo_agente_estado_actual='Excluido'>Incluir</a>";
            $boton_estado_contrario = "<a href='#' class='hidden btn btn-danger btn-xs modificar-estado-grupo-agente' id='modificar-estado-grupo-agente-".$item["id"].$datos["formulario_editar_grupo_agente_id"]."' modificar_grupo_agente_id_agente='".$datos["formulario_editar_grupo_agente_id"]."' modificar_grupo_agente_grupo='".$item["id"]."' modificar_grupo_agente_registro='0' modificar_grupo_agente_auditoria_usuario='".$datos["formulario_editar_grupo_agente_auditoria_usuario"]."' modificar_grupo_agente_estado_actual='Incluido'>Excluir</a>";
            if($consulta_grupo_agente != NULL){
                if($consulta_grupo_agente["grupo"] == $item["id"] && $consulta_grupo_agente["estado"] == 2){
                    $boton_estado = "<a href='#' class='btn btn-danger btn-xs modificar-estado-grupo-agente' id='modificar-estado-grupo-agente-".$item["id"]."' modificar_grupo_agente_id_agente='".$datos["formulario_editar_grupo_agente_id"]."' modificar_grupo_agente_grupo='".$item["id"]."' modificar_grupo_agente_registro='".$consulta_grupo_agente["id"]."' modificar_grupo_agente_auditoria_usuario='".$datos["formulario_editar_grupo_agente_auditoria_usuario"]."' modificar_grupo_agente_estado_actual='Incluido'>Excluir</a>";
                    $boton_estado_contrario = "<a href='#' class='hidden btn btn-success btn-xs modificar-estado-grupo-agente' id='modificar-estado-grupo-agente-".$item["id"].$datos["formulario_editar_grupo_agente_id"]."' modificar_grupo_agente_id_agente='".$datos["formulario_editar_grupo_agente_id"]."' modificar_grupo_agente_grupo='".$item["id"]."' modificar_grupo_agente_registro='".$consulta_grupo_agente["id"]."' modificar_grupo_agente_auditoria_usuario='".$datos["formulario_editar_grupo_agente_auditoria_usuario"]."' modificar_grupo_agente_estado_actual='Excluido'>Incluir</a>";
                }
            }
                        echo '<tr>
                                <td>'.$item["grupo_trabajo"].'</td>
                                <td>'.$boton_estado.$boton_estado_contrario.'</td>
                            </tr>';
        }
                echo ' </tbody>
                    </table>
                </div>
            </form>
        </div>';
    }

    static public function crear_grupo_trabajo_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $auditoria_creado = date("Y-m-d H:i:s");
        $auditoria_usuario = $_COOKIE["usuario"];
        $datos = array("grupo_trabajo"=> $datos["crear_grupo_trabajo_nombre"],
                    "tipo"=>  $datos["crear_grupo_trabajo_tipo"],
                    "auditoria_usuario"=>  $auditoria_usuario,
                    "auditoria_creado"=>  $auditoria_creado,
                    "estado"=> 2);
        $respuesta = Grupo_Trabajo_Modelo::crear_grupo_trabajo_modelo("grupo_trabajo", $datos);
        return $respuesta;
    }

    static public function modificar_grupo_agente_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $auditoria_creado = date("Y-m-d H:i:s");
        $consulta_grupo_agente = Grupo_Trabajo_Modelo::consulta_grupo_usuario_modelo("usuario_grupo_trabajo", $datos["modificar_grupo_agente_id_agente"], $datos["modificar_grupo_agente_grupo"]);
        if($consulta_grupo_agente != NULL){
            if($datos["modificar_grupo_agente_estado_actual"] == "Incluido"){
                $datos = array("agente"=> $datos["modificar_grupo_agente_id_agente"],
                    "grupo"=>  $datos["modificar_grupo_agente_grupo"],
                    "auditoria_usuario"=>  $datos["modificar_grupo_agente_auditoria_usuario"],
                    "estado"=> 1);
                $respuesta = Grupo_Trabajo_Modelo::actualizar_estado_grupo_usuario_modelo("usuario_grupo_trabajo", $datos);
            }else{
                $datos = array("agente"=> $datos["modificar_grupo_agente_id_agente"],
                "grupo"=>  $datos["modificar_grupo_agente_grupo"],
                "auditoria_usuario"=>  $datos["modificar_grupo_agente_auditoria_usuario"],
                "estado"=> 2);
                $respuesta = Grupo_Trabajo_Modelo::actualizar_estado_grupo_usuario_modelo("usuario_grupo_trabajo", $datos);
            }
        }else {
            $respuesta = Grupo_Trabajo_Modelo::registro_agente_grupo_modelo("usuario_grupo_trabajo", $datos, $auditoria_creado);
        }
        return $respuesta;
    }

    static public function formulario_eliminar_grupo_trabajo_controlador($datos){
        $eliminar_grupo_trabajo_id = $datos["formulario_eliminar_grupo_trabajo_id"];
        $eliminar_grupo_trabajo_auditoria_usuario = $datos["formulario_eliminar_grupo_trabajo_auditoria_usuario"];
        echo '<div class="alert alert-danger text-center">
                <i class="icon fa fa-ban"></i><strong>ATENCIÓN:</strong><br>
                <h5>Va a eliminar un grupo de trabajo!</h5>
                <h6>Todos los usuarios agregados perderan el acceso al mismo, incluido el supervisor del grupo.</h6>
                <form method="post" class="form-horizontal">
                    <input type="hidden" id="eliminar_grupo_trabajo_id" value="'.$eliminar_grupo_trabajo_id.'">
                    <input type="hidden" id="eliminar_grupo_trabajo_auditoria_usuario" value="'.$eliminar_grupo_trabajo_auditoria_usuario.'">
                </form>
            </div>';
    }

    static public function eliminar_grupo_trabajo_controlador($datos){
        $respuesta = Grupo_Trabajo_Modelo::eliminar_grupo_trabajo_modelo("grupo_trabajo", $datos);
        return $respuesta;
    }

    static public function establecer_supervisor_grupo_trabajo_controlador($datos){
        $respuesta = Grupo_Trabajo_Modelo::establecer_supervisor_grupo_trabajo_modelo("grupo_trabajo", $datos);
        return $respuesta;
    }

    static public function formulario_agregar_integrante_grupo_trabajo_controlador($datos){
        $auditoria_usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $supervisor_actual_grupo = Grupo_Trabajo_Modelo::consultar_grupo_trabajo_sin_supervisor_modelo("grupo_trabajo", $datos["formulario_agregar_integrante_grupo_trabajo_id"]);
        $integrantes_grupo = Grupo_Trabajo_Modelo::consultar_integrante_grupo_trabajo_modelo("usuario_grupo_trabajo", $datos["formulario_agregar_integrante_grupo_trabajo_id"]);
        $id_integrantes = array(0);
        if($integrantes_grupo != NULL){
            echo ' <h3>Integrantes</h3>
                    <div class="form-group has-feedback">';
            foreach ($integrantes_grupo as $integrantes => $integrante) {
                array_push($id_integrantes, $integrante["id_usuario"]);
                echo '<a disabled class="btn btn-warning btn-xs">'.$integrante["nombres"].'<a class="btn btn-danger btn-xs agregar-integrante-grupo-trabajo" agregar_integrante_grupo_trabajo_estado_actual="Agregado" agregar_integrante_grupo_trabajo_id_usuario="'.$integrante["id_usuario"].'" agregar_integrante_grupo_trabajo_id="'.$datos["formulario_agregar_integrante_grupo_trabajo_id"].'" agregar_integrante_grupo_trabajo_auditoria_usuario="'.$auditoria_usuario.'" title="Excluir del grupo" >x</a></a> ';
            }
            echo '</div>';
        }
        if($supervisor_actual_grupo["tipo"] == 1){
            $respuesta = Grupo_Trabajo_Modelo::lista_usuario_modelo("usuario");
        }else{
            $respuesta = Grupo_Trabajo_Modelo::lista_usuario_modelo("cliente");
        }
        echo ' <div class="table-responsive">
                    <table id ="tabla-agregar-integrante" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Nombres</th>
                                <th>Cargo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
                        foreach($respuesta as $fila => $item){
                            if(!in_array($item["id"], $id_integrantes)){
                                $avatar = "<img src='".$raiz."/vista/img/avatar/".$item["avatar"]."' width='50' height='50' class='img-circle' alt='Avatar'>";
                                $agregar_integrante = "<button type='button' agregar_integrante_grupo_trabajo_estado_actual='Por agregar' agregar_integrante_grupo_trabajo_id_usuario='".$item["id"]."' agregar_integrante_grupo_trabajo_id='".$datos["formulario_agregar_integrante_grupo_trabajo_id"]."' agregar_integrante_grupo_trabajo_auditoria_usuario='".$auditoria_usuario."' class='btn btn-success btn-sm agregar-integrante-grupo-trabajo' title='Agregar'>Agregar</button>";
                                $acciones = "<div class='btn-group'>".$agregar_integrante."</div>";
                                echo'<tr>
                                        <td>'.$avatar.'</td>
                                        <td>'.$item["nombres"].'</td>
                                        <td>'.ltrim(rtrim($item["cargo"])).'</td>
                                        <td>'.$acciones.'</td>
                                    </tr>';
                            }
                        }
                echo '</tbody>
                </table>
            </div>';
        echo " <script type='text/javascript'>
                $(document).ready(function() {
                    var table = $('#tabla-agregar-integrante').DataTable( {
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

    static public function agregar_integrante_grupo_trabajo_controlador($datos){
        date_default_timezone_set("America/Bogota");
        $auditoria_creado = date("Y-m-d H:i:s");
        $consulta_grupo_agente = Grupo_Trabajo_Modelo::consulta_grupo_usuario_modelo("usuario_grupo_trabajo", $datos["agregar_integrante_grupo_trabajo_id_usuario"], $datos["agregar_integrante_grupo_trabajo_id"]);
        if($consulta_grupo_agente != NULL){
            $nuevo_estado = 2;
            if($consulta_grupo_agente["estado"] == 2){
                $nuevo_estado = 1;
            }
            $datos = array("agente"=> $datos["agregar_integrante_grupo_trabajo_id_usuario"],
            "grupo"=>  $datos["agregar_integrante_grupo_trabajo_id"],
            "auditoria_usuario"=>  $datos["agregar_integrante_grupo_trabajo_auditoria_usuario"],
            "estado"=> $nuevo_estado);
            $respuesta = Grupo_Trabajo_Modelo::actualizar_estado_grupo_usuario_modelo("usuario_grupo_trabajo", $datos);
        }else {
            $datos = array("agente"=> $datos["agregar_integrante_grupo_trabajo_id_usuario"],
                "grupo"=>  $datos["agregar_integrante_grupo_trabajo_id"],
                "auditoria_usuario"=>  $datos["agregar_integrante_grupo_trabajo_auditoria_usuario"],
                "auditoria_creado"=>  $auditoria_creado);
            $respuesta = Grupo_Trabajo_Modelo::registro_agente_grupo_modelo("usuario_grupo_trabajo", $datos, $auditoria_creado);
        }
        return $respuesta;
    }

    static public function formulario_modificar_grupo_trabajo_controlador($datos){
        $consulta = Grupo_Trabajo_Modelo::consultar_detalle_grupo_trabajo_modelo("grupo_trabajo", $datos["formulario_modificar_grupo_trabajo_id"]);
        echo '<div class="box-body">
                <form method="POST">
                    <div class="form-group has-feedback">
                        <label class="control-label">Nombre</label>
                        <input type="hidden" id="editar_grupo_trabajo_id" value="'.$datos["formulario_modificar_grupo_trabajo_id"].'">
                        <input type="text" class="form-control" id="editar_grupo_trabajo_nombre" placeholder="Nombre del grupo" value="'.$consulta["grupo_trabajo"].'">
                        <span class="glyphicon glyphicon-pencil form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label">Tipo</label>
                        <select class="form-control" id="editar_grupo_trabajo_tipo" required>';
                        if($consulta["tipo"] == 1){
                            echo '<option value="1">Agentes</option>
                                  <option value="2">Solicitantes</option>';
                        }else{
                            echo '<option value="2">Solicitantes</option>
                                  <option value="1">Agentes</option>';
                        }
                    echo'</select>
                    </div>';
                    if($consulta["supervisor"] != NULL){
                        $consultar_nombre_supervisor = Grupo_Trabajo_Modelo::consultar_nombre_usuario_modelo("cliente", $consulta["supervisor"]);
                        echo '<div class="form-group has-feedback">
                                <label class="control-label">Supervisor del grupo</label>
                                <input type="text" class="form-control" disabled value="'.$consultar_nombre_supervisor["nombres"].'">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>';
                    }
                    $integrantes_grupo = Grupo_Trabajo_Modelo::consultar_integrante_grupo_trabajo_modelo("usuario_grupo_trabajo", $datos["formulario_modificar_grupo_trabajo_id"]);
                    if($integrantes_grupo != NULL){
                        echo '<hr><h3>'.count($integrantes_grupo).' Integrantes:</h3>';
                        foreach ($integrantes_grupo as $integrantes => $integrante) {
                            echo '<div class="form-group has-feedback">
                                    <input type="text" class="form-control" disabled value="'.$integrante["nombres"].'">
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                </div>';
                        }
                    }
            echo'</form>
            </div>';
    }

    static public function editar_grupo_trabajo_controlador($datos){
        $auditoria_usuario = $_COOKIE["usuario"];
        $datos = array("id"=> $datos["editar_grupo_trabajo_id"],
            "grupo_trabajo"=>  $datos["editar_grupo_trabajo_nombre"],
            "auditoria_usuario"=>  $auditoria_usuario,
            "tipo"=>  $datos["editar_grupo_trabajo_tipo"]);
        $respuesta = Grupo_Trabajo_Modelo::editar_grupo_trabajo_modelo("grupo_trabajo", $datos);
        return $respuesta;
    }

    static public function consultar_mi_grupo_trabajo_controlador(){
        $usuario = $_COOKIE["usuario"];
        $consulta = Grupo_Trabajo_Modelo::consultar_mi_grupo_trabajo_modelo("grupo_trabajo", $usuario);
        foreach ($consulta as $row => $grupo) {
            $integrantes_grupo = Grupo_Trabajo_Modelo::consultar_integrante_grupo_trabajo_modelo("usuario_grupo_trabajo", $grupo["id"]);
            echo '<section class="col-xs-12 col-md-3 d-block">
                    <div class="box box-default">
                        <div class="box-body">
                            <div class="box-body box-profile">
                                <img class="group-profile-img img-responsive img-circle" src="vista/img/avatar/groupo-trabajo.png" alt="User profile picture">
                                <h3 class="profile-username text-center">'.$grupo["grupo_trabajo"].'</h3>
                                <p class="text-muted text-center">Integrantes: '.count($integrantes_grupo).'</p>
                                <a href="#" class="btn btn-warning btn-block imagen-pefil consultar-panel-detalle-grupo-trabajo" consulta_panel_detalle_grupo_trabajo_id="'.$grupo["id"].'"><b>Ver más</b></a>
                            </div>
                        </div>
                    </div>
                </section>';
        }
    }

    static public function consulta_panel_detalle_grupo_trabajo_controlador($grupo){
        $usuario = $_COOKIE["usuario"];
        $raiz = $_COOKIE["raiz"];
        $identificador_integrantes = "";
        $consulta = Grupo_Trabajo_Modelo::consultar_detalle_grupo_trabajo_modelo("grupo_trabajo", $grupo);
        $integrantes_grupo = Grupo_Trabajo_Modelo::consultar_integrante_grupo_trabajo_modelo("usuario_grupo_trabajo", $consulta["id"]);
        echo ' <div class="box box-default">
                    <div class="box-header with-border">
                        <section class="content-header">
                            <h1>
                                <a href="mi-grupo-trabajo"><i class="fas fa-arrow-circle-left"></i></a>
                                '.$consulta["grupo_trabajo"].'
                            </h1>
                        </section>
                    </div>
                </div>
                <div class="row">';
        foreach ($integrantes_grupo as $row => $integrante) {
            if($row > 0){
                $identificador_integrantes = $identificador_integrantes.','.$integrante["id_usuario"];
            }else{
                $identificador_integrantes = $identificador_integrantes.$integrante["id_usuario"];
            }
            if($integrante["tipo"] == 2){
                $titulo_data_table = "Casos generados por los integrantes del grupo";
                $tipo = 2;
                $cantidad_casos = Grupo_Trabajo_Modelo::consultar_cantidad_casos_creados_modelo("cliente", $integrante["id_usuario"]);
                echo '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div class="box box-default">
                            <div class="box-body">
                                <div style="height: 290px;">
                                    <img class="group-profile-img img-responsive img-circle" src="'.$raiz.'vista/img/avatar/'.$integrante["avatar"].'" alt="User profile picture">
                                    <h3 class="profile-username text-center">'.$integrante["nombres"].'</h3>
                                    <p class="text-muted text-center">'.$integrante["cargo"].'</p>
                                </div>
                                <hr>
                                <div class="form-group has-feedback">
                                    <label class="control-label">Casos en ejecución</label>
                                    <input type="text" class="form-control" value="'.$cantidad_casos["casos_creados"].'" disabled>
                                    <span class="form-control-feedback glyphicon glyphicon-th-list"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label">Casos para calificar</label>
                                    <input type="text" class="form-control" value="'.$cantidad_casos["casos_resueltos"].'" disabled>
                                    <span class="form-control-feedback glyphicon glyphicon-ok"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label">Casos calificados</label>
                                    <input type="text" class="form-control" value="'.$cantidad_casos["casos_cerrados"].'" disabled>
                                    <span class="form-control-feedback glyphicon glyphicon-star"></span>
                                </div>
                            </div>
                        </div>
                    </div>';
            }else{
                $titulo_data_table = "Casos activos asignados a los integrantes del grupo";
                $tipo = 1;
                $cantidad_casos = Grupo_Trabajo_Modelo::consultar_cantidad_casos_asignados_modelo("usuario", $integrante["id_usuario"]);
                echo '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                        <div class="box box-default">
                            <div class="box-body">
                                <div style="height: 290px;">
                                    <img class="group-profile-img img-responsive img-circle" src="'.$raiz.'vista/img/avatar/'.$cantidad_casos["avatar"].'" alt="User profile picture">
                                    <h3 class="profile-username text-center">'.$integrante["nombres"].'</h3>
                                    <p class="text-muted text-center">'.$integrante["cargo"].'</p>
                                </div>
                                <hr>
                                <div class="form-group has-feedback">
                                    <a href="#" title="Consultar casos asignados" data-toggle="modal" consultar_casos_asignados_agente_id="'.$integrante["id_usuario"].'" data-target="#modal_panel_detalle_grupo_trabajo_casos_asignados" class="consultar-casos-asignados-agente">
                                        <label class="control-label">Casos asignados</label>
                                        <input type="text" class="form-control" value="'.$cantidad_casos["casos_asignados"].'" disabled>
                                        <span class="form-control-feedback glyphicon glyphicon-th-list"></span>
                                    </a>
                                </div>
                                <div class="form-group has-feedback">
                                    <a href="#" title="Consultar casos resueltos" data-toggle="modal" consultar_casos_resueltos_agente_id="'.$integrante["id_usuario"].'" data-target="#modal_panel_detalle_grupo_trabajo_casos_asignados" class="consultar-casos-resueltos-agente">
                                        <label class="control-label">Casos resueltos</label>
                                        <input type="text" class="form-control" value="'.$cantidad_casos["casos_resueltos"].'" disabled>
                                        <span class="form-control-feedback glyphicon glyphicon-ok"></span>
                                    </a>
                                </div>
                                <div class="form-group has-feedback">
                                    <a href="#" title="Consultar casos cerrados" data-toggle="modal" consultar_casos_cerrados_agente_id="'.$integrante["id_usuario"].'" data-target="#modal_panel_detalle_grupo_trabajo_casos_asignados" class="consultar-casos-cerrados-agente">
                                        <label class="control-label">Casos cerrados</label>
                                        <input type="text" class="form-control" value="'.$cantidad_casos["casos_cerrados"].'" disabled>
                                        <span class="form-control-feedback glyphicon glyphicon-star"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        }
    echo '</div>
        <div class="box box-default">
            <div class="box-body">
                <h3 class="text-uppercase">'.$titulo_data_table.'</h3>
                <hr>
                <div class="table-responsive">
                    <table id ="tabla-casos-asignados-integrantes-grupo-trabajo" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID caso</th>
                                <th>Fecha creado</th>
                                <th>Asunto</th>
                                <th>Solicitante</th>
                                <th>Agente asignado</th>
                                <th>Estado</th>
                                <th>Acciones</th>';
                        echo'</tr>
                        </thead>
                        <tbody>';
                        if($tipo == 2){
                            $respuesta = Grupo_Trabajo_Modelo::listado_solicitudes_creadas_activas_grupo_modelo("pqr_solicitud", $identificador_integrantes);
                        }else{
                            $respuesta = Grupo_Trabajo_Modelo::listado_solicitudes_activas_grupo_modelo("pqr_solicitud", $identificador_integrantes);
                        }
                        foreach($respuesta as $fila => $caso){
                            $calificacion = "";
                            $archivo = "";
                            if($caso["estado"] == "Cerrado"){
                                $color_boton_estado = "success";
                                $calificacion = "<button type='button' consulta_calificacion_id='".$caso["id"]."' class='btn btn-primary btn-sm consulta-calificacion esconder-contenido-modal-anterior-detalle' title='Consultar resultados encuesta' data-toggle='modal' data-target='#modal_consulta_calificacion'><i class='fas fa-clipboard-check'></i></button>";
                            }
                            if($caso["estado"] == "Resuelto"){
                                $color_boton_estado = "success";
                            }else if($caso["estado"] == "Asignado"){
                                $color_boton_estado = "warning";
                            }else if($caso["estado"] == "Sin asignar"){
                                $color_boton_estado = "danger";
                            }
                            if($caso["archivo"] != NULL){
                                $archivo = "<button type='button' formulario_archivo_adjunto_solicitud='".$caso["id"]."' class='btn btn-warning btn-sm esconder-contenido-modal-anterior-detalle formulario-adjunto-solicitud' title='Archivos ajuntos' data-toggle='modal' data-target='#modal_adjunto_solicitud'><i class='fas fa-folder-open'></i></button>";
                            }
                            $historial = "<button type='button' historial_respuesta_id_cliente='".$caso["id"]."' class='btn btn-info btn-sm consulta-cliente-solicitud' title='Historial de respuestas' data-toggle='modal' data-target='#modal_historial_respuesta'><i class='fas fa-list'></i></button>";
                            $ver_detalle = "<button type='button' formulario_detalle_solicitud='".$caso["id"]."' formulario_detalle_auditoria_usuario='".$usuario."' class='btn btn-primary btn-sm formulario-detalle-solicitud' title='Ver detalle' data-toggle='modal' data-target='#modal_detalle_solicitud'><i class='fas fa-edit'></i></button>";
                            $estado = "<a class='btn btn-".$color_boton_estado." disabled'> ".$caso["estado"]." </a>";
                            $establecer_fechas_resolucion = "<button type='button' formulario_establecer_fecha_resolucion_solicitud='".$caso["id"]."' class='btn btn-danger btn-sm formulario-establecer-fecha-resolucion id_boton_establecer_fecha_resolucion_solicitud_".$caso["id"]."' title='Establecer fecha de resolución' data-toggle='modal' data-target='#modal_establecer_fecha_resolucion'><i class='fas fa-calendar-week'></i></button>";
                            if($tipo == 2 || $caso["fecha_estimada_resuelto"] != NULL){
                                $establecer_fechas_resolucion = "";
                            }
                            $acciones = "<div class='btn-group'>".$calificacion.$archivo.$historial.$ver_detalle.$establecer_fechas_resolucion."</div>";
                            $fecha_creado = date_create($caso["auditoria_creado"]);
                            $fecha_creado = date_format($fecha_creado, "d/m/Y h:iA");
                            echo'<tr>
                                    <td>'.$caso["id"].'</td>
                                    <td>'.$fecha_creado.'</td>
                                    <td>'.$caso["asunto"].'</td>
                                    <td>'.$caso["solicitante"].'</td>
                                    <td>'.$caso["asignado"].'</td>
                                    <td>'.$estado.'</td>
                                    <td>'.$acciones.'</td>
                                </tr>';
                        }
                echo '</tbody>
                </div>  
            </div>
        </div>';
        echo '<div id="modal_historial_respuesta" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background:var(--color-principal); color:white">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Historial respuestas</h4>
                        </div>
                        <section class="content" style="background-color: #ecf0f5;">
                            <div class="row">
                                <section class="col-xs-12">
                                    <div class="box-body" id="formulario-modal-cliente-solicitud">
                                    </div>
                                </section>
                            </div>
                        </section>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal_detalle_solicitud" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background:var(--color-principal); color:white">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Ver detalle</h4>
                        </div>
                        <div class="modal-body text-center">
                            <div class="box-body" id="formulario-detalle-solicitud">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                            <button type="button" id="guardar-cambios-solicitud" class="btn btn-success guardar-cambios-solicitud">Guardar cambios</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal_consulta_calificacion" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background:var(--color-principal); color:white">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Consulta calificación</h4>
                        </div>
                        <div class="modal-body text-center">
                            <div class="box-body" id="formulario-modal-calificacion-cliente">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal_establecer_fecha_resolucion" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background:var(--color-principal); color:white">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Establecer fecha de resolución</h4>
                        </div>
                        <div class="modal-body text-center">
                            <div class="box-body" id="formulario-modal-establecer-fecha-resolucion">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="establecer-fecha-resolucion" refrescar_panel_detalle_grupo_trabajo_id="'.$grupo.'" class="btn btn-success establecer-fecha-resolucion-supervisor-grupo establecer-fecha-resolucion">Establecer fecha</button>
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal_adjunto_solicitud" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background:var(--color-principal); color:white">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Archivos adjuntos al caso</h4>
                        </div>
                        <div class="modal-body text-center">
                            <div class="box-body" id="formulario-modal-adjunto-solicitud">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                        </div>
                    </div>
                </div>
            </div>';
        echo " <script type='text/javascript'>
            $(document).ready(function() {
                var table = $('#tabla-casos-asignados-integrantes-grupo-trabajo').DataTable( {
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
                },
                order: [[ 0, "desc" ]]';
            echo "} );
            } );
        </script>";
    }

    static public function consultar_gestion_casos_agente_controlador($agente, $estado){
        $usuario = $_COOKIE["usuario"];
        if($estado == "Asignado"){
            $respuesta = Grupo_Trabajo_Modelo::consultar_casos_asignados_agente_modelo("pqr_solicitud", $agente);
            $id_tabla = "tabla-gestion-casos-asignados-agente";
            $color_boton_estado = "warning";
        }elseif($estado == "Resuelto"){
            $respuesta = Grupo_Trabajo_Modelo::consultar_casos_gestionados_agente_modelo("pqr_solicitud", $agente, 4);
            $id_tabla = "tabla-gestion-casos-resuelto-agente";
            $color_boton_estado = "success";
        }elseif($estado == "Cerrado"){
            $respuesta = Grupo_Trabajo_Modelo::consultar_casos_gestionados_agente_modelo("pqr_solicitud", $agente, 5);
            $id_tabla = "tabla-gestion-casos-cerrado-agente";
            $color_boton_estado = "success";
        }
        $nombre_agente = "No hay casos para mostrar";
        if($respuesta != NULL){
            $nombre_agente = $respuesta[0]["asignado"];
        }
        echo '<div class="box box-default">
            <div class="box-body">
                <h3 class="text-uppercase">Casos '.$estado.'S - '.$nombre_agente.'</h3>
                <hr>
                <div class="table-responsive">
                    <table id ="'.$id_tabla.'" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID solicitud</th>
                                <th>Fecha creado</th>
                                <th>Asunto</th>
                                <th>Solicitante</th>
                                <th>Estado</th>';
                                if($estado == "Cerrado" || $estado == "Resuelto"){
                                    echo '<th>Fecha resuelto</th>';
                                }
                            echo'<th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
                        foreach($respuesta as $fila => $caso){
                            $calificacion = "";
                            $archivo = "";
                            $historial = "<button type='button' historial_respuesta_id_cliente='".$caso["id"]."' class='btn btn-info btn-sm esconder-contenido-modal-anterior-historial consulta-cliente-solicitud' title='Historial de respuestas'><i class='fas fa-list'></i></button>";
                            if($caso["estado"] == "Cerrado"){
                                $calificacion = "<button type='button' consulta_calificacion_id='".$caso["id"]."' class='btn btn-primary btn-sm consulta-calificacion esconder-contenido-modal-anterior-detalle' title='Consultar resultados encuesta' data-toggle='modal' data-target='#modal_consulta_calificacion'><i class='fas fa-clipboard-check'></i></button>";
                            }
                            if($caso["archivo"] != NULL){
                                $archivo = "<button type='button' formulario_archivo_adjunto_solicitud='".$caso["id"]."' class='btn btn-warning btn-sm esconder-contenido-modal-anterior-detalle formulario-adjunto-solicitud' title='Archivos ajuntos' data-toggle='modal' data-target='#modal_adjunto_solicitud'><i class='fas fa-folder-open'></i></button>";
                            }
                            $ver_detalle = "<button type='button' formulario_detalle_solicitud='".$caso["id"]."' formulario_detalle_auditoria_usuario='".$usuario."' class='btn btn-primary btn-sm esconder-contenido-modal-anterior-detalle formulario-detalle-solicitud' title='Ver detalle'><i class='fas fa-edit'></i></button>";
                            $estado = "<a class='btn btn-".$color_boton_estado." disabled'> ".$caso["estado"]." </a>";
                            $acciones = "<div class='btn-group'>".$calificacion.$historial.$ver_detalle.$archivo."</div>";
                            $fecha_creado = date_create($caso["auditoria_creado"]);
                            $fecha_creado = date_format($fecha_creado, "d/m/Y h:iA");
                            echo'<tr>
                                    <td>'.$caso["id"].'</td>
                                    <td>'.$fecha_creado.'</td>
                                    <td>'.$caso["asunto"].'</td>
                                    <td>'.$caso["solicitante"].'</td>
                                    <td>'.$estado.'</td>';
                                    if($caso["estado"] == "Cerrado" || $caso["estado"] == "Resuelto"){
                                        $fecha_resuelto = date_create($caso["fecha_resuelto"]);
                                        $fecha_resuelto = date_format($fecha_resuelto, "d/m/Y h:iA");
                                        echo '<td>'.$fecha_resuelto.'</td>';
                                    }
                                echo'<td>'.$acciones.'</td>
                                </tr>';
                        }
                echo '</tbody>
                </div>  
            </div>
        </div>';
        echo " <script type='text/javascript'>
            $(document).ready(function() {
                var table = $('#".$id_tabla."').DataTable( {
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
                },
                order: [[ 0, "desc" ]]';
            echo "} );
            } );
        </script>";
    }
}