<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Database\Seeder;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentorsData = [
            [
                'first_name' => 'Chukwuemeka',
                'last_name' => 'Obi',
                'title' => 'Senior Backend Engineer at Paystack',
                'bio' => 'Chukwuemeka has over 8 years of experience building scalable payment infrastructure. He leads backend teams at Paystack and is passionate about teaching system design and clean code architectures.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Chukwuemeka+Obi&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 8,
                'target_courses' => ['backend-development', 'software-development'],
            ],
            [
                'first_name' => 'Afolabi',
                'last_name' => 'Adeleke',
                'title' => 'Product Manager at Flutterwave',
                'bio' => 'Afolabi transitioned from engineering to product management and now leads core payment integrations at Flutterwave. He excels at breaking down complex user problems into actionable roadmaps.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Afolabi+Adeleke&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 6,
                'target_courses' => ['product-management'],
            ],
            [
                'first_name' => 'Hadiza',
                'last_name' => 'Musa',
                'title' => 'Senior Data Scientist',
                'bio' => 'Hadiza has spent nearly a decade deriving insights from large transaction datasets across the African fintech landscape. She specializes in predictive analytics and credit scoring models.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Hadiza+Musa&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 9,
                'target_courses' => ['data-science', 'data-analytics'],
            ],
            [
                'first_name' => 'Seun',
                'last_name' => 'Adeyemi',
                'title' => 'DevOps Engineer at Kuda',
                'bio' => 'Seun maintains the infrastructure that powers one of Nigeria\'s largest digital banks. He is an AWS Certified DevOps Engineer and a Terraform expert.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Seun+Adeyemi&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 7,
                'target_courses' => ['devops-engineering', 'software-development'],
            ],
            [
                'first_name' => 'Ngozi',
                'last_name' => 'Eze',
                'title' => 'Senior Product Designer at PiggyVest',
                'bio' => 'Ngozi crafts intuitive financial products used by millions. She is a strong advocate for accessible design and comprehensive user research.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Ngozi+Eze&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 5,
                'target_courses' => ['product-design', 'graphics-design'],
            ],
            [
                'first_name' => 'Tunde',
                'last_name' => 'Balogun',
                'title' => 'Cybersecurity Analyst at Interswitch',
                'bio' => 'With over a decade of experience securing enterprise networks, Tunde leads threat hunting and incident response. He holds CISSP and CEH certifications.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Tunde+Balogun&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 12,
                'target_courses' => ['cybersecurity'],
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Ibrahim',
                'title' => 'Cloud Solutions Architect at AWS',
                'bio' => 'Fatima helps enterprise clients migrate to the cloud securely and efficiently. She specializes in highly available, fault-tolerant architectures.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Fatima+Ibrahim&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 11,
                'target_courses' => ['cloud-engineering'],
            ],
            [
                'first_name' => 'Olumide',
                'last_name' => 'Coker',
                'title' => 'Frontend Engineer at Andela',
                'bio' => 'Olumide is deeply passionate about web performance and accessibility. He builds complex React interfaces for global clients.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Olumide+Coker&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 4,
                'target_courses' => ['frontend-development', 'software-development'],
            ],
            [
                'first_name' => 'Amara',
                'last_name' => 'Okafor',
                'title' => 'Digital Marketer',
                'bio' => 'Amara is a growth marketer specializing in acquisition frameworks and paid media. She has scaled user bases for multiple YC-backed startups in Africa.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Amara+Okafor&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 5,
                'target_courses' => ['digital-marketing', 'content-creation'],
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Okonkwo',
                'title' => 'AI/ML Engineer at Microsoft',
                'bio' => 'James works on deploying machine learning models to production. He is an active contributor to open-source LLM tooling and orchestration frameworks.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=James+Okonkwo&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 7,
                'target_courses' => ['ai-automation', 'data-science'],
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Chen',
                'title' => 'Staff Engineer at Stripe',
                'bio' => 'Based in the US, Sarah brings global engineering standards to the Wenza curriculum. She focuses on distributed systems and API design.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Sarah+Chen&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 14,
                'target_courses' => ['backend-development', 'software-development', 'cloud-engineering'],
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Mensah',
                'title' => 'Senior Project Manager',
                'bio' => 'David is a certified Scrum Master and PMP based in Ghana. He has delivered multi-million dollar tech projects across the continent on time and under budget.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=David+Mensah&background=random',
                'linkedin_url' => 'https://linkedin.com/in/example',
                'years_experience' => 10,
                'target_courses' => ['project-management', 'virtual-assistant'],
            ],
        ];

        // Fetch courses for attachment
        $allCourses = Course::all()->keyBy('slug');

        foreach ($mentorsData as $data) {
            $targetCourses = $data['target_courses'];
            unset($data['target_courses']);

            $mentor = Mentor::create($data);

            $courseIdsToAttach = collect($targetCourses)
                ->map(fn ($slug) => $allCourses->get($slug)?->id)
                ->filter()
                ->toArray();

            if (! empty($courseIdsToAttach)) {
                $mentor->courses()->attach($courseIdsToAttach);
            }
        }
    }
}
