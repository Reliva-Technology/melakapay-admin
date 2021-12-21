@component('mail::message')
Dear **{{ $name }}**,  

This is to inform that we have made some changes in your user profile at MelakaPay, upon your request. The account details are as below:

@component('mail::panel')
Username:  {{ $username }}  
Password:  {{ $password }}
@endcomponent

Thank you for using MelakaPay.  

Sincerely,  
Administrator.  
{{ config('app.name') }}

@component('mail::subcopy')
If this is not your account / request, kindly contact our administrator using the contact details below the soonest:  

Telephone No: +606-3333333 ext 7656  
Email Address: melakapay_admin@melaka.gov.my  
Address: Bahagian Teknologi Maklumat Dan Komunikasi, Aras 1, Blok Temenggong, Seri Negeri, Hang Tuah Jaya, 75450 Ayer Keroh, Melaka MALAYSIA
@endcomponent

@endcomponent