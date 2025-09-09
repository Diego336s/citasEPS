# 🏥 API de Gestión Hospitalaria - citasEPS

Esta es una API de gestión hospitalaria desarrollada con Laravel.  
Permite gestionar doctores, pacientes, recepcionistas, especialidades y citas a través de una API RESTful.  
La autenticación se realiza mediante Laravel Sanctum.

---

## ⚙ Requisitos del Sistema

- ✅ PHP >= 8.2  
- ✅ [Composer](https://getcomposer.org/)  
- ✅ Una base de datos (SQLite, MySQL, MariaDB, etc.)  
- ✅ [Node.js](https://nodejs.org/) y npm (para recursos de frontend con Vite y Tailwind CSS)  

---

## 📦 Instalación

```bash
# 1. Clonar el repositorio
git clone [URL_DEL_REPOSITORIO]
cd citasEPS

# 2. Instalar dependencias de Composer
composer install

# 3. Copiar archivo de entorno
cp .env.example .env

# 4. Generar la clave de la aplicación
php artisan key:generate

# 5. Configurar base de datos en .env
# Por defecto usa SQLite, pero puedes habilitar MySQL/MariaDB

# 6. Ejecutar migraciones
php artisan migrate

# 7. Instalar dependencias de frontend
npm install
npm run dev

# 8. Iniciar el servidor local
php artisan serve


---

🔐 Autenticación

La API utiliza Laravel Sanctum.
Para acceder a endpoints protegidos debes registrarte o iniciar sesión para obtener un token de acceso.
El token debe enviarse en el encabezado:

Authorization: Bearer {token}


---

📌 Endpoints de Autenticación

Método	Endpoint	Descripción

POST	/api/registrar	Registrar un nuevo usuario
POST	/api/login	Autenticar usuario y devolver token
GET	/api/me	Obtener datos del usuario autenticado (requiere token)


Ejemplo de login (cURL):

curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@test.com", "password": "password"}'


---

🧩 Endpoints de Gestión de Recursos (CRUD)

👨‍⚕ Médicos

GET /api/listarMedicos

POST /api/crearMedico

PUT /api/actualizarMedico/{id}

DELETE /api/eliminarMedico/{id}

GET /api/totalMedicos

GET /api/medicoPorDocumento/{documento}


Ejemplo (listar médicos):

curl -X GET http://localhost:8000/api/listarMedicos \
  -H "Authorization: Bearer {token}"


---

🧑 Pacientes

GET /api/listarPaciente

POST /api/crearPaciente

PUT /api/actualizarPaciente/{id}

DELETE /api/eliminarPaciente/{id}

GET /api/pacientesPorSexo/{sexo}

GET /api/pacientePorNacionalidad/{nacionalidad}

GET /api/pacientePorRh/{rh}



---

🏢 Recepcionistas

GET /api/listarRecepcionistas

POST /api/crearRecepcionistas

PUT /api/actualizarRecepcionistas/{id}

DELETE /api/eliminarRecepcionistas/{id}



---

🩺 Especialidades

GET /api/listarEspecialidades

POST /api/crearEspecialidades

PUT /api/actualizarEspecialidades/{id}

DELETE /api/eliminarEspecialidades/{id}

GET /api/totalEspecialidades



---

🔗 Relación Médicos - Especialidades

GET /api/listarEspecialidadesMedicos

POST /api/crearEspecialidadesMedicos

PUT /api/actualizarEspecialidadesMedicos/{id}

DELETE /api/eliminarEspecialidadesMedicos/{id}



---

📅 Citas

GET /api/listarCitas

POST /api/crearCitas

PUT /api/actualizarCitas/{id}

DELETE /api/eliminarCitas/{id}

GET /api/citasConfirmadas

GET /api/citasPorPacientes/{documento}

GET /api/citasDelDia

GET /api/totalCitas



---

✅ Checklist del Proyecto

[x] Configuración inicial de Laravel

[x] Autenticación con Sanctum

[x] CRUD de Médicos, Pacientes, Recepcionistas y Especialidades

[x] CRUD de Citas

[ ] Tests unitarios y de integración

[ ] Documentación con Swagger



---

🤝 Contribución

1. Haz un fork del repositorio


2. Crea una rama con tu nueva funcionalidad

git checkout -b feature/nueva-funcionalidad


3. Haz commit de tus cambios

git commit -m "Agregada nueva funcionalidad"


4. Sube tu rama

git push origin feature/nueva-funcionalidad


5. Abre un Pull Request 🚀




---

📜 Licencia

Este proyecto está bajo la licencia MIT.
Puedes usarlo y modificarlo libremente respetando la licencia.


---

👨‍💻 Autor

Nombre: Diego Sanabria

Rol: Desarrollador FullStack

GitHub: https://github.com/Diego336s/citasEPS.git

Documentacion_Postman: https://documenter.getpostman.com/view/45753118/2sB3HkqfhV
