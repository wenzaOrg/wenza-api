@component('mail::message')
# New Lead Submitted

**{{ $lead->full_name }}** just applied.

- **Reference:** {{ $lead->reference_code }}
- **Email:** {{ $lead->email }}
- **Phone:** {{ $lead->phone }}
- **Age:** {{ $lead->age }}
- **Programme:** {{ $lead->course?->title ?? 'General enquiry' }}
- **Employment:** {{ $lead->employment_status }}
- **Education:** {{ $lead->education_level }}
- **Scholarship interest:** {{ $lead->wants_scholarship ? 'Yes' : 'No' }}

**Goals:**
{{ $lead->goals }}

@component('mail::button', ['url' => $adminUrl, 'color' => 'primary'])
View in admin
@endcomponent
@endcomponent
