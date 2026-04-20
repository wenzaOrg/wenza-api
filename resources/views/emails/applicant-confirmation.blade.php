@component('mail::message')
# Welcome to Wenza, {{ $firstName }}!

Thank you for applying to our **{{ $programmeName }}** programme.

Your application reference is: **{{ $referenceCode }}**

@component('mail::panel')
**What happens next?**

Our admissions team will review your application and contact you within 2 business days to schedule a brief conversation about your goals.
@endcomponent

@component('mail::button', ['url' => $whatsappUrl, 'color' => 'primary'])
Join our WhatsApp community
@endcomponent

In the meantime, you can also [book a free info session]({{ $calendlyUrl }}) to ask any questions about the programme.

Looking forward to speaking with you soon,

The Wenza Team
@endcomponent
