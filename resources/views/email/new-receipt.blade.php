@component('mail::message')
Dear **{{ $name }}**,  

New transaction receipt has been generated for your transaction. Please refer to the latest receipt attached for your future reference.

Thank you for using MelakaPay.  

Sincerely,  
Administrator.  
{{ config('app.name') }}

@component('mail::subcopy')
Telephone No: +606-3333333 ext 7656  
Email Address: melakapay_admin@melaka.gov.my  
Address: Bahagian Teknologi Maklumat Dan Komunikasi, Aras 1, Blok Temenggong, Seri Negeri, Hang Tuah Jaya, 75450 Ayer Keroh, Melaka MALAYSIA
@endcomponent

@endcomponent