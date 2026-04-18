<?php

namespace Database\Seeders;

use App\Models\Cohort;
use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            // Engineering & Development
            [
                'slug' => 'software-development',
                'title' => 'Software Development',
                'category' => 'engineering',
                'description' => 'Master full-stack software development from web fundamentals to production-grade applications. You will build real projects using React, Node.js, PostgreSQL, and modern DevOps practices. This programme is designed for beginners with no prior coding experience who want to land their first developer role within six months.',
                'duration_weeks' => 12,
                'format' => 'cohort',
                'price_ngn' => 150000,
                'price_usd' => 100,
                'scholarship_price_ngn' => 15000,
                'is_published' => true,
            ],
            [
                'slug' => 'backend-development',
                'title' => 'Backend Development',
                'category' => 'engineering',
                'description' => 'Build robust, scalable server-side applications using Node.js, Express, PostgreSQL, and MongoDB. You will learn API design, authentication, caching with Redis, and deploying to production on cloud platforms. Perfect for those who want to specialise in the engine room of modern software.',
                'duration_weeks' => 10,
                'format' => 'cohort',
                'price_ngn' => 140000,
                'price_usd' => 95,
                'scholarship_price_ngn' => 14000,
                'is_published' => true,
            ],
            [
                'slug' => 'frontend-development',
                'title' => 'Frontend Development',
                'category' => 'engineering',
                'description' => 'Build pixel-perfect, accessible, and performant user interfaces using React, TypeScript, and Tailwind CSS. You will cover state management, performance optimisation, and testing — finishing with a portfolio of production-ready projects that demonstrate your craft to employers.',
                'duration_weeks' => 10,
                'format' => 'cohort',
                'price_ngn' => 140000,
                'price_usd' => 95,
                'scholarship_price_ngn' => 14000,
                'is_published' => true,
            ],
            [
                'slug' => 'devops-engineering',
                'title' => 'DevOps Engineering',
                'category' => 'engineering',
                'description' => 'Learn the tools and practices that keep modern software running reliably at scale. This programme covers Linux systems, Docker, Kubernetes, CI/CD pipelines, Terraform, AWS/GCP, and site reliability engineering. Graduates consistently land roles paying ₦7M–₦13M annually.',
                'duration_weeks' => 14,
                'format' => 'cohort',
                'price_ngn' => 160000,
                'price_usd' => 110,
                'scholarship_price_ngn' => 16000,
                'is_published' => true,
            ],

            // Data & Emerging Technologies
            [
                'slug' => 'data-science',
                'title' => 'Data Science',
                'category' => 'data',
                'description' => 'Move from data to decisions. This programme teaches Python, machine learning with scikit-learn and TensorFlow, statistical modelling, and communicating insights to non-technical stakeholders. You will work on two real-world datasets sourced from African companies.',
                'duration_weeks' => 16,
                'format' => 'cohort',
                'price_ngn' => 170000,
                'price_usd' => 115,
                'scholarship_price_ngn' => 17000,
                'is_published' => true,
            ],
            [
                'slug' => 'data-analytics',
                'title' => 'Data Analytics',
                'category' => 'data',
                'description' => 'Turn raw data into actionable business intelligence. Starting from Excel and SQL, you will progress through Power BI, Python (pandas, NumPy), and AI-augmented analytics using ChatGPT and Copilot. The 3-month programme ends with a real-world dataset capstone you can present to employers.',
                'duration_weeks' => 12,
                'format' => 'cohort',
                'price_ngn' => 130000,
                'price_usd' => 88,
                'scholarship_price_ngn' => 13000,
                'is_published' => true,
            ],
            [
                'slug' => 'ai-automation',
                'title' => 'AI & Automation',
                'category' => 'data',
                'description' => 'Build intelligent systems and automate repetitive workflows using Python, OpenAI APIs, LangChain, n8n, and Zapier. You will ship three working AI-powered tools during the programme, giving you a portfolio that stands out in a rapidly evolving job market.',
                'duration_weeks' => 12,
                'format' => 'cohort',
                'price_ngn' => 150000,
                'price_usd' => 100,
                'scholarship_price_ngn' => 15000,
                'is_published' => true,
            ],

            // Design & Creativity
            [
                'slug' => 'product-design',
                'title' => 'Product Design (UI/UX)',
                'category' => 'design',
                'description' => 'Design digital products that people love to use. You will learn user research, wireframing, Figma, interaction design, usability testing, and design systems. The programme culminates in a portfolio of three case studies — the standard requirement for a junior product designer role.',
                'duration_weeks' => 12,
                'format' => 'cohort',
                'price_ngn' => 130000,
                'price_usd' => 88,
                'scholarship_price_ngn' => 13000,
                'is_published' => true,
            ],
            [
                'slug' => 'graphics-design',
                'title' => 'Graphics Design',
                'category' => 'design',
                'description' => 'Build a professional graphics design practice using Adobe Photoshop, Illustrator, and InDesign. You will cover brand identity design, typography, print and digital layouts, and client communication. Wenza graduates regularly land roles at agencies and go on to build successful freelance practices.',
                'duration_weeks' => 10,
                'format' => 'cohort',
                'price_ngn' => 110000,
                'price_usd' => 75,
                'scholarship_price_ngn' => 11000,
                'is_published' => true,
            ],
            [
                'slug' => 'content-creation',
                'title' => 'Content Creation',
                'category' => 'design',
                'description' => 'Learn to create compelling content across video, social media, podcasting, and written formats. You will master the craft of storytelling, SEO writing, video editing (DaVinci Resolve, CapCut), and building an audience. The programme teaches you to monetise your skills through brand deals, consulting, and digital products.',
                'duration_weeks' => 8,
                'format' => 'cohort',
                'price_ngn' => 90000,
                'price_usd' => 60,
                'scholarship_price_ngn' => 9000,
                'is_published' => true,
            ],

            // Management & Business
            [
                'slug' => 'product-management',
                'title' => 'Product Management',
                'category' => 'business',
                'description' => 'Learn what it takes to define, build, and launch great products. This 11-module programme covers market research, product vision, Agile methodology, user-centred design, data analytics, and stakeholder management. Wenza PM graduates earn ₦7M–₦12M annually at top Nigerian tech companies.',
                'duration_weeks' => 12,
                'format' => 'cohort',
                'price_ngn' => 150000,
                'price_usd' => 100,
                'scholarship_price_ngn' => 15000,
                'is_published' => true,
            ],
            [
                'slug' => 'project-management',
                'title' => 'Project Management',
                'category' => 'business',
                'description' => 'Become a certified project manager using PMBOK, Agile, and Scrum frameworks. You will learn how to scope, plan, execute, and close projects across industries. The programme prepares you for PMP and CAPM certifications and connects you with hiring partners actively seeking project management talent.',
                'duration_weeks' => 10,
                'format' => 'cohort',
                'price_ngn' => 120000,
                'price_usd' => 80,
                'scholarship_price_ngn' => 12000,
                'is_published' => true,
            ],
            [
                'slug' => 'virtual-assistant',
                'title' => 'Virtual Assistant',
                'category' => 'business',
                'description' => 'Launch a career as a professional virtual assistant serving clients globally. You will learn executive assistance, calendar management, email management, bookkeeping basics, social media management, and tools like Notion, Asana, and Slack. Wenza VA graduates earn $800–$2,500 per month working remotely.',
                'duration_weeks' => 8,
                'format' => 'cohort',
                'price_ngn' => 90000,
                'price_usd' => 60,
                'scholarship_price_ngn' => 9000,
                'is_published' => true,
            ],
            [
                'slug' => 'digital-marketing',
                'title' => 'Digital Marketing',
                'category' => 'business',
                'description' => 'Drive real business growth through digital channels. This programme covers SEO, Google Ads, Meta advertising, email marketing, marketing analytics, and conversion optimisation. You will manage a live ad campaign during the course, giving you verifiable proof of results for your portfolio.',
                'duration_weeks' => 10,
                'format' => 'cohort',
                'price_ngn' => 120000,
                'price_usd' => 80,
                'scholarship_price_ngn' => 12000,
                'is_published' => true,
            ],

            // Security
            [
                'slug' => 'cybersecurity',
                'title' => 'Cybersecurity',
                'category' => 'security',
                'description' => 'Train as a professional cybersecurity analyst defending against the attacks that cost African businesses billions annually. Over four months you will cover networking and Linux foundations, defensive security (Splunk, Wazuh, Wireshark), offensive security (Kali Linux, Metasploit, OWASP Top 10), and a "Defend & Respond" capstone. Graduates earn ₦6M–₦10M annually.',
                'duration_weeks' => 16,
                'format' => 'cohort',
                'price_ngn' => 180000,
                'price_usd' => 120,
                'scholarship_price_ngn' => 18000,
                'is_published' => true,
            ],

            // Reserved slot — Cloud Engineering
            [
                'slug' => 'cloud-engineering',
                'title' => 'Cloud Engineering',
                'category' => 'engineering',
                'description' => 'Design, build, and manage scalable cloud infrastructure on AWS and Azure. This programme covers cloud architecture, serverless computing, containerisation with Docker and Kubernetes, infrastructure-as-code with Terraform, and cost optimisation. Graduates are prepared for AWS Solutions Architect and Azure Administrator certifications.',
                'duration_weeks' => 14,
                'format' => 'cohort',
                'price_ngn' => 170000,
                'price_usd' => 115,
                'scholarship_price_ngn' => 17000,
                'is_published' => true,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(
                ['slug' => $courseData['slug']],
                $courseData
            );

            // Seed a starter cohort for each course
            Cohort::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'name' => 'Phoenix Cohort',
                ],
                [
                    'start_date' => now()->addDays(30)->toDateString(),
                    'end_date' => now()->addDays(30 + ($courseData['duration_weeks'] * 7))->toDateString(),
                    'capacity' => 30,
                    'status' => 'upcoming',
                ]
            );
        }
    }
}
