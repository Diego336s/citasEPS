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



Route::middleware(['auth:sanctum'])->group(function () {
    //Acceso medico
    Route::middleware('ability:Medico')->group(function () {
        Route::post('logoutMedico', [MedicosController::class, 'logout']);
    });

    //Acceso medico y admin
    Route::middleware('ability:Medico, Admin')->group(function () {

        Route::get('listarMedicos', [MedicosController::class, 'index']);
        Route::post("crearMedico", [MedicosController::class, "store"]);
        Route::delete("eliminarMedico/{id}", [MedicosController::class, "destroy"]);

        Route::get("listarPaciente", [PacientesController::class, "index"]);

        Route::get("listarRecepcionistas", [RecepcionistasController::class, "index"]);
        Route::post("crearRecepcionista", [RecepcionistasController::class, "store"]);
        Route::delete("eliminarRecepcionistas/{id}", [RecepcionistasController::class, "destroy"]);
    });

    //Acceso paciente y admin
    Route::middleware('ability:Paciente, Admin')->group(function () {
        Route::get("listarEspecialidades", [EspecialidadesController::class, "index"]);
        Route::get('listarMedicosConEspecialidades', [MedicosController::class, 'listarMedicosConEspecialidades']);
    });

    //Acceso paciente
    Route::middleware('ability:Paciente')->group(function () {
        Route::get('me', [PacientesController::class, 'me']);
        Route::get("filtrarMedicosPorEspecialidad/{id_especialidad}", [EspecialidadesMedicosController::class, "filtrar_medicos_por_especialidad"]);
        Route::post('cambiarClave/{id}', [PacientesController::class, 'cambiarClave']);
        Route::post("crearCitas", [CitasController::class, "store"]);
         Route::get("citasEsteMesPorPaciente/{id}", [CitasController::class, "citasEsteMesPorPaciente"]);
    });

    //Acceso admin
    Route::middleware('ability:Admin')->group(function () {
    Route::get('listarMedicos', [MedicosController::class, 'index']);
    });
});


Route::post("loginAdmin", [AdministradoresController::class, "login"]);

Route::post("loginMedico", [MedicosController::class, "login"]);

Route::put("actualizarMedico/{id}", [MedicosController::class, "update"]);


Route::post("loginPaciente", [PacientesController::class, "login"]);
Route::post('logoutPaciente', [PacientesController::class, 'logout']);

Route::post("crearPaciente", [PacientesController::class, "store"]);
Route::put("actualizarPaciente/{id}", [PacientesController::class, "update"]);
Route::delete("eliminarPaciente/{id}", [PacientesController::class, "destroy"]);


Route::post("loginRecepcionista", [RecepcionistasController::class, "login"]);
Route::post('logoutRecepcionista', [RecepcionistasController::class, 'logout']);

Route::put("actualizarRecepcionistas/{id}", [RecepcionistasController::class, "update"]);


Route::post("crearEspecialidades", [EspecialidadesController::class, "store"]);
Route::put("actualizarEspecialidades/{id}", [EspecialidadesController::class, "update"]);
Route::delete("eliminarEspecialidades/{id}", [EspecialidadesController::class, "destroy"]);


Route::get("listarEspecialidadesMedicos", [EspecialidadesMedicosController::class, "index"]);
Route::post("crearEspecialidadesMedicos", [EspecialidadesMedicosController::class, "store"]);
Route::put("actualizarEspecialidadesMedicos/{id}", [EspecialidadesMedicosController::class, "update"]);
Route::delete("eliminarEspecialidadesMedicos/{id}", [EspecialidadesMedicosController::class, "destroy"]);



Route::get("listarCitas", [CitasController::class, "index"]);

Route::put("actualizarCitas/{id}", [CitasController::class, "update"]);
Route::delete("eliminarCitas/{id}", [CitasController::class, "destroy"]);

//Total de medicos
Route::get("eliminarCitas", [MedicosController::class, "contarMedicos"]);

//Total de especialidades
Route::get("totalEspecialidades", [EspecialidadesController::class, "contarEspecialidades"]);

//Filtrar medico por numero documento
Route::get("medicoPorDocumento/{documento}", [MedicosController::class, "medicoPorDucumento"]);

//filtrar pacientes por sexo
Route::get("pacientesPorSexo/{sexo}", [PacientesController::class, "filtrarPacientesPorSexo"]);

//Citas confirmadas
Route::get("citasConfirmadas", [CitasController::class, "citasConfirmadas"]);

//Filtrar citas por documento del paciente
Route::get("citasPorPacientes/{documento}", [CitasController::class, "citasPorPaciente"]);

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
