@component('mail::message')
# New Contact Message

**{{ $message->full_name }}** sent a message.

- **Reference:** {{ $message->reference_code }}
- **Email:** {{ $message->email }}
- **Subject:** {{ ucwords(str_replace('_', ' ', $message->subject)) }}

**Message:**
{{ $message->message }}

@component('mail::button', ['url' => $adminUrl, 'color' => 'primary'])
View in admin
@endcomponent
@endcomponent