<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'source' => 'linkedin',
                'content' => 'Wenza completely changed my career trajectory. I joined as a secondary school teacher with zero coding knowledge. Eight months later I accepted a frontend developer role at a fintech startup paying ₦5.2M annually. The cohort structure kept me accountable when I would have otherwise quit.',
                'author_name' => 'Chidi Okafor',
                'author_role' => 'Frontend Developer, Kuda Bank',
                'is_featured' => true,
            ],
            [
                'source' => 'twitter',
                'content' => 'I applied to Wenza on a scholarship after losing my job. The mentors genuinely invested in my success. I went from unemployed to a ₦4.8M data analyst role in six months. If you are on the fence, just apply — the scholarship removes every financial excuse.',
                'author_name' => 'Amara Nwosu',
                'author_role' => 'Data Analyst, Flutterwave',
                'is_featured' => true,
            ],
            [
                'source' => 'manual',
                'content' => 'The Product Management programme at Wenza is the most practical PM training I have come across in Nigeria. We worked on real product problems, not case studies. I now lead a product team of five engineers at a Series B startup.',
                'author_name' => 'Tunde Adeyemi',
                'author_role' => 'Product Manager, Paystack',
                'is_featured' => true,
            ],
            [
                'source' => 'linkedin',
                'content' => 'As a remote virtual assistant, I now earn $1,800 per month serving clients in the UK and US — from my bedroom in Lagos. Wenza taught me not just the technical skills but how to position myself, price my services, and find clients. Life-changing programme.',
                'author_name' => 'Fatima Aliyu',
                'author_role' => 'Senior Virtual Assistant (Remote)',
                'is_featured' => true,
            ],
            [
                'source' => 'twitter',
                'content' => 'Six months ago I was a civil servant earning ₦85,000 a month. Today I am a junior DevOps engineer earning ₦7.5M annually. The Wenza cybersecurity programme gave me the specific skills Nigerian companies are desperate to hire. The certificate verification gave employers confidence in my credentials.',
                'author_name' => 'Emeka Obi',
                'author_role' => 'Junior DevOps Engineer, Interswitch',
                'is_featured' => false,
            ],
            [
                'source' => 'linkedin',
                'content' => 'I had tried three other online courses before Wenza and given up on all of them. The difference is the cohort accountability — knowing 25 other people are progressing with you and that a mentor will check in if you miss a session changes everything.',
                'author_name' => 'Blessing Eze',
                'author_role' => 'Product Designer, Carbon',
                'is_featured' => true,
            ],
            [
                'source' => 'manual',
                'content' => 'My team has hired four Wenza graduates in the past year. The quality of their projects and the depth of their problem-solving consistently stand out. We now partner with Wenza because it is the most reliable pipeline of job-ready junior talent we have found.',
                'author_name' => 'Kunle Olawale',
                'author_role' => 'CTO, Cowrywise',
                'is_featured' => true,
            ],
            [
                'source' => 'twitter',
                'content' => 'Completed the Backend Development programme whilst working full time. The evening and weekend schedule actually worked. The capstone project is something I am genuinely proud to show in interviews. Landed a remote role with a UK startup within three weeks of graduating.',
                'author_name' => 'Ngozi Eze',
                'author_role' => 'Backend Developer (Remote, UK startup)',
                'is_featured' => false,
            ],
            [
                'source' => 'linkedin',
                'content' => 'I was sceptical about doing cybersecurity online rather than a traditional degree path. But Wenza\'s curriculum is industry-current in a way that university courses simply cannot keep up with. I passed my CompTIA Security+ on the first attempt, which my employer covered.',
                'author_name' => 'Ibrahim Musa',
                'author_role' => 'Security Analyst, Access Bank',
                'is_featured' => false,
            ],
            [
                'source' => 'manual',
                'content' => 'As someone in my 40s who feared it was too late to switch careers, Wenza proved me wrong. The instructors met me where I was, the community was supportive, and I now run digital marketing campaigns for FMCG brands across West Africa. Age is just a number.',
                'author_name' => 'Grace Adeyemi',
                'author_role' => 'Digital Marketing Consultant',
                'is_featured' => true,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }
    }
}
