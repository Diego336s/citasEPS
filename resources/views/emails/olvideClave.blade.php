@component('mail::message')
# Código de verificación

Tu código para restablecer la contraseña es:

@component('mail::panel')
**{{ $datos['codigo'] }}**
@endcomponent

Este código expirará en 15 minutos y es de unico uso.  
Si no solicitaste este correo, ignóralo.

Gracias,<br>
**{{ config('app.name') }}**
@endcomponent
