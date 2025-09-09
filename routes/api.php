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
    Route::middleware('ability:Medico')->group(function () {
        Route::post('logoutMedico', [MedicosController::class, 'logout']);
    });
    Route::middleware('ability:Medico, Admin')->group(function () {});
});

Route::post("loginAdmin", [AdministradoresController::class, "login"]);

Route::post("loginMedico", [MedicosController::class, "login"]);
Route::get('listarMedicos', [MedicosController::class, 'index']);
Route::post("crearMedico", [MedicosController::class, "store"]);
Route::put("actualizarMedico/{id}", [MedicosController::class, "update"]);
Route::delete("eliminarMedico/{id}", [MedicosController::class, "destroy"]);

Route::post("loginPaciente", [PacientesController::class, "login"]);
Route::post('logoutPaciente', [PacientesController::class, 'logout']);
Route::get("listarPaciente", [PacientesController::class, "index"]);
Route::post("crearPaciente", [PacientesController::class, "store"]);
Route::put("actualizarPaciente/{id}", [PacientesController::class, "update"]);
Route::delete("eliminarPaciente/{id}", [PacientesController::class, "destroy"]);


Route::post("loginRecepcionista", [RecepcionistasController::class, "login"]);
Route::post('logoutRecepcionista', [RecepcionistasController::class, 'logout']);
Route::get("listarRecepcionistas", [RecepcionistasController::class, "index"]);
Route::post("crearRecepcionista", [RecepcionistasController::class, "store"]);
Route::put("actualizarRecepcionistas/{id}", [RecepcionistasController::class, "update"]);
Route::delete("eliminarRecepcionistas/{id}", [RecepcionistasController::class, "destroy"]);


Route::get("listarEspecialidades", [EspecialidadesController::class, "index"]);
Route::post("crearEspecialidades", [EspecialidadesController::class, "store"]);
Route::put("actualizarEspecialidades/{id}", [EspecialidadesController::class, "update"]);
Route::delete("eliminarEspecialidades/{id}", [EspecialidadesController::class, "destroy"]);


Route::get("listarEspecialidadesMedicos", [EspecialidadesMedicosController::class, "index"]);
Route::post("crearEspecialidadesMedicos", [EspecialidadesMedicosController::class, "store"]);
Route::put("actualizarEspecialidadesMedicos/{id}", [EspecialidadesMedicosController::class, "update"]);
Route::delete("eliminarEspecialidadesMedicos/{id}", [EspecialidadesMedicosController::class, "destroy"]);



Route::get("listarCitas", [CitasController::class, "index"]);
Route::post("crearCitas", [CitasController::class, "store"]);
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

//Filtrar cita por documento del paciente
Route::get("citasPorPacientes/{documento}", [CitasController::class, "citasPorPaciente"]);

//Citas del dia
Route::get("citasDelDia", [CitasController::class, "citasDeHoy"]);

//Total citas
Route::get("totalCitas", [CitasController::class, "contarCitas"]);

//Filtrar pacientes por nacionalidad
Route::get("pacientePorNacionalidad/{nacionalidad}", [PacientesController::class, "pacientePorNacionalidad"]);

//Filtar por rh
Route::get("pacientePorRh/{rh}", [PacientesController::class, "pacientePorRh"]);



