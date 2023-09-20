@extends('adminlte::page')

@section('title', 'Dashboard')
@section('content_header')
    {{-- <h1>Asignación de Activos a Colaboradores</h1> --}}
    @include('Layouts.header', ['nombreModulo' => "ASIGNACIÓN COLABORADORES"])

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css">
@stop

@section('content')
 @include('Layouts.primerCambioContrasena')  
 {{--  @include('Layouts.header', ['nombreModulo' => "Asignación Activos Colaboradores"])  --}}

@php
$permisoPagina = false; // Valor predeterminado en caso de que no se cumpla ninguna condición
@endphp
@foreach (session('permisos') as $moduloID => $permisos)
    @if ($moduloID == 21)
        {{-- Debes colocar el id del modulo --}}
        @php
            $permisoPagina = true; //Variable para saber si tiene permiso al modulo
        @endphp
        
           
        <div class="card-header bg-dark d-flex justify-content-between align-items-center">
            <h3 class="text-light">Asignaciones de Activos a Colaboradores</h3>
        </div>
        
        <form id="formularioActivosColaboradores">
            @csrf              
            <div id="padre" style="height:800px;" class="card shadow">
                <div class="col-md-12" style="width:100%; max-height:650px;" > 
                
                <div class="contenedor">            
                    <div class="col-md-6 colaboradores">
                      <div class="col-md-12">
                        <label class="control-label" id="labelP" for="selectColaborador">*Colaboradores</label>               
                        <select id="selectColaborador"  name="selectColaborador" class="form-control" >
                            <option value="" selected disabled>Selecciona un colaborador</option>
                            @foreach ($colaboradores as $colaborador)
                                <option class="form-control" id_unidad="{{$colaborador->Id_Unidad_Admin}}" id="optionColaborador" value="{{ $colaborador->Id_Colaborador}}">{{$colaborador->Nombres}}{{$colaborador->Apellido_Paterno}}{{$colaborador->Apellido_Materno }} <br><b>email:</b> {{$colaborador->Email}}</option>
                            @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="col-md-12 d-block ">                                                   
                            <label  id="labelFL" for="Fecha_Fin">*Fin de la Asignación</label>
                            <input type="date"  class="form-control col-md-12 mb-2" name="Fecha_Fin" id="Fecha_Fin" placeholder="dd/mm/yyyy" required min="<?php $hoy=date("Y-m-d"); echo $hoy;?>" />
                        </div>
                        <div class="d-flex checkbox">
                            <input type="checkbox" id="ckeckboxFecha">
                            <a for="">Sin fecha límite (indefinida)</a>
                        </div>                
                    </div>
                   
                </div>
               
                <div id="body" class="col-md-12" style="display: flex;">
                   
                    
                     <div  id="gruposLicencias" style="width:40%;">
                        <div style="max-height: 370px; overflow-y: auto;">
                             <table id="table-gruposLicencias" class="table-hover display table-striped table-hover responsive no-wrap" width="100%">
                                <thead class="bg-dark">
                                    <tr> <td width="5%" class="d-none">Id_Grupo</td>
                                        <td width="85%">  Nombre Grupo</td> 
                                        <td width="15%">Acciones</td>
                                    </tr>
                                </thead>
                               
                            </table>
                            @foreach ($gruposLicencias as $grupoLicencia)

                            <div  style="display: flex; width:100%; height:auto; padding:0px;" class="col-md-12">
                                <div style="display: flex; width:100%; height:auto;">
                                    <div style="display: block;width:100%; ">
                                        <div idSelect="{{ $grupoLicencia->Id_Grupo }}"  class="selectGrupo">
                                            <ul style="width: 100%;" class="list-group-item d-flex justify-content-between ">
                                                {{ $grupoLicencia->Nombre_Grupo }}
                                                                            
                                            <i class="bx bx-chevron-down select-btn"> </i>                                    
                                            </ul>
                                        </div>
                                                
                                        <div  id="select_{{$grupoLicencia->Id_Grupo}}">

                                        </div>
                                    </div>                                   
                                </div>
                                <div style="margin-top:10px; margin-left:10px;  justify-content: center;">
                                <button type="button"  class="btn btn-sm btn-success btnAgregarArray" id="btnAgregarArray" data-id="{{ $grupoLicencia->Id_Grupo }}">
                                    <i class="fas fa-plus"></i> <!-- Icono de signo más -->
                                </button>
                                </div>
                            </div>
                       
                            @endforeach  
                        </div>  
                        <a href="/gruposlicencias">Agregar un nuevo grupo</a>

                    </div>  
                 

                    <div style="width:100%;" id="detalleAsignacion">

                        <div class="col-md-12 mb-2 d-flex">
                            <div class="col-md-6 form-group">
                                <label for="">*Producto</label>                                  
                                <select  name="selectProductos" id="selectProductos" class="form-control" style="padding:.375rem .75rem; width:100%; min-height: 38px;" >
                                    <option value="" selected disabled>Selecciona un producto</option>
                                    @foreach ($productos as $p)
                                    @if ($p->Cantidad!=0)
                                        <option data-licencias={{$p->Cantidad}} style="color: black;" data-productoid={{$p->Id_Producto}} id="optionProductos" value="{{$p->Id_Producto}}">{{$p->Nombre_Producto}} - [Disponibles: {{$p->Cantidad}} ]</option>
                                    {{--  @else
                                        <option style="background-color:rgb(219, 219, 219);" data-productoid={{$p->Id_Producto}} id="optionProductos" value="{{$p->Id_Producto}}" disabled >{{$p->Nombre_Producto}} -  [Disponibles: {{$p->Cantidad}}]</option>  --}}
                                    @endif
                                    @endforeach
                                </select>

                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label for="">Buscar por No.Serie del activo</label>
                                <div class=" form-group d-flex">
                                    <input class="form-control" type="text" id="search-input" name="q" placeholder="Buscar...">
                                        <div>
                                            <button class="btn btn-primary" style="margin:0px 0px 5px 5px; max-height:90%; max-width:90%; font-size: 15;" id="btnBuscar" type="button"><i class="fa fa-lg fa-fw fa-search"></i></button>
                                        </div>                                
                                </div>
                            </div>         
                        </div>      

                        <div class="col-md-12" style="max-height: 325px; overflow-y: auto;">

                            <table id="table-detalleAsignacion" class="table-hover display table-striped table-hover responsive no-wrap" width="100%;">
                                <thead class="bg-dark">
                                    <tr>
                                        <td width="1%" class="d-none">Id_Activo</td>
                                        <td width="30%">Colaborador</td>
                                        {{--  <td width="20%">Grupo</td>--}}
                                        <td width="27%">Producto</td>
                                        <td width="15%">Fecha Fin</td>
                                        <td width="23%">Activo (No.Serie)</td>
                                        <td width="5%"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                   
                </div>
               
             </div>
             <div id="bottons" style="display: flex">
                <x-adminlte-button theme="danger" class="ml-auto" label="Borrar" id="btnLimpiarArray"/>
                    {{--  <x-adminlte-button class="ml-auto" id="btnEditar" label="Editar" theme="primary" form="formulario-Activos" />  --}}
                <x-adminlte-button class="ml-auto" id="btnAsignar" label="Asignar" type="submit" theme="success">Asignar</x-adminlte-button>
                
            </div> 
            </div>   
        </form>
    @endif
@endforeach

@if ($permisoPagina == false)
    {{-- Función para redirigir al usuario si no tiene este módulo --}}
    <script>
        window.location.href = "{{ route('error.index') }}";
    </script>
@endif
@stop

@section('footer')
    @include('Layouts.footer')
@stop
