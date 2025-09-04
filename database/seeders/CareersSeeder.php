<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Career;
use Carbon\Carbon;

class CareersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careers = [
            [
                'title' => 'Senior Full-Stack Developer',
                'department' => 'Development',
                'employment_type' => 'full-time',
                'location' => 'Remote',
                'remote_available' => true,
                'short_description' => 'We are looking for an experienced full-stack developer to join our growing team.',
                'description' => 'We are seeking a talented Senior Full-Stack Developer to join our dynamic development team. You will be responsible for developing and maintaining web applications using modern technologies like Laravel, Vue.js, and more. The ideal candidate should have strong problem-solving skills and experience with both frontend and backend technologies.',
                'requirements' => [
                    '5+ years of experience in full-stack development',
                    'Proficiency in PHP, Laravel framework',
                    'Strong knowledge of JavaScript, Vue.js or React',
                    'Experience with MySQL/PostgreSQL databases',
                    'Understanding of RESTful APIs and web services',
                    'Knowledge of Git version control',
                    'Experience with cloud platforms (AWS, DigitalOcean)',
                    'Strong problem-solving and debugging skills',
                    'Excellent communication skills',
                    'Bachelor\'s degree in Computer Science or related field'
                ],
                'benefits' => [
                    'Competitive salary and performance bonuses',
                    'Flexible working hours and remote work options',
                    'Professional development opportunities',
                    'Health insurance coverage',
                    'Paid vacation and sick leave',
                    'Modern equipment and tools',
                    'Collaborative and innovative work environment',
                    'Opportunities for career advancement'
                ],
                'salary_range' => '$70,000 - $95,000',
                'experience_level' => 'senior',
                'skills' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'JavaScript', 'Git', 'AWS'],
                'is_active' => true,
                'priority' => 90,
                'application_deadline' => Carbon::now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Frontend Developer',
                'department' => 'Development',
                'employment_type' => 'full-time',
                'location' => 'New York, NY',
                'remote_available' => false,
                'short_description' => 'Join our team as a Frontend Developer and create amazing user experiences.',
                'description' => 'We are looking for a creative Frontend Developer to join our team. You will be responsible for implementing visual elements that users see and interact with in web applications. The ideal candidate should have a keen eye for design and excellent skills in modern frontend technologies.',
                'requirements' => [
                    '3+ years of frontend development experience',
                    'Proficiency in HTML5, CSS3, and JavaScript (ES6+)',
                    'Strong experience with Vue.js or React',
                    'Knowledge of responsive design principles',
                    'Experience with CSS preprocessors (Sass/Less)',
                    'Understanding of modern build tools (Webpack, Vite)',
                    'Familiarity with version control (Git)',
                    'Experience with UI/UX design principles',
                    'Knowledge of cross-browser compatibility',
                    'Strong attention to detail'
                ],
                'benefits' => [
                    'Competitive salary',
                    'Health insurance',
                    'Flexible working hours',
                    'Professional development budget',
                    'Modern office environment',
                    'Team building events',
                    'Paid time off'
                ],
                'salary_range' => '$55,000 - $75,000',
                'experience_level' => 'mid',
                'skills' => ['Vue.js', 'JavaScript', 'HTML5', 'CSS3', 'Sass', 'Responsive Design'],
                'is_active' => true,
                'priority' => 80,
                'application_deadline' => Carbon::now()->addMonths(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Junior Backend Developer',
                'department' => 'Development',
                'employment_type' => 'full-time',
                'location' => 'San Francisco, CA',
                'remote_available' => true,
                'short_description' => 'Great opportunity for a junior developer to grow with our expanding team.',
                'description' => 'We are offering an excellent opportunity for a Junior Backend Developer to start their career with our innovative development team. You will work on exciting projects while learning from experienced developers and contributing to our growing platform.',
                'requirements' => [
                    '1-2 years of backend development experience',
                    'Basic knowledge of PHP or Python',
                    'Understanding of databases (MySQL/PostgreSQL)',
                    'Familiarity with MVC frameworks',
                    'Basic understanding of RESTful APIs',
                    'Knowledge of version control systems (Git)',
                    'Strong willingness to learn and grow',
                    'Good problem-solving skills',
                    'Bachelor\'s degree in Computer Science or related field preferred'
                ],
                'benefits' => [
                    'Competitive entry-level salary',
                    'Mentorship program',
                    'Learning and development opportunities',
                    'Health insurance',
                    'Remote work options',
                    'Flexible schedule',
                    'Career growth path'
                ],
                'salary_range' => '$45,000 - $60,000',
                'experience_level' => 'junior',
                'skills' => ['PHP', 'MySQL', 'Git', 'REST APIs', 'Laravel'],
                'is_active' => true,
                'priority' => 70,
                'application_deadline' => Carbon::now()->addMonths(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'DevOps Engineer',
                'department' => 'Infrastructure',
                'employment_type' => 'full-time',
                'location' => 'Austin, TX',
                'remote_available' => true,
                'short_description' => 'Help us build and maintain scalable infrastructure for our growing platform.',
                'description' => 'We are seeking a skilled DevOps Engineer to help us build, deploy, and maintain our infrastructure. You will work with our development team to ensure smooth deployment processes and maintain high availability of our services.',
                'requirements' => [
                    '3+ years of DevOps experience',
                    'Strong knowledge of Linux systems',
                    'Experience with containerization (Docker, Kubernetes)',
                    'Proficiency with cloud platforms (AWS, GCP, Azure)',
                    'Knowledge of CI/CD pipelines',
                    'Experience with infrastructure as code (Terraform, Ansible)',
                    'Understanding of monitoring and logging tools',
                    'Strong scripting skills (Bash, Python)',
                    'Experience with database administration',
                    'Knowledge of security best practices'
                ],
                'benefits' => [
                    'Excellent salary package',
                    'Stock options',
                    'Remote work flexibility',
                    'Professional certifications support',
                    'Conference attendance opportunities',
                    'Health and dental insurance',
                    'Retirement plan matching'
                ],
                'salary_range' => '$80,000 - $110,000',
                'experience_level' => 'senior',
                'skills' => ['Docker', 'Kubernetes', 'AWS', 'Terraform', 'Linux', 'Python', 'CI/CD'],
                'is_active' => true,
                'priority' => 85,
                'application_deadline' => Carbon::now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'UI/UX Designer Intern',
                'department' => 'Design',
                'employment_type' => 'internship',
                'location' => 'Remote',
                'remote_available' => true,
                'short_description' => 'Perfect internship opportunity for aspiring UI/UX designers.',
                'description' => 'We are offering an exciting internship opportunity for a UI/UX Designer to join our creative team. This is a perfect chance to gain hands-on experience in digital design while working on real projects and learning from experienced designers.',
                'requirements' => [
                    'Currently pursuing or recently completed degree in Design, HCI, or related field',
                    'Basic knowledge of design principles and best practices',
                    'Familiarity with design tools (Figma, Adobe XD, Sketch)',
                    'Understanding of user-centered design processes',
                    'Basic knowledge of HTML/CSS is a plus',
                    'Strong portfolio showcasing design projects',
                    'Excellent communication and collaboration skills',
                    'Eagerness to learn and take feedback',
                    'Attention to detail'
                ],
                'benefits' => [
                    'Paid internship',
                    'Mentorship from senior designers',
                    'Real project experience',
                    'Flexible working hours',
                    'Remote work opportunity',
                    'Potential for full-time employment',
                    'Learning and development resources'
                ],
                'salary_range' => '$20 - $25 per hour',
                'experience_level' => 'junior',
                'skills' => ['Figma', 'Adobe XD', 'UI Design', 'UX Design', 'Prototyping'],
                'is_active' => true,
                'priority' => 60,
                'application_deadline' => Carbon::now()->addMonths(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'QA Engineer',
                'department' => 'Quality Assurance',
                'employment_type' => 'full-time',
                'location' => 'Chicago, IL',
                'remote_available' => true,
                'short_description' => 'Ensure the quality of our products through comprehensive testing strategies.',
                'description' => 'We are looking for a detail-oriented QA Engineer to develop and execute exploratory and automated tests to ensure product quality. You will be responsible for finding and reporting bugs and glitches, and working closely with the development team to ensure high-quality releases.',
                'requirements' => [
                    '2+ years of QA engineering experience',
                    'Experience with manual and automated testing',
                    'Knowledge of testing frameworks (PHPUnit, Jest, Cypress)',
                    'Understanding of software development lifecycle',
                    'Experience with bug tracking tools (Jira, Bugzilla)',
                    'Knowledge of API testing tools (Postman, Insomnia)',
                    'Basic knowledge of SQL',
                    'Experience with browser testing and debugging',
                    'Strong analytical and problem-solving skills',
                    'Attention to detail and quality focus'
                ],
                'benefits' => [
                    'Competitive salary',
                    'Health and dental insurance',
                    'Professional development opportunities',
                    'Remote work flexibility',
                    'Modern testing tools and equipment',
                    'Collaborative team environment',
                    'Career advancement opportunities'
                ],
                'salary_range' => '$60,000 - $80,000',
                'experience_level' => 'mid',
                'skills' => ['PHPUnit', 'Cypress', 'API Testing', 'SQL', 'Jira', 'Postman'],
                'is_active' => true,
                'priority' => 75,
                'application_deadline' => Carbon::now()->addMonths(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Product Manager',
                'department' => 'Product',
                'employment_type' => 'full-time',
                'location' => 'Seattle, WA',
                'remote_available' => false,
                'short_description' => 'Lead product development from conception to launch.',
                'description' => 'We are seeking an experienced Product Manager to drive the development of our digital products. You will work cross-functionally with engineering, design, and marketing teams to define product strategy, roadmap, and ensure successful product launches.',
                'requirements' => [
                    '4+ years of product management experience',
                    'Experience with product lifecycle management',
                    'Strong analytical and data-driven approach',
                    'Knowledge of agile development methodologies',
                    'Experience with product management tools (Jira, Confluence, Figma)',
                    'Understanding of UX/UI principles',
                    'Strong communication and leadership skills',
                    'Experience with A/B testing and user research',
                    'Bachelor\'s degree in Business, Engineering, or related field',
                    'Technical background preferred'
                ],
                'benefits' => [
                    'Excellent compensation package',
                    'Equity participation',
                    'Health, dental, and vision insurance',
                    'Professional development budget',
                    'Conference and training opportunities',
                    'Modern office environment',
                    'Team building activities'
                ],
                'salary_range' => '$90,000 - $120,000',
                'experience_level' => 'senior',
                'skills' => ['Product Strategy', 'Agile', 'Data Analysis', 'User Research', 'A/B Testing'],
                'is_active' => true,
                'priority' => 95,
                'application_deadline' => Carbon::now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mobile Developer (React Native)',
                'department' => 'Mobile Development',
                'employment_type' => 'contract',
                'location' => 'Los Angeles, CA',
                'remote_available' => true,
                'short_description' => 'Build amazing mobile experiences with React Native.',
                'description' => 'We are looking for a skilled Mobile Developer to join our team on a contract basis. You will be responsible for developing cross-platform mobile applications using React Native, ensuring optimal performance and user experience across iOS and Android platforms.',
                'requirements' => [
                    '3+ years of React Native development experience',
                    'Strong knowledge of JavaScript and TypeScript',
                    'Experience with mobile app deployment (App Store, Google Play)',
                    'Understanding of mobile UI/UX principles',
                    'Knowledge of native iOS/Android development is a plus',
                    'Experience with state management (Redux, Context API)',
                    'Familiarity with mobile testing frameworks',
                    'Understanding of mobile security best practices',
                    'Experience with CI/CD for mobile apps',
                    'Strong problem-solving skills'
                ],
                'benefits' => [
                    'Competitive contract rate',
                    'Flexible working arrangements',
                    'Remote work opportunity',
                    'Opportunity for contract extension',
                    'Access to latest mobile development tools',
                    'Collaborative development environment',
                    'Potential for full-time conversion'
                ],
                'salary_range' => '$70 - $90 per hour',
                'experience_level' => 'mid',
                'skills' => ['React Native', 'JavaScript', 'TypeScript', 'Redux', 'Mobile UI/UX'],
                'is_active' => false,
                'priority' => 65,
                'application_deadline' => Carbon::now()->addMonths(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($careers as $career) {
            Career::create($career);
        }
    }
}
