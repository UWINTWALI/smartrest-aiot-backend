@component('mail::message')
# Test Email

This is a test email from SmartRest AIoT backend application.

@component('mail::button', ['url' => config('app.url')])
Visit Our Site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
