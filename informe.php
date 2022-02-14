<?php
/*
Autor: Julián Rojas Bustamante
Fecha: 23/04/2021
Comentario: 
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Informe_Controlador{
    
    static public function generar_grafico_usuario_reporte(){
        date_default_timezone_set("America/Bogota");
        $fecha_actual = date("Y-m-d");
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Informe_Modelo::consulta_casos_cliente("pqr_solicitud", $fecha_primer_dia_mes, $fecha_actual);
        return $respuesta;
    }

    static public function generar_grafico_incidencia_reporte(){
        date_default_timezone_set("America/Bogota");
        $fecha_actual = date("Y-m-d");
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Informe_Modelo::consulta_casos_incidencia("pqr_solicitud", $fecha_primer_dia_mes, $fecha_actual);
        return $respuesta;
    }

    static public function generar_grafico_caso_asignado_reporte(){
        date_default_timezone_set("America/Bogota");
        $fecha_actual = date("Y-m-d");
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Informe_Modelo::consulta_casos_asignado("pqr_solicitud", $fecha_primer_dia_mes, $fecha_actual);
        return $respuesta;
    }

    static public function generar_grafico_caso_resuelto_reporte(){
        date_default_timezone_set("America/Bogota");
        $fecha_actual = date("Y-m-d");
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Informe_Modelo::consulta_casos_resuelto("pqr_solicitud", $fecha_primer_dia_mes, $fecha_actual);
        return $respuesta;
    }

    static public function generar_grafico_caso_asignado_panel_inicio(){
        $respuesta = Informe_Modelo::consulta_casos_asignado_panel_inicio("pqr_solicitud");
        return $respuesta;
    }

    static public function generar_grafico_area_caso_reporte(){
        date_default_timezone_set("America/Bogota");
        $fecha_actual = date("Y-m-d");
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Informe_Modelo::generar_grafico_area_caso_reporte_modelo("pqr_solicitud", $fecha_primer_dia_mes, $fecha_actual);
        return $respuesta;
    }

    static public function generar_grafico_encuesta_satisfaccion_calificacion($item){
        date_default_timezone_set("America/Bogota");
        $fecha_primer_dia_mes = date("Y-m-");
        $fecha_primer_dia_mes = $fecha_primer_dia_mes.'1';
        $respuesta = Informe_Modelo::generar_grafico_encuesta_satisfaccion_calificacion_modelo("pqr_cerrado", $fecha_primer_dia_mes, $item);
        $calificacion = [];
        $cantidad = [];
        foreach($respuesta as $fila => $item){
            if($item["calificacion"] == 1){
                $calificacion_nombre = "Todo estuvo mal";
            }elseif($item["calificacion"] == 2){
                $calificacion_nombre = "Malo";
            }elseif($item["calificacion"] == 3){
                $calificacion_nombre = "Regular";
            }elseif($item["calificacion"] == 4){
                $calificacion_nombre = "Bueno";
            }elseif($item["calificacion"] == 5){
                $calificacion_nombre = "Excelente";
            }
            array_push($calificacion, $calificacion_nombre);
            array_push($cantidad, $item["total"]);
        }
        $respuesta_datos = array(
            $calificacion,
            $cantidad
        );
        return $respuesta_datos;
    }

    static public function formulario_buscar_casos_controlador($datos){
        $fecha_inicial = $datos["formulario_buscar_casos_fecha_desde"].' '.$datos["formulario_buscar_casos_hora_desde"].':00';
        $fecha_final = $datos["formulario_buscar_casos_fecha_hasta"].' '.$datos["formulario_buscar_casos_hora_hasta"].':00';
        $caso_reabierto = "";
        if($datos["formulario_buscar_casos_tipo"] == 0){
            $respuesta = Informe_Modelo::consultar_exporte_informe_modelo("pqr_solicitud", $fecha_inicial, $fecha_final);
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
                            <table id ="tabla-exporte-excel" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Agente/Asignado</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Categoría</th>
                                        <th>Fecha creación</th>
                                        <th>Fecha compromiso</th>
                                        <th>Fecha actualización</th>
                                        <th>Veces reabierto</th>
                                        <th>Asunto</th>
                                        <th>Descripción</th>
                                        <th>Cliente</th>
                                        <th>Área</th>
                                        <th>Subcategoria</th>
                                        <th>Error/Falla</th>';
                                        if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                                            echo '<th>Última respuesta</th>';
                                        }
                                echo'</tr>
                                </thead>
                                <tbody>';
            foreach($respuesta as $fila => $item){
                if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                    $consultar_ultima_respuesta = Informe_Modelo::consultar_ultima_respuesta_agente_modelo("pqr_respuesta", $item["id"]);
                }
                echo'<tr>
                        <td>'.$item["id"].'</td>
                        <td>'.$item["agente"].'</td>
                        <td>'.$item["prioridad"].'</td>
                        <td>'.$item["estado"].'</td>
                        <td>'.$item["categoria"].'</td>
                        <td>'.$item["fecha_creado"].'</td>
                        <td>'.$item["fecha_estimada_resuelto"].'</td>
                        <td>'.$item["auditoria_modificado"].'</td>
                        <td>'.$item["reabierto"].'</td>
                        <td>'.$item["asunto"].'</td>
                        <td>'.$item["descripcion"].'</td>
                        <td>'.$item["cliente"].'</td>
                        <td>'.$item["area"].'</td>
                        <td>'.$item["subcategoria"].'</td>
                        <td>'.$item["Error_falla"].'</td>';
                        if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                            if ($consultar_ultima_respuesta != NULL) {
                                echo '<td>'.$consultar_ultima_respuesta["respuesta"].'</td>';
                            }else{
                                echo '<td> </td>';
                            }
                        }
                echo'</tr>';
            }
                echo '</tbody>
                </table>
            </div>';
        }else if($datos["formulario_buscar_casos_tipo"] == 1){
            $respuesta = Informe_Modelo::consultar_exporte_casos_resueltos_informe_modelo("pqr_solicitud", $fecha_inicial, $fecha_final);
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
                            <table id ="tabla-exporte-excel" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Agente/Asignado</th>
                                        <th>Horas en estado actual</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Categoría</th>
                                        <th>Fecha creación</th>
                                        <th>Fecha compromiso</th>
                                        <th>Fecha resuelto</th>
                                        <th>Horas de resolución</th>
                                        <th>Veces reabierto</th>
                                        <th>Asunto</th>
                                        <th>Descripción</th>
                                        <th>Cliente</th>
                                        <th>Área</th>
                                        <th>Subcategoria</th>
                                        <th>Error/Falla</th>';
                                        if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                                            echo '<th>Última respuesta</th>';
                                        }
                                echo'</tr>
                                </thead>
                                <tbody>';
            foreach($respuesta as $fila => $item){
                if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                    $consultar_ultima_respuesta = Informe_Modelo::consultar_ultima_respuesta_agente_modelo("pqr_respuesta", $item["id"]);
                }
                $tiempo_empleado_resolver_caso = "En ejecución";
                $formato = '%m meses %d días %h horas %i minutos %s segundos';
                $formato_dia = '%d';
                $formato_hora = '%h';
                $formato_minutos = '%i';
                $acumulado_minutos = 0;
                date_default_timezone_set("America/Bogota");
                $fecha_actual = date("Y-m-d H:i:s");
                $fecha_actual = date_create($fecha_actual);
                $fecha_actualizado = date_create($item["fecha_actualizado"]);
                $diferencia_estado_actual = date_diff($fecha_actualizado, $fecha_actual);
                $tiempo_estado_actual_dia = $diferencia_estado_actual->format($formato_dia);
                $tiempo_estado_actual_hora = $diferencia_estado_actual->format($formato_hora);
                $horas_estado_actual_horas = ($tiempo_estado_actual_dia * 24) + $tiempo_estado_actual_hora;
                $tiempo_resuelto_hora_total = "";
                if(($item["estado"] == "Resuelto" || $item["estado"] == "Cerrado") && $item["reabierto"] == 0){
                    $fecha_resuelto = date_create($item["fecha_resuelto"]);
                    $auditoria_creado = date_create($item["fecha_creado"]);
                    $diferencia_resuelto = date_diff($auditoria_creado, $fecha_resuelto);
                    $tiempo_resuelto_dia = $diferencia_resuelto->format($formato_dia);
                    $tiempo_resuelto_hora = $diferencia_resuelto->format($formato_hora);
                    $tiempo_resuelto_minutos = $diferencia_resuelto->format($formato_minutos);
                    $tiempo_resuelto_hora_total = ($tiempo_resuelto_dia * 24) + $tiempo_resuelto_hora;
                    $fecha_creado = date_create($item["fecha_creado"]);
                    $diferencia = date_diff($fecha_creado, $fecha_actualizado);
                    $tiempo_empleado_resolver_caso = $diferencia->format($formato);
                    $tiempo_resuelto_hora_total += ($tiempo_resuelto_minutos/60);
                }else if ($item["reabierto"] > 0 && ($item["estado"] == "Resuelto" || $item["estado"] == "Cerrado")) {
                    $fecha_resuelto = date_create($item["fecha_resuelto"]);
                    $auditoria_creado = date_create($item["fecha_creado"]);
                    $diferencia_resuelto = date_diff($auditoria_creado, $fecha_resuelto);
                    $tiempo_resuelto_dia = $diferencia_resuelto->format($formato_dia);
                    $tiempo_resuelto_hora = $diferencia_resuelto->format($formato_hora);
                    $tiempo_resuelto_minutos = $diferencia_resuelto->format($formato_minutos);
                    $acumulado_minutos += $tiempo_resuelto_minutos;
                    $tiempo_resuelto_hora_total = ($tiempo_resuelto_dia * 24) + $tiempo_resuelto_hora;
                    $fecha_creado = date_create($item["fecha_creado"]);
                    $diferencia = date_diff($fecha_creado, $fecha_actualizado);
                    $tiempo_empleado_resolver_caso = intval($tiempo_resuelto_hora_total);
                    $consultar_reapertura = Informe_Modelo::consultar_caso_veces_reabierto_modelo("pqr_reabierto", $item["id"]);
                    foreach ($consultar_reapertura as $row => $reapertura) {
                        $fecha_reapertura = date_create($reapertura["fecha_reapertura"]);
                        $fecha_resuelto = date_create($reapertura["fecha_resuelto"]);
                        $diferencia_resuelto = date_diff($fecha_reapertura, $fecha_resuelto);
                        $tiempo_resuelto_dia = $diferencia_resuelto->format($formato_dia);
                        $tiempo_resuelto_hora = $diferencia_resuelto->format($formato_hora);
                        $tiempo_resuelto_minutos = $diferencia_resuelto->format($formato_minutos);
                        $acumulado_minutos += $tiempo_resuelto_minutos;
                        $tiempo_resuelto_hora_total = ($tiempo_resuelto_dia * 24) + $tiempo_resuelto_hora;
                        $fecha_reapertura = date_create($reapertura["fecha_reapertura"]);
                        $diferencia = date_diff($fecha_reapertura, $fecha_actualizado);
                        $tiempo_empleado_resolver_caso_reabierto = $diferencia->format($formato);
                        $tiempo_empleado_resolver_caso += intval($tiempo_resuelto_hora_total);
                    }
                    $tiempo_resuelto_hora_total = $tiempo_empleado_resolver_caso + $acumulado_minutos/60;
                }
                if($item["estado"] == "Resuelto" || $item["estado"] == "Cerrado"){
                    if($item["fecha_resuelto"] == NULL || $item["fecha_resuelto"] == ""){
                        $consultar_fecha_resuelto = Informe_Modelo::consultar_fecha_resuelto_modelo("pqr_respuesta", $item["id"]);
                        if($consultar_fecha_resuelto != NULL) {
                            $registrar_fecha_resuelto = Informe_Modelo::registrar_fecha_resuelto_modelo("pqr_solicitud", $item["id"], $consultar_fecha_resuelto["auditoria_creado"]);
                        }
                    }
                }
                echo'<tr>
                        <td>'.$item["id"].'</td>
                        <td>'.$item["agente"].'</td>
                        <td>'.$horas_estado_actual_horas.'</td>
                        <td>'.$item["prioridad"].'</td>
                        <td>'.$item["estado"].'</td>
                        <td>'.$item["categoria"].'</td>
                        <td>'.$item["fecha_creado"].'</td>
                        <td>'.$item["fecha_estimada_resuelto"].'</td>
                        <td>'.$item["fecha_resuelto"].'</td>
                        <td>'.$tiempo_resuelto_hora_total.'</td>
                        <td>'.$item["reabierto"].'</td>
                        <td>'.$item["asunto"].'</td>
                        <td>'.$item["descripcion"].'</td>
                        <td>'.$item["cliente"].'</td>
                        <td>'.$item["area"].'</td>
                        <td>'.$item["subcategoria"].'</td>
                        <td>'.$item["Error_falla"].'</td>';
                        if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                            if ($consultar_ultima_respuesta != NULL) {
                                echo '<td>'.$consultar_ultima_respuesta["respuesta"].'</td>';
                            }else{
                                echo '<td> </td>';
                            }
                        }
                echo'</tr>';
            }
                echo '</tbody>
                </table>
            </div>';
            
        }else if($datos["formulario_buscar_casos_tipo"] == 2){
            $respuesta = Informe_Modelo::consultar_exporte_casos_cerrados_informe_modelo("pqr_solicitud", $fecha_inicial, $fecha_final);
            echo '<div class="box-header with-border">
                        <h3 class="box-title">Resultado de búsqueda</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id ="tabla-exporte-excel" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Agente/Asignado</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Categoría</th>
                                        <th>Fecha creación</th>
                                        <th>Fecha compromiso</th>
                                        <th>Fecha resuelto</th>
                                        <th>Asunto</th>
                                        <th>Descripción</th>
                                        <th>Cliente</th>
                                        <th>Área</th>
                                        <th>Subcategoria</th>
                                        <th>Error/Falla</th>
                                        <th>Fecha cierre</th>
                                        <th>Calificación Calidad</th>
                                        <th>Calificacion Cumplimiento</th>
                                        <th>Observación</th>';
                                        if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                                            echo '<th>Última respuesta del agente</th>';
                                        }
                                echo'</tr>
                                </thead>
                                <tbody>';
                                foreach($respuesta as $fila => $item){
                                    if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                                        $consultar_ultima_respuesta = Informe_Modelo::consultar_ultima_respuesta_agente_modelo("pqr_respuesta", $item["id"]);
                                    }
                                    echo'<tr>
                                            <td>'.$item["id"].'</td>
                                            <td>'.$item["agente"].'</td>
                                            <td>'.$item["prioridad"].'</td>
                                            <td>'.$item["estado"].'</td>
                                            <td>'.$item["categoria"].'</td>
                                            <td>'.$item["fecha_creado"].'</td>
                                            <td>'.$item["fecha_estimada_resuelto"].'</td>
                                            <td>'.$item["fecha_resuelto"].'</td>
                                            <td>'.$item["asunto"].'</td>
                                            <td>'.$item["descripcion"].'</td>
                                            <td>'.$item["cliente"].'</td>
                                            <td>'.$item["area"].'</td>
                                            <td>'.$item["subcategoria"].'</td>
                                            <td>'.$item["Error_falla"].'</td>
                                            <td>'.$item["fecha_cierre"].'</td>
                                            <td>'.$item["calificacion_calidad"].'</td>
                                            <td>'.$item["calificacion_cumplimiento"].'</td>
                                            <td>'.$item["observacion"].'</td>';
                                            if($datos["formulario_buscar_casos_ultima_respuesta_agente"] != 0){
                                                if ($consultar_ultima_respuesta != NULL) {
                                                    echo '<td>'.$consultar_ultima_respuesta["respuesta"].'</td>';
                                                }else{
                                                    echo '<td> </td>';
                                                }
                                            }
                                    echo'</tr>';
                                }
                        echo '</tbody>
                        </table>
                    </div>';
        }
        echo " <script type='text/javascript'>
                $(document).ready(function() {
                    $('#tabla-exporte-excel thead tr').clone(true).appendTo( '#tabla-exporte-excel thead' );
                    $('#tabla-exporte-excel thead tr:eq(1) th').each( function (i) {
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
                    var table = $('#tabla-exporte-excel').DataTable( {
                        orderCellsTop: true,
                        fixedHeader: true,
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5', 'excelHtml5', 'pdfHtml5', 'csvHtml5'
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

    static public function consultar_exporte_casos_cerrados_informe_controlador(){
        $respuesta = Informe_Modelo::consultar_exporte_casos_cerrados_informe_modelo("pqr_solicitud");
        foreach($respuesta as $fila => $item){
            echo'<tr>
                    <td>'.$item["id"].'</td>
                    <td>'.$item["agente"].'</td>
                    <td>'.$item["prioridad"].'</td>
                    <td>'.$item["estado"].'</td>
                    <td>'.$item["categoria"].'</td>
                    <td>'.$item["fecha_creado"].'</td>
                    <td>'.$item["fecha_actualizado"].'</td>
                    <td>'.$item["asunto"].'</td>
                    <td>'.$item["descripcion"].'</td>
                    <td>'.$item["cliente"].'</td>
                    <td>'.$item["area"].'</td>
                    <td>'.$item["subcategoria"].'</td>
                    <td>'.$item["Error_falla"].'</td>
                    <td>'.$item["fecha_cierre"].'</td>
                    <td>'.$item["calificacion_calidad"].'</td>
                    <td>'.$item["calificacion_cumplimiento"].'</td>
                </tr>';
        }
    }
}