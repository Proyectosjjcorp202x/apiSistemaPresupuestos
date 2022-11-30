<?php

require '../database.php';

class ObtieneInformacionAExportar {

    /**
     * @string No_Ppto
     */
    var $No_Ppto = '';

    /**
     * @string Cliente
     */
    var $Cliente = '';

    /**
     * @string Proyecto
     */
    var $Proyecto = '';

    /**
     * @string Plazas
     */
    var $Plazas = '';

    /**
     * @string Periodos
     */
    var $Periodos = '';

    /**
     * @string Duracion
     */
    var $Duracion = '';

    /**
     * @string Objetivo
     */
    var $Objetivo = '';

    /**
     * @array personal
     */
    var $personal = [];

    /**
     * @array degustacion
     */
    var $degustacion = [];

    /**
     * @array viaticos
     */
    var $viaticos = [];

    /**
     * @array uniformes
     */
    var $uniformes = [];

    /**
     * @array seguridad_y_trabajo
     */
    var $seguridad_y_trabajo = [];

    /**
     * @array materiales_POP
     */
    var $materiales_POP = [];

    /**
     * @array servicios
     */
    var $servicios = [];

    /**
     * @string subtotal_personal
     */
    var $subtotal_personal = '';

    /**
     * @string subtotal_degustacion
     */
    var $subtotal_degustacion = '';

    /**
     * @string subtotal_viaticos
     */
    var $subtotal_viaticos = '';

    /**
     * @string subtotal_uniformes
     */
    var $subtotal_uniformes = '';

    /**
     * @string subtotal_seguridad_y_trabajo
     */
    var $subtotal_seguridad_y_trabajo = '';

    /**
     * @string subtotal_materiales_POP
     */
    var $subtotal_materiales_POP = '';

    /**
     * @string subtotal_servicios
     */
    var $subtotal_servicios = '';

    /**
     * @string subtotal_general
     */
    var $subtotal_general = '';

    /**
     * @string comision_agencia_servicio
     */
    var $comision_agencia_servicio = '';

    /**
     * @string subtotal_antes_de_iva
     */
    var $subtotal_antes_de_iva = '';

    function ObtenerInformacion($idpresupuesto) {
        $mysql = new MysqlManager();

        $sql = "CALL proc_consultas_reporte_creacion_presupuesto ('{$idpresupuesto}');";

        $con = $mysql->connect();

// Executa consulta multiple
        if (mysqli_multi_query($con, $sql)) {
            // Extrae datos del presupuesto
            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->No_Ppto = $row['folio'];
                    $this->Cliente = $row['nombrecliente'];
                    $this->Proyecto = $row['proyecto'];
                    $this->Plazas = $row['plaza'];
                    $this->Periodos = $row['periodo'];
                    $_descripcion_duracion = ($row['descripcion_duracion'] != null) ? (trim($row['descripcion_duracion']) != '') ? $row['descripcion_duracion'] . ' ' : '' : '';
                    $_duracion = ($row['duracion'] != null) ? (trim($row['duracion']) != '') ? $row['duracion'] . ' ' : '' : '';
                    $this->Duracion = $_descripcion_duracion . '' . $_duracion;
                    //$this->Objetivo = $row['objetivo'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            /*             * ********************************************************************************************* */
            /*             * ********************** Extrae datos de cada uno de los rubros de detalles de presupuesto***** */

            //Extrae informacion del rubro de personal
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->personal[$i]['numero_de_personal'] = $row['cantidad'];
                    $this->personal[$i]['descripcion'] = $row['concepto'];
                    $this->personal[$i]['costo_unitario_pdv'] = $row['costo_unitario'];
                    $this->personal[$i]['costo_cuota_diaria_fiscal'] = $row['diario_integrado'];
                    $this->personal[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->personal[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro personal
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_personal = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            //Extrae informacion del rubro de degustacion
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->degustacion[$i]['numero'] = $row['cantidad'];
                    $this->degustacion[$i]['descripcion'] = $row['concepto'];
                    $this->degustacion[$i]['costo_unitario'] = $row['costo_unitario'];
                    $this->degustacion[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->degustacion[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro degustacion
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_degustacion = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            //Extrae informacion del rubro de viaticos
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->viaticos[$i]['numero'] = $row['cantidad'];
                    $this->viaticos[$i]['descripcion'] = $row['concepto'];
                    $this->viaticos[$i]['costo_unitario'] = $row['costo_unitario'];
                    $this->viaticos[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->viaticos[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro viaticos
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_viaticos = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            //Extrae informacion del rubro de uniformes
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->uniformes[$i]['numero'] = $row['cantidad'];
                    $this->uniformes[$i]['descripcion'] = $row['concepto'];
                    $this->uniformes[$i]['costo_unitario'] = $row['costo_unitario'];
                    $this->uniformes[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->uniformes[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro uniformes
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_uniformes = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            //Extrae informacion del rubro de seguridad y trabajo
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->seguridad_y_trabajo[$i]['numero'] = $row['cantidad'];
                    $this->seguridad_y_trabajo[$i]['descripcion'] = $row['concepto'];
                    $this->seguridad_y_trabajo[$i]['costo_unitario'] = $row['costo_unitario'];
                    $this->seguridad_y_trabajo[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->seguridad_y_trabajo[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro seguridad y trabajo
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_seguridad_y_trabajo = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            //Extrae informacion del rubro de materiales POP
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->materiales_POP[$i]['numero'] = $row['cantidad'];
                    $this->materiales_POP[$i]['descripcion'] = $row['concepto'];
                    $this->materiales_POP[$i]['costo_unitario'] = $row['costo_unitario'];
                    $this->materiales_POP[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->materiales_POP[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro materiales POP
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_materiales_POP = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            //Extrae informacion del rubro de servicios
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->servicios[$i]['numero'] = $row['cantidad'];
                    $this->servicios[$i]['descripcion'] = $row['concepto'];
                    $this->servicios[$i]['costo_unitario'] = $row['costo_unitario'];
                    $this->servicios[$i]['numero_de_dias'] = $row['no_dias'];
                    $this->servicios[$i]['total'] = $row['costo_total'];
                    $i++;
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
            // Extrae el valor calculado del subtotal del rubro servicios
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_servicios = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            // Extrae el valor calculado del subtotal de todo en general
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_general = $row['subtotal'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            // Extrae el valor calculado de la comisión de agencia servicio
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->comision_agencia_servicio = $row['comision_agencia_servicio'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }

            // Extrae el valor calculado del subtotal antes de iva
            mysqli_next_result($con);

            if ($result = mysqli_store_result($con)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->subtotal_antes_de_iva = $row['subtotal_antes_de_iva'];
                }
                mysqli_free_result($result);
            }
            // if there are more result-sets, the print a divider
            if (mysqli_more_results($con)) {
                
            }
        }

        $mysql->Close($con);
    }

}
