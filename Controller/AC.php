public function index()
    {
        $productos = DB::select("EXEC SP_Productos_licencias_Disponibles ?",[session::get('Id_Unidad_Admin')]);

        $colaboradores = DB::select('EXEC SP_Colaboradores_Seleccionar ?,?,?',[Auth::user()->Id_Usuario,null,session::get('Id_Unidad_Admin')]);
        //dd($colaboradores);
        $gruposLicencias = DB::select('SELECT * FROM Grupos_Licencias;');
        return view('Administrador.AsignacionesColaboradores',compact('gruposLicencias','colaboradores','productos'));  //Retornamos la vista Index que esta el la carpeta Ejemplo2
    }

    /**
     * Show the form for creating a new resource.
     */
    public function gruposLicencias(ShowRequest $request){
        try{
            $Id_Grupo = $request->Id_Grupo;    
            //$findIdProducto = Productos::find($request->Id_Producto);            
            //$Id_Producto=$findIdProducto->Id_Producto;
            //$Id_Producto = intval($Id_Producto);
            
            $submit = DB::select(
                'EXEC [SP_Detalle_Grupos_Licencias_Seleccionar]  ?',
                [$Id_Grupo]
            );
            // Recupera los detalles del registro con el ID proporcionado, puedes utilizar tu lógica actual para obtener los datos específicos
            
            // Retornar los detalles del producto en formato JSON
            //return response()->json($submit);
            if (!empty($submit)) {  //Validamos si la variable no viene vacía
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje, además de los datos obtenidos
                return response()->json(['status' => 'success', 'titulo' => 'Consulta exitosa', 'message' => 'Se consultó exitosamente', "datos" => $submit]);
            } else {    //Si viene vacía entonces ocurrio un error
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
                return response()->json(['status' => 'error', 'titulo' => 'Error al consultar el ejemplo', 'message' => "La BD lanzó un error<br><br>Codigo de error: " . $submit[0]->ErrorNumber . "<br><br> Procedimiento: " . $submit[0]->ErrorProcedure . "<br><br> Vuelva a intentarlo, si el problema perciste pongase en contacto con soporte"."<br><br>Mensaje de la bd: " . $submit[0]->ErrorMessage]);
            }
        }catch(Exception $e){       //Si se generá un error en la ejecución
            //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
            return response()->json(['status' => 'error', 'titulo' => 'Entra al catch de show Error al consultar el Producto', "message" => "<br>Código de error: " . $e->getCode() . "<br><br>El sistema arrojó el mensaje: " .$e->getMessage()]);
        }
      
    }
    public function consultarLicencias(ShowRequest $request){
        try{
            $Id_Grupo = $request->Id_Grupo;    
            $submit = DB::select(
                'EXEC [SP_Activos_Por_Grupo_Seleccionar]  ?',
                [$Id_Grupo]
            );
            // Recupera los detalles del registro con el ID proporcionado, puedes utilizar tu lógica actual para obtener los datos específicos
            
            // Retornar los detalles del producto en formato JSON
            //return response()->json($submit);
            if (!empty($submit)) {  //Validamos si la variable no viene vacía
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje, además de los datos obtenidos
                return response()->json(['status' => 'success', 'titulo' => 'Consulta exitosa', 'mensaje' => 'Se consultó exitosamente', "datos" => $submit]);
            } else {    //Si viene vacía entonces ocurrio un error
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
                return response()->json(['status' => 'error', 'titulo' => 'Error al consultar el ejemplo', 'mensaje' => "La BD lanzó un error<br><br>Codigo de error: " . $submit[0]->ErrorNumber . "<br><br> Procedimiento: " . $submit[0]->ErrorProcedure . "<br><br> Vuelva a intentarlo, si el problema perciste pongase en contacto con soporte"."<br><br>Mensaje de la bd: " . $submit[0]->ErrorMessage]);
            }
        }catch(Exception $e){       //Si se generá un error en la ejecución
            //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
            return response()->json(['status' => 'error', 'titulo' => 'Entra al catch de show Error al consultar el Producto', "mensaje" => "<br>Código de error: " . $e->getCode() . "<br><br>El sistema arrojó el mensaje: " .$e->getMessage()]);
        }
      
    }
    

    public function store(StoreRequest $request)
    {
        try {
           // DB::beginTransaction(); // Iniciar la transacción
            $detalles = json_decode($request->input('arreglo'));
            $productosNoDisponibles = []; // Arreglo para almacenar los nombres de los productos no disponibles
            $asignadosCorrectamente = [];
            if(empty($detalles)){
                return response()->json(['status' => 'Warning', 'titulo' => 'Seleccione un producto', 'mensaje' => "No hay productos seleccionados, por favor agregue almenos un producto a la lista "  .  "<br><br> Puede agregar un grupo de productos, un producto o buscar un Número de serie <br><br> Si el problema persiste, póngase en contacto con soporte"]);
            }

            //dd($detalles);
            foreach ($detalles as $detalle) {
                
                $Activo = DB::select('SELECT TOP 1 a.Id_Activo FROM Activos A JOIN Asignaciones_Unidades as au on au.Id_Activo = A.Id_Activo  WHERE au.Id_Unidad_Admin = '. session::get('Id_Unidad_Admin') .'AND A.Id_Producto ='.$detalle->Id_Producto .' AND A.Estatus = 1 AND A.Estatus_Asignacion = 0 ;');
           
               // $Activo = DB::select('SELECT Id_Activo FROM Activos A WHERE Id_Producto = '. $detalle->Id_Producto.' AND A.Estatus = 1 AND A.Estatus_Asignacion = 0;');
                
                if (empty($Activo)) {
                    $productosNoDisponibles[] = $detalle->Nombre_Producto; // Agregar el nombre del producto al arreglo
                } else {
                    $Id_Producto = $detalle->Id_Producto;
                    $Colaborador = $detalle->colaborador;
                    $Id_Activo = $detalle->Id_activo;
                   
                    //$FechaFin = $detalle->Fecha_Fin;
                    $FechaFin = empty($detalle->Fecha_Fin) ? '3000-01-01' : $detalle->Fecha_Fin;
                    //if($FechaFin = ""){
                    //$FechaFin = '3000-12-01';   
                    $submit = DB::select('EXEC SP_Asignacion_Activos_Colaboradores_Insertar ?,?,?,?,?,?', [$Id_Producto, $Colaborador,  $FechaFin, $Id_Activo, Auth::user()->Id_Usuario, session::get('Id_Unidad_Admin')]);
                    //}
                    //dd($FechaFin);   
                    if ($submit[0]->respuesta == 'Consulta Exitosa')
                    {
                        $asignadosCorrectamente[] = $detalle->Nombre_Producto; 
                      
                    }
                }        
            }           
            if (!empty($productosNoDisponibles)) {
                // Si hay productos no disponibles, mostrar la advertencia con los nombres de los productos
                $mensaje = 'Debes agregar activos de los siguientes productos:<br>';
                foreach ($productosNoDisponibles as $producto) {
                    $mensaje .= $producto . '<br>';
                }
                if(!empty($asignadosCorrectamente)){
                    $mensaje.='<br> Asignados con éxito: ';
                    foreach ($asignadosCorrectamente as $correctos) {
                        $mensaje .= $correctos . '<br>';
                    } 
                }
                $mensaje .= 'Contacta a tu proveedor o haz una solicitud de cotización';
                return response()->json(['status' => 'warning', 'titulo' => 'No hay activos disponibles de algunos productos', 'mensaje' => $mensaje]);
            } else if ($submit[0]->respuesta == 'Consulta Exitosa') {
                // Si todos los productos están disponibles y la consulta se realizó con éxito
               // DB::commit(); // Confirmar la transacción        
                return response()->json(['status' => 'success', 'titulo' => 'Registro exitoso', 'mensaje' => 'Se registró el ejemplo exitosamente']);
            }else if($submit[0]->respuesta =='ActivoNoDisponible'){
                $mensaje = 'Debes agregar activos de los siguientes productos:<br>';
                foreach ($productosNoDisponibles as $producto) {
                    $mensaje .= $producto . '<br>';
                }
                if(!empty($asignadosCorrectamente)){
                    $mensaje.='<br> Asignados con éxito: ';
                    foreach ($asignadosCorrectamente as $correctos) {
                        $mensaje .= $correctos . '<br>';
                    } 
                }
                $mensaje .= 'Contacta a tu proveedor o haz una solicitud de cotización';
                return response()->json(['status' => 'warning', 'titulo' => 'No hay activos disponibles de algunos productos', 'mensaje' => $mensaje]);

            } 
            else if ($submit[0]->respuesta == 'Error') {
                // Si la consulta no se realizó con éxito
                return response()->json(['status' => 'error', 'titulo' => 'Error al registrar el ejemplo', 'mensaje' => "La BD lanzó un error<br><br>Código de error: " . $submit[0]->ErrorNumber . "<br><br>Mensaje de la bd: " . $submit[0]->ErrorMessage . "<br><br> Procedimiento: " . $submit[0]->ErrorProcedure . "<br><br> Vuelva a intentarlo, si el problema persiste, póngase en contacto con soporte"]);
            }
        } catch (Exception $e) {
            // Si se captura un error durante la ejecución
            //DB::rollback();
            return response()->json(['status' => 'error', 'titulo' => 'Error al registrar el ejemplo', 'mensaje' => "<br>Código de error: " . $e->getCode() . "<br><br>El sistema arrojó el mensaje: " . $e->getMessage()]);
        }         
    }
    //La intensión con este método es conseguir el detalle de activos por producto y la cantidad de activos disponibles, se devuelve cada valos en una variable diferente 
    public function licenciasDisponibles(ShowRequestLicenciasDisponibles $request)
    {
        try{
            $Id_Unidad_Admin = session::get('Id_Unidad_Admin');
            $Id_Producto = $request->Id_Producto;   
            $productosNoDisponibles = []; // Arreglo para almacenar los nombres de los productos no disponibles
            
            $submit = DB::select(
                'EXEC [SP_Activos_Disponibles] ?,?',
                [$Id_Producto,$Id_Unidad_Admin]
            );
            if (empty($submit)){
                $productosNoDisponibles[] = $request->Nombre_Producto; // Agregar el nombre del producto al arreglo

            }
            
           if ($submit[0]->Licencias != '0'){
                $submit2 = DB::select(
                    'EXEC SP_Asignacion_Activos_Unidades_Consultar_Detalle ?,?', [$Id_Unidad_Admin,$Id_Producto]
                ); 
            }else{
                $productosNoDisponibles[] = $request->Nombre_Producto; // Agregar el nombre del producto al arreglo
            }
            
            
           // dd($submit2);
            // Recupera los detalles del registro con el ID proporcionado, puedes utilizar tu lógica actual para obtener los datos específicos
            
            // Retornar los detalles del producto en formato JSON
 
            if (!empty($submit) && !empty($submit2) && empty($productosNoDisponibles)) {  //Validamos si la variable no viene vacía
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje, además de los datos obtenidos
                return response()->json(['status' => 'success', 'titulo' => 'Consulta exitosa', 'mensaje' => 'Se consultó exitosamente', "datos" => $submit , "activosDetalle" => $submit2]);
            }
            else if (!empty($submit) && !empty($submit2) && !empty($productosNoDisponibles)){
               // return response()->json(['status' => 'success', 'titulo' => 'Consulta exitosa', 'mensaje' => 'Se consultó exitosamente', "datos" => $submit , "activosDetalle" => $submit2]);
               $mensaje = 'Debes agregar activos de los siguientes productos:<br>';
               foreach ($productosNoDisponibles as $producto) {
                   $mensaje .= $producto . '<br>';
               }
               $mensaje .= 'Contacta a tu proveedor o haz una solicitud de cotización';
               return response()->json(['status' => 'success', 'titulo' => 'No hay activos disponibles de algunos productos', 'mensaje' => $mensaje, "datos" =>$submit,"activosDetalle" => $submit2]);

            }
            else if(!empty($productosNoDisponibles)){
                $mensaje = 'Debes agregar activos de los siguientes productos:<br>';
                foreach ($productosNoDisponibles as $producto) {
                    $mensaje .= $producto . '<br>';
                }
                $mensaje .= 'Contacta a tu proveedor o haz una solicitud de cotización';
                return response()->json(['status' => 'warning', 'titulo' => 'No hay activos disponibles de algunos productos', 'mensaje' => $mensaje, "datos" =>$submit]);
            }
             else {    //Si viene vacía entonces ocurrio un error
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
                return response()->json(['status' => 'error', 'titulo' => 'Error al consultar el ejemplo', 'mensaje' => "La BD lanzó un error<br><br>Codigo de error: " . $submit[0]->ErrorNumber . "<br><br> Procedimiento: " . $submit[0]->ErrorProcedure . "<br><br> Vuelva a intentarlo, si el problema perciste pongase en contacto con soporte"."<br><br>Mensaje de la bd: " . $submit[0]->ErrorMessage]);
            }
        }catch(Exception $e){       //Si se generá un error en la ejecución
            //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
            return response()->json(['status' => 'error', 'titulo' => 'Entra al catch de show Error al consultar el Producto', "mensaje" => "<br>Código de error: " . $e->getCode() . "<br><br>El sistema arrojó el mensaje: " .$e->getMessage()]);
        }
    }

    public function licenciasDisponiblesGrupos(ShowRequestLicenciasDisponibles $request)
    {
        try{
            $Id_Unidad_Admin = session::get('Id_Unidad_Admin');
            $Id_Producto = $request->Id_Producto;   
           
            $submit = DB::select(
                'EXEC [SP_Activos_Disponibles] ?,?',
                [$Id_Producto,$Id_Unidad_Admin]
            ); 

            if (!empty($submit)) {  //Validamos si la variable no viene vacía
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje, además de los datos obtenidos
                return response()->json(['status' => 'success', 'titulo' => 'Consulta exitosa', 'mensaje' => 'Se consultó exitosamente', "datos" => $submit]);
            }
             else {    //Si viene vacía entonces ocurrio un error
                //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
                return response()->json(['status' => 'error', 'titulo' => 'Error al consultar el ejemplo', 'mensaje' => "La BD lanzó un error<br><br>Codigo de error: " . $submit[0]->ErrorNumber . "<br><br> Procedimiento: " . $submit[0]->ErrorProcedure . "<br><br> Vuelva a intentarlo, si el problema perciste pongase en contacto con soporte"."<br><br>Mensaje de la bd: " . $submit[0]->ErrorMessage]);
            }
        }catch(Exception $e){       //Si se generá un error en la ejecución
            //Retornamos un json con los datos que podemos mostrar en una alerta status, titulo y mensaje con el error
            return response()->json(['status' => 'error', 'titulo' => 'Entra al catch de show Error al consultar el Producto', "mensaje" => "<br>Código de error: " . $e->getCode() . "<br><br>El sistema arrojó el mensaje: " .$e->getMessage()]);
        }
    }