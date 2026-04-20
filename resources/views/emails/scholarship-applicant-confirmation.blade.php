@component('mail::message')
# Scholarship Application Received, {{ $firstName }}!

Thank you for your interest in the **{{ $programmeName }}** programme at Wenza Academy.

Your scholarship application reference is: **{{ $referenceCode }}**

@component('mail::panel')
**What happens next?**

Our admissions team will review your application and contact you within 4 weeks. Please keep your reference code safe as you may need it for follow-up enquiries.
@endcomponent

@component('mail::button', ['url' => $whatsappUrl, 'color' => 'primary'])
Join our WhatsApp community
@endcomponent

You can also [book a free info session]({{ $calendlyUrl }}) to learn more about the Wenza learning experience.

Best regards,

The Wenza Admissions Team
@endcomponent
