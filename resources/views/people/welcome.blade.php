@component('mail::message')
# Welcome, {{ $personName }}

Thank you for joining our system. We're excited to have you onboard!

@component('mail::button', ['url' => url('/')])
Visit Our Website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
