@component('mail::message')
# New Scholarship Application

**{{ $application->first_name }} {{ $application->last_name }}** has submitted a scholarship application.

- **Reference:** {{ $application->reference_code }}
- **Email:** {{ $application->email }}
- **Phone:** {{ $application->phone }}
- **Gender:** {{ $application->gender }}
- **Location:** {{ $application->state_or_city }}, {{ $application->country }}
- **Status:** {{ $application->current_status }}
- **Education:** {{ $application->education_level }}
- **Programme:** {{ $application->course?->title }}
- **Intake:** {{ $application->cohort?->name }}
- **Learning Mode:** {{ $application->learning_mode }}
- **Prior Experience:** {{ $application->prior_tech_experience }}
- **Job Placement Interest:** {{ $application->wants_job_placement ? 'Yes' : 'No' }}

@component('mail::button', ['url' => $adminUrl, 'color' => 'primary'])
View and Review Application
@endcomponent
@endcomponent
