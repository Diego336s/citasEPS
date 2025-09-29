<?php

use App\Http\Controllers\AdministradoresController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\EspecialidadesMedicosController;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\RecepcionistasController;

use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


//Rutas publicas 
Route::post("crearPaciente", [PacientesController::class, "store"]);
Route::post("loginRecepcionista", [RecepcionistasController::class, "login"]);
Route::post("loginAdmin", [AdministradoresController::class, "login"]);
Route::post("loginMedico", [MedicosController::class, "login"]);
Route::post("loginPaciente", [PacientesController::class, "login"]);

Route::middleware(['auth:sanctum'])->group(function () {
    //Acesso recepcionista
    Route::middleware('ability:Recepcionista')->group(function () {
        Route::post('logoutRecepcionista', [RecepcionistasController::class, 'logout']);
        Route::get('me/Recepcionista', [RecepcionistasController::class, 'me']);
        Route::post('cambiar/clave/recepcionista/{id}', [RecepcionistasController::class, 'cambiarClave']);
        Route::post('cambiar/correo/recepcionista/{id}', [RecepcionistasController::class, 'cambiarCorreo']);

        //Citas pendientes
        Route::get("citasPendientes", [CitasController::class, "citasPendientes"]);

        Route::get("total/citas/mes", [CitasController::class, "totalCitasPorMes"]);
        Route::get("total/citas/pendientes", [CitasController::class, "totalCitasPendiente"]);
        Route::get("citas/conMedicos/especialidades", [CitasController::class, "listarCitasConDoctorEspecialidad"]);
    });

    //Acceso medico
    Route::middleware('ability:Medico')->group(function () {
        Route::get('me/Doctor', [MedicosController::class, 'me']);
        Route::post('logoutMedico', [MedicosController::class, 'logout']);
        Route::post('cambiar/clave/medico/{id}', [MedicosController::class, 'cambiarClave']);
        Route::post('cambiar/correo/medico/{id}', [MedicosController::class, 'cambiarCorreo']);
        Route::get("citasPorPacientes/{documento}", [CitasController::class, "citasPorPaciente"]);

        Route::put("actualizarMedico/{id}", [MedicosController::class, "update"]);
        Route::get('citas/confirmadas/doctor/{id}', [CitasController::class, 'citasPorDoctorConfirmadas']);
    });


    //Acceso paciente
    Route::middleware('ability:Paciente')->group(function () {


        Route::put("actualizarPaciente/{id}", [PacientesController::class, "update"]);
        Route::get('me/Paciente', [PacientesController::class, 'me']);
        Route::get("filtrarMedicosPorEspecialidad/{id_especialidad}", [EspecialidadesMedicosController::class, "filtrar_medicos_por_especialidad"]);
        Route::post('cambiar/clave/paciente/{id}', [PacientesController::class, 'cambiarClave']);
        Route::post('cambiar/correo/paciente/{id}', [PacientesController::class, 'cambiarCorreo']);

        Route::post("crearCitas", [CitasController::class, "store"]);
        Route::get("citasEsteMesPorPaciente/{id}", [CitasController::class, "citasEsteMesPorPaciente"]);
        Route::get("totalCitasPorPaciente/{id}", [CitasController::class, "totalCitasPorPaciente"]);
        Route::post('logoutPaciente', [PacientesController::class, 'logout']);


        //Filtrar citas por documento del paciente
        Route::get("citasPorPacientes/{documento}", [CitasController::class, "citasPorPaciente"]);
    });



    //Acceso admin
    Route::middleware('ability:Admin')->group(function () {
        Route::get('me/Admin', [AdministradoresController::class, 'me']);
        Route::put("actualizar/Admin/{id}", [AdministradoresController::class, "update"]);
        Route::post('logoutAdmin', [AdministradoresController::class, 'logout']);
        Route::post('cambiar/clave/Admin/{id}', [AdministradoresController::class, 'cambiarClave']);
        Route::post('cambiar/correo/Admin/{id}', [AdministradoresController::class, 'cambiarCorreo']);



        Route::get('listarMedicos', [MedicosController::class, 'index']);
        Route::post("crearMedico/{idEspecialidad}", [MedicosController::class, "store"]);
        Route::delete("eliminarMedico/{id}", [MedicosController::class, "destroy"]);
        Route::put("actualizarMedicoConEspecialida/{id}/{idEspecialidad}", [MedicosController::class, "actualizarMedicoConEspecialida"]);

        Route::get("listarPaciente", [PacientesController::class, "index"]);

        Route::get("listarRecepcionistas", [RecepcionistasController::class, "index"]);
        Route::get("filtrarRecepcionista/{id}", [RecepcionistasController::class, "show"]);
        Route::post("crearRecepcionista", [RecepcionistasController::class, "store"]);
        Route::delete("eliminarRecepcionistas/{id}", [RecepcionistasController::class, "destroy"]);

        Route::get("filtrarEspecialidad/{id}", [EspecialidadesController::class, "show"]);
        Route::post("crearEspecialidades", [EspecialidadesController::class, "store"]);
        Route::put("actualizarEspecialidades/{id}", [EspecialidadesController::class, "update"]);
        Route::delete("eliminarEspecialidades/{id}", [EspecialidadesController::class, "destroy"]);


        //Total de medicos
        Route::get("totalMedicos", [MedicosController::class, "contarMedicos"]);

        //Total de especialidades
        Route::get("totalEspecialidades", [EspecialidadesController::class, "contarEspecialidades"]);
    });

    //Acceso medico y admin
    Route::middleware('ability:Medico,Admin')->group(function () {
        Route::put("actualizarMedico/{id}", [MedicosController::class, "update"]);
        Route::get('filtrarMedicoConEspecialidades/{id}', [MedicosController::class, 'filtrarMedicoConEspecialidades']);
    });

    //Acceso paciente y admin
    Route::middleware('ability:Paciente,Admin')->group(function () {
        Route::get("listarEspecialidades", [EspecialidadesController::class, "index"]);

        Route::get('datosCita/{id}', [CitasController::class, 'show']);
        Route::put("actualizarCitas/{id}", [CitasController::class, "update"]);
    });

    //Acceso paciente, recepcionista y medico
    Route::middleware('ability:Paciente,Recepcionista,Medico')->group(function () {
        Route::post('cambiarEstadoCita/{id}', [CitasController::class, 'cambiarEstadoCita']);
    });

    //Acceso paciente, recepcionista y admin
    Route::middleware('ability:Paciente,Recepcionista,Admin')->group(function () {
        Route::get('listarMedicosConEspecialidades', [MedicosController::class, 'listarMedicosConEspecialidades']);
    });
});









Route::delete("eliminarPaciente/{id}", [PacientesController::class, "destroy"]);




Route::put("actualizarRecepcionistas/{id}", [RecepcionistasController::class, "update"]);




Route::get("listarEspecialidadesMedicos", [EspecialidadesMedicosController::class, "index"]);
Route::post("crearEspecialidadesMedicos", [EspecialidadesMedicosController::class, "store"]);
Route::put("actualizarEspecialidadesMedicos/{id}", [EspecialidadesMedicosController::class, "update"]);
Route::delete("eliminarEspecialidadesMedicos/{id}", [EspecialidadesMedicosController::class, "destroy"]);



Route::get("listarCitas", [CitasController::class, "index"]);

Route::put("actualizarCitas/{id}", [CitasController::class, "update"]);
Route::delete("eliminarCitas/{id}", [CitasController::class, "destroy"]);

//Filtrar medico por numero documento
Route::get("medicoPorDocumento/{documento}", [MedicosController::class, "medicoPorDucumento"]);

//filtrar pacientes por sexo
Route::get("pacientesPorSexo/{sexo}", [PacientesController::class, "filtrarPacientesPorSexo"]);

//Citas confirmadas
Route::get("citasConfirmadas", [CitasController::class, "citasConfirmadas"]);


//Filtrar citas que esten confirmadas por documento del paciente
Route::get("citasPorPacientesConfirmadas/{documento}", [CitasController::class, "citasPorPacienteConfirmadas"]);

//Citas del dia
Route::get("citasDelDia", [CitasController::class, "citasDeHoy"]);

//Total citas
Route::get("totalCitas", [CitasController::class, "contarCitas"]);



//Filtrar pacientes por nacionalidad
Route::get("pacientePorNacionalidad/{nacionalidad}", [PacientesController::class, "pacientePorNacionalidad"]);

//Filtar por rh
Route::get("pacientePorRh/{rh}", [PacientesController::class, "pacientePorRh"]);
