et btnModal = {
    "btnAgregar": document.getElementById("btnAgregar"),    //Agregamos el boton de agregar
    "btnEditar": document.getElementById("btnEditar"),      //Agregamos el boton de editar
};
let modal = document.getElementById(null);         //Guardamos el modal que utilizaremos en una variables
let formulario = document.getElementById("formulario-AsignacionesColaboradores");   //Guardamos en una variable el formulario a utilzar
let objeto = new Peticion("/AsignacionActivosColaboradores/gruposLicencias", modal, btnModal, formulario);     //Creamos el objeto de tipo peticion y le mandamos las variables ya creadas
let arrayLicencias = [];

$(document).on("click", "#btnCambiarContrasena", function (e) {
    objeto.primerCambioContrasena();
});
arreglo=[];
$(document).ready(function () {
    objeto.cierreSesionInactividad();
});

 let selectProductos = $("#selectProductos");

$('#btnBuscar').on('click', function () {

    if (!$("#selectColaborador").val() && !$("#Fecha_Fin").val() && !$('#ckeckboxFecha').is(':checked')) {
        objeto.verAlerta(
            "Por favor, selecciona un colaborador y una fecha de fin primero",
            `.`,
            "warning"
        );
    selectProductos.val("");
        return;
    }
    else if (!$("#selectColaborador").val()) {
        objeto.verAlerta(
            "Por favor, selecciona un colaborador",
            `.`,
            "warning"
        );
    selectProductos.val("");
        return;
    }else if(!$("#Fecha_Fin").val() && !$('#ckeckboxFecha').is(':checked') ){
        objeto.verAlerta(
            "Por favor, selecciona una fecha",
            `.`,
            "warning"
        );
    selectProductos.val("");

        return;
    }else{
    var query = $('#search-input').val();
    //Aquí reutilizamos la ruta del método de buscarNoSerit de las asignaciones de unidadesAdministrativas para ahorrarnos código en el controller de asignacionesColaboradores
    objeto.setUrlPeticion = "/AsignacionActivosUnidadesAdministrativas/buscarNoSerie"; //al objeto le envimos la url donde se realizara el proceso
    objeto.setDatosPeticion = { q: query }; //Le enviamos el objeto con todos los datos
    objeto.verDetallesRegistro(function (e) {

        let No_Serie = e.datos[0].No_Serie;
        let Nombre_Producto = e.datos[0].Nombre_Producto;

        let nuevoElemento = {
            Fecha_Fin: $("#Fecha_Fin").val(),
            colaborador: $("#selectColaborador").val(),
            Id_Producto: e.datos[0].Id_Producto,
            Nombre_Producto: Nombre_Producto,
            No_Serie: No_Serie,
            Id_activo:  e.datos[0].Id_Activo
        };
        console.log(nuevoElemento);
        arrayLicencias.push(nuevoElemento);
        console.log(arrayLicencias);
        arreglo.push(nuevoElemento);
        var table = document.getElementById("table-detalleAsignacion").getElementsByTagName("tbody")[0];
        // Limpiar la tabla eliminando todas las filas existentes
        // table.innerHTML = '';
        // Recorrer el arreglo y crear una fila por cada elemento
        //for (var i = 0; i < arreglo.length; i++) {
        // Crear un nuevo elemento <tr>
        var row = table.insertRow();
        // Insertar celdas en la fila
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        // Asignar valores a las celdas
        //cell1.innerHTML = $("#selectUnidades").children('option:selected').text();
        cell1.innerHTML = $("#selectColaborador").children('option:selected').text();
        
        cell2.innerHTML = e.datos[0].Nombre_Producto;

        if ($('#ckeckboxFecha').is(':checked')) {
            cell3.innerHTML = '<span class="indefinido"> Indefinida</span>';
        }else{
            cell3.innerHTML = $("#Fecha_Fin").val();
        }

        cell4.innerHTML = e.datos[0].No_Serie;
        cell5.innerHTML = '<center><a href="#" Id_Array="' + nuevoElemento.Id_Producto + '" id="quitarArray" title="Eliminar ejemplo" class="btn btn-xs btn-default text-dark m-1 p-1 shadow"><i class="fa fa-lg fa-fw fa-trash"></i></a></center>';
        //}
        $('#search-input').val("");

        console.log(arreglo);
    });
    }
});


$(document).on('change', '#selectProductos', function () {
        if (!$("#selectColaborador").val() && !$("#Fecha_Fin").val() && !$('#ckeckboxFecha').is(':checked')) {
            objeto.verAlerta(
                "Por favor, selecciona un colaborador y una fecha de fin primero",
                `.`,
                "warning"
            );
        selectProductos.val("");
            return;
        }
        else if (!$("#selectColaborador").val()) {
            objeto.verAlerta(
                "Por favor, selecciona un colaborador",
                `.`,
                "warning"
            );
        selectProductos.val("");
            return;
        }else if(!$("#Fecha_Fin").val() && !$('#ckeckboxFecha').is(':checked') ){
            objeto.verAlerta(
                "Por favor, selecciona una fecha",
                `.`,
                "warning"
            );
        selectProductos.val("");

            return;
        }  else {
            var selectedProductId = $('#selectProductos').val();  // Corrección aquí
            var selectedProductName = $('#selectProductos').find('option:selected').text();  // Corrección aquí
            
            let activos = { Id_Producto: selectedProductId };
            objeto.setUrlPeticion = "/AsignacionesColaboradores/licenciasDisponibles";
            objeto.setDatosPeticion = activos;
            objeto.verDetallesRegistro(function (e) {
                let nuevoElemento = {
                    Fecha_Fin: $("#Fecha_Fin").val(),
                    colaborador: $("#selectColaborador").val(),
                    Id_Producto: selectedProductId,
                    Nombre_Producto: selectedProductName,
                    Id_activo : "0" //Aquí por defecto le agregamos el valor 0 para pasarselo como parámetro al SP y que sepa exactamente cual activo agregarle o si es 0 agregarle uno aleatorio
                };
                arrayLicencias.push(nuevoElemento);
                console.log(arrayLicencias);
                console.log('activos'+e.activosDetalle.No_Serie);
                
                // Agregar fila a la tabla
                var table = document.getElementById("table-detalleAsignacion").getElementsByTagName("tbody")[0];
        
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
        
                cell1.innerHTML = $("#selectColaborador").children('option:selected').text();
                cell2.innerHTML = selectedProductName;

                if ($('#ckeckboxFecha').is(':checked')) {
                    cell3.innerHTML = '<span class="indefinido"> Indefinida</span>';
                }else{
                    cell3.innerHTML = $("#Fecha_Fin").val();
                }

                cell5.innerHTML = '<a href="#" Id_Array="' + selectedProductId + '" id="quitarArray" title="Eliminar ejemplo" style="margin-left:15px;" class="btn btn-xs btn-default text-dark m-1 p-1 shadow"><i class="fa fa-lg fa-fw fa-trash"></i></a>';
                //cell3.innerHTML = $("#Fecha_Fin").val();
                //cell4.innerHTML = '<select class="form-control SelectActivo"  id="SelectActivo" id_select="' + selectedProductId + '"><option value="0" >Aleatorio</option></select>';

                var select = document.createElement("select");
                select.className = "form-control SelectActivo";
                select.id = "SelectActivo";
                var posicion = arrayLicencias.length - 1;
                console.log('Elemento agregado en la posición: ' + posicion);
                select.setAttribute("posicion", posicion);
            
                var optionAleatorio = document.createElement("option");
                optionAleatorio.value = "0";
                optionAleatorio.textContent = "Automático";
                select.appendChild(optionAleatorio);

                select.setAttribute("id_select", selectedProductId)
                e.activosDetalle.forEach(function (detalleActivo) {
                   var optionActivo = document.createElement("option");
                   optionActivo.value = detalleActivo.Id_Activo;
                   optionActivo.textContent = detalleActivo.No_Serie;
                   select.appendChild(optionActivo);
//                    detalleActivo.SelectActivo.append('<option></option>');
                });
                cell4.appendChild(select); 
                
             //  cell4.appendChild(select); 

            });
           
            }     
        selectProductos.val("");

    });


$(document).on('click', '#btnAgregarArray', function () {
    // ... tu código anterior ...
    if (!$("#selectColaborador").val() && !$("#Fecha_Fin").val() && !$('#ckeckboxFecha').is(':checked')) {
        objeto.verAlerta(
            "Por favor, selecciona un colaborador y una fecha de fin primero",
            `.`,
            "warning"
        );
    selectProductos.val("");
        return;
    }
    else if (!$("#selectColaborador").val()) {
        objeto.verAlerta(
            "Por favor, selecciona un colaborador",
            `.`,
            "warning"
        );
    selectProductos.val("");
        return;
    }else if(!$("#Fecha_Fin").val() && !$('#ckeckboxFecha').is(':checked') ){
        objeto.verAlerta(
            "Por favor, selecciona una fecha",
            `.`,
            "warning"
        );
    selectProductos.val("");

        return;
    }  else {
        var selectedOption = $(this).attr('data-id');

        let datos = {
            Id_Grupo: selectedOption,
        };
        objeto.setUrlPeticion = "/AsignacionesColaboradores/gruposLicencias";//al objeto le envimos la url donde se realizara el proceso
        objeto.setDatosPeticion = datos;
        objeto.verDetallesRegistro(function (e) {
        // ... tu código anterior ...
        let arrayDetalle = [];
        var table = document.getElementById("table-detalleAsignacion").getElementsByTagName("tbody")[0];
        var arrayNoDisponibles = [];
          //  console.log('e');
           // console.log(e);
        e.datos.forEach(function (detalle, i) {
            // ... tu código anterior ...
            
            let activos = { Id_Producto: detalle.Id_Producto };
            objeto.setUrlPeticion = "/AsignacionesColaboradores/licenciasDisponibles";
            objeto.setDatosPeticion = activos;
            objeto.verDetallesRegistro(function (e) {
               console.log('e datos' );
               console.log(e.datos[0]);
               console.log('e datos' );
               console.log(detalle.Nombre_Producto);activosDetalle
               console.log('e activosDetalle' );
               console.log(e.activosDetalle[0]);
                if (e.datos[0].Licencias == 0) {
l
                    arrayNoDisponibles.push(detale.Nombre_Producto);
               //     console.log('No hay activos del siguiente producto: '+ arrayNoDisponibles.toString);
                } else  {

                    arrayDetalle['Fecha_Fin'] = $("#Fecha_Fin").val();
                    arrayDetalle['colaborador'] = $("#optionColaborador").val();
    
                    detalle.Fecha_Fin = $("#Fecha_Fin").val();
                    detalle.colaborador = $("#optionColaborador").val();
                    detalle.Id_activo = "0"; //Aquí por defecto le agregamos el valor 0 para pasarselo como parámetro al SP y que sepa exactamente cual activo agregarle o si es 0 agregarle uno aleatorio
                    
                  //  console.log(detalle);
                 //   arrayLicencias.push(detalle);
                    
                 //   console.log(arrayLicencias);
                    // Crear un nuevo elemento <tr>
                    var row = table.insertRow();
                    // Insertar celdas en la fila
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    // Asignar valores a las celdas
                    cell1.innerHTML = $("#selectColaborador").children('option:selected').text(); // Utiliza 'detalle' directamente en lugar de 'arrayLicencias[i]'
                    cell2.innerHTML = detalle.Nombre_Producto;
                   
                    if ($('#ckeckboxFecha').is(':checked')) {
                        cell3.innerHTML = '<span class="indefinido"> Indefinida</span>';
                    }else{
                        cell3.innerHTML = $("#Fecha_Fin").val();
                    }
                   
                    cell5.innerHTML = '<a href="#" Id_Array="' + detalle.Id_Producto + '" id="quitarArray" title="Eliminar ejemplo" style="margin-left:15px;" class="btn btn-xs btn-default text-dark m-1 p-1 shadow"><i class="fa fa-lg fa-fw fa-trash"></i></a>';                                                      
                    var select = document.createElement("select");
                    select.className = "form-control SelectActivo";
                    select.id = "SelectActivo";

                    select.setAttribute("id_select", detalle.Id_Producto);
                    select.setAttribute("posicion", i);
                    // Agregar opción "Aleatorio"
                    var optionAleatorio = document.createElement("option");
                    optionAleatorio.value = "0";
                    optionAleatorio.textContent = "Aleatorio";
                    select.appendChild(optionAleatorio);

                    // Agregar opciones de activos disponibles
                    e.activosDetalle.forEach(function (detalleActivo) {
                        var optionActivo = document.createElement("option");
                        optionActivo.value = detalleActivo.Id_Activo;
                        optionActivo.textContent =  detalleActivo.No_Serie;
                        select.appendChild(optionActivo);

                    });

                    cell4.appendChild(select);

                }
            });
        });
        console.log('No hay activos del siguiente producto: ' + arrayNoDisponibles);

        console.log("arrayNoDisponibles: ");
        console.log(arrayNoDisponibles);

        console.log("length arrayNoDisponibles: ");
        console.log(arrayNoDisponibles.length);
        if(arrayNoDisponibles.length > -1){
           let arreglo2 = arrayNoDisponibles.toString();
           console.log(arreglo2);
            
            let arreglo = arrayNoDisponibles.toString();
                var mensaje = "No tienes activos disponibles de algunos productos. \n Has una solicitud de cotización o contacta a tu proveedor." + arreglo;
                objeto.verAlerta(
                    "Productos no disponibles",
                    mensaje,
                    "warning"
                );
                console.log(arreglo);
        }

    });
    }
});

$(document).on("change", ".SelectActivo", function () {
    let select = $(this);
    console.log('select');
    console.log(select);
    
    let posicion = $(this).attr('posicion');

    var optionSelected = $(this);
    var selectedValue  = optionSelected.val();

    arrayLicencias[posicion].Id_activo = selectedValue;
    
    console.log("Array licencias: ");
    console.log(arrayLicencias[posicion]);
    console.log(selectedValue);
    console.log(arrayLicencias);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////


$(document).on("change", ".SelectActivo", function () {
    // Obtener el valor del option seleccionado
    var selectedOptionValue = $(this).val();
    
    // Establecer el valor del select
    $(this).val(selectedOptionValue);
    
    // Mostrar el valor en la consola (esto es opcional)
    console.log('El valor del select: ' + selectedOptionValue);
});

$(document).on("click", "#quitarArray", function () {
    // Obtener el Id_grupo del botón de eliminación
    var Id_Producto = $(this).attr("Id_Array");
    // Buscar el índice del objeto con el Id_Producto correspondiente en el arreglo
    var index = arrayLicencias.findIndex(function (element) {
        return element.Id_Producto === Id_Producto;
    });
    // Eliminar el objeto del arreglo
    arrayLicencias.splice(index, 1);
    // Actualizar la tabla eliminando la fila correspondiente
    $(this).closest("tr").remove();
    console.log(arrayLicencias);
});
//let contador = 0; //Este contador nos servirá para saber si el dropdawn está desplegado o no
$(document).on("click", ".selectGrupo", function () {
    var selectGrupo = $(this);
    var Id_Grupo = selectGrupo.attr("idSelect");

    let datos = {
        Id_Grupo: Id_Grupo,
    };
    var opciones = $(`#select_${Id_Grupo}`);

    if ($.trim(opciones.html()) == '') {
        objeto.setUrlPeticion = "/AsignacionesColaboradores/consultarLicencias";
        objeto.setDatosPeticion = datos;
        objeto.verDetallesRegistro(function (e) {

            selectGrupo.find(".select-btn").toggleClass("rotated2");
            // Agregamos las nuevas opciones al select utilizando forEach
            e.datos.forEach(function (item) {
                let activos = { Id_Producto: item.Id_Producto };
                objeto.setUrlPeticion = "/AsignacionesColaboradores/licenciasDisponiblesGrupos";
                objeto.setDatosPeticion = activos;
                objeto.verDetallesRegistro(function (e) {

                    opciones.append(`
                            <li id="${item.Id_Producto}" idSelect="${item.Id_Grupo}" class="list-group-item d-flex justify-content-between align-items-center option">
                                ${item.Nombre_Producto}
                                
                                <span class="disponibles"> Disponibles:</span>
                                <span class="badge badge-primary badge-pill">${e.datos[0].Licencias}</span>
                            </li>
                        `);
                });
            });
            contador = 1;
        });
    }
    else{
        opciones.empty();
        selectGrupo.find(".select-btn").toggleClass("rotated2");
    }
});

$(document).on("click", "#btnLimpiarArray", function () {
    arrayLicencias = [];
    var table = document.getElementById("table-detalleAsignacion").getElementsByTagName("tbody")[0];
    table.innerHTML = ''; // Limpiar la tabla eliminando todas las filas existentes para que se
});

$(document).on("click", "#btnAsignar", () => {       //Cuando se le da click al boton de agregar
    $("#formularioActivosColaboradores").validate().destroy();    //Destruimos la validacion del formulario, ya que si no hacemos esto la instancia de validacion se queda guardada en la cache y es como si se repitiera este metodo   
    $("#formularioActivosColaboradores").validate({               //Comenzamos la validacion del formulario       
        ignore: [],
        errorClass: "border-danger text-danger",    //Estas clases se colocaran en caso de error
        errorElement: "x-adminlte-input",           //A este elemento se le colocaran las clases de error
        errorPlacement: function (error, e) {
            jQuery(e).parents(".form-group").append(error);
        },
        //     //Reglas que tendrá cada campo en el formulario
        rules: {
            selectColaborador: {
                required: true,
                minlength: 1
            },
            Fecha_Fin: {
                required: true,
                minlength: 1
            }
        },
        //     //Si todas las reglas se cumplen se comienza con el envio del formulario
        submitHandler: (form) => {
            //         alert('submitHandler');        
            let formData = new FormData(form);
            formData.append('arreglo', JSON.stringify(arrayLicencias));

            objeto.setUrlPeticion = "/AsignacionesColaboradores/agregarAsignacion";      //al objeto le envimos la url donde se realizara el proceso
            objeto.datosPeticion = formData;                  //Le enviamos el objeto con todos los datos
            objeto.insertarRegistroNoTable();
            console.log('limpio');
            arrayLicencias = [];
            $('#table-detalleAsignacion tbody').html('');                        //llamamos al metodo del objeto para insertar el registro
        //    this.resetearFormulario();
        },
    });
    // var table = document.getElementById("").getElementsByTagName("tbody")[0];
    // table.innerHTML = ''; // Limpiar la tabla eliminando todas las filas existentes para que se 
});
function resetearFormulario() {
    $(this.getFormulario)[0].reset();
    $(this.getFormulario).validate().resetForm();
}
$(document).on("change", "#ckeckboxFecha", function () {
    if ($('#ckeckboxFecha').is(':checked')) {
        console.log("Esta checkeado");
        $('#Fecha_Fin').val("");
        $('#Fecha_Fin').prop("disabled", true);

    }else{
        console.log("no esta checkeado");
 //       dataTable.$(".eliminarMasivo_checkbox").prop("checked", false);
        $('#Fecha_Fin').prop("disabled",false);


    }
});
