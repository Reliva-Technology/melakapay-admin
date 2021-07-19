@component('mail::message')
Dear **{{ $user->name }}**,  

Your password has been reset. Please find the information below:

Username:  {{ $user->username }}
Password:  {{ $user->password }}

Thank you for using MelakaPay.  

Sincerely,  
Administrator.  
{{ config('app.name') }}
@endcomponent