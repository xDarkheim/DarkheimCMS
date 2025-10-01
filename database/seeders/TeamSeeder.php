<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeamMember;
use Carbon\Carbon;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamMembers = [
            [
                'name' => 'Alex Johnson',
                'position' => 'Lead Full-Stack Developer',
                'department' => 'Development',
                'bio' => 'Alex is our lead developer with over 8 years of experience in web development. He specializes in Laravel, Vue.js, and modern web technologies. Alex leads our development team and ensures code quality and best practices across all projects.',
                'email' => 'alex.johnson@darkheim.dev',
                'skills' => ['Laravel', 'Vue.js', 'PHP', 'JavaScript', 'MySQL', 'Docker', 'AWS', 'Git'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/alexjohnson',
                    'github' => 'https://github.com/alexjohnson',
                    'twitter' => 'https://twitter.com/alex_codes'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subYears(3),
                'priority' => 100,
                'show_on_website' => true,
                'avatar' => '/images/team/alex-johnson.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Chen',
                'position' => 'Senior UI/UX Designer',
                'department' => 'Design',
                'bio' => 'Sarah is our creative lead with a passion for user-centered design. She has over 6 years of experience in digital design and specializes in creating intuitive and beautiful user interfaces for web and mobile applications.',
                'email' => 'sarah.chen@darkheim.dev',
                'skills' => ['Figma', 'Adobe XD', 'Sketch', 'Photoshop', 'Illustrator', 'Prototyping', 'User Research'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/sarahchen',
                    'dribbble' => 'https://dribbble.com/sarahchen',
                    'behance' => 'https://behance.net/sarahchen'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subYears(2)->subMonths(6),
                'priority' => 95,
                'show_on_website' => true,
                'avatar' => '/images/team/sarah-chen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Rodriguez',
                'position' => 'DevOps Engineer',
                'department' => 'Infrastructure',
                'bio' => 'Michael is our infrastructure expert who ensures our applications run smoothly and securely. With 5 years of experience in DevOps and cloud technologies, he manages our deployment pipelines and server infrastructure.',
                'email' => 'michael.rodriguez@darkheim.dev',
                'skills' => ['Docker', 'Kubernetes', 'AWS', 'Terraform', 'Jenkins', 'Linux', 'Python', 'Bash'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/michaelrodriguez',
                    'github' => 'https://github.com/mrodriguez',
                    'twitter' => 'https://twitter.com/devops_mike'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subYears(2),
                'priority' => 90,
                'show_on_website' => true,
                'avatar' => '/images/team/michael-rodriguez.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emily Davis',
                'position' => 'Frontend Developer',
                'department' => 'Development',
                'bio' => 'Emily is a talented frontend developer with a keen eye for detail and user experience. She specializes in Vue.js and modern CSS frameworks, creating responsive and interactive web applications.',
                'email' => 'emily.davis@darkheim.dev',
                'skills' => ['Vue.js', 'React', 'JavaScript', 'TypeScript', 'Sass', 'Tailwind CSS', 'Webpack'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/emilydavis',
                    'github' => 'https://github.com/emilydavis',
                    'twitter' => 'https://twitter.com/emily_codes'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subYears(1)->subMonths(8),
                'priority' => 85,
                'show_on_website' => true,
                'avatar' => '/images/team/emily-davis.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David Kim',
                'position' => 'Backend Developer',
                'department' => 'Development',
                'bio' => 'David is our backend specialist with expertise in server-side technologies and database design. He focuses on building robust and scalable APIs that power our applications.',
                'email' => 'david.kim@darkheim.dev',
                'skills' => ['Laravel', 'PHP', 'Node.js', 'PostgreSQL', 'MySQL', 'Redis', 'REST APIs', 'GraphQL'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/davidkim',
                    'github' => 'https://github.com/davidkim',
                    'twitter' => 'https://twitter.com/david_backend'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subYears(1)->subMonths(4),
                'priority' => 80,
                'show_on_website' => true,
                'avatar' => '/images/team/david-kim.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lisa Thompson',
                'position' => 'Project Manager',
                'department' => 'Management',
                'bio' => 'Lisa coordinates our development projects and ensures smooth communication between team members and clients. With her strong organizational skills and technical background, she keeps our projects on track and delivered on time.',
                'email' => 'lisa.thompson@darkheim.dev',
                'skills' => ['Project Management', 'Agile', 'Scrum', 'Jira', 'Confluence', 'Team Leadership'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/lisathompson',
                    'twitter' => 'https://twitter.com/lisa_pm'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subYears(1)->subMonths(2),
                'priority' => 75,
                'show_on_website' => true,
                'avatar' => '/images/team/lisa-thompson.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'James Wilson',
                'position' => 'Junior Developer',
                'department' => 'Development',
                'bio' => 'James is our newest team member, bringing fresh ideas and enthusiasm to our development team. He is currently learning our tech stack and contributing to various projects under senior developer guidance.',
                'email' => 'james.wilson@darkheim.dev',
                'skills' => ['PHP', 'JavaScript', 'Laravel', 'Vue.js', 'MySQL', 'Git'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/jameswilson',
                    'github' => 'https://github.com/jameswilson'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subMonths(6),
                'priority' => 70,
                'show_on_website' => true,
                'avatar' => '/images/team/james-wilson.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maria Garcia',
                'position' => 'Quality Assurance Engineer',
                'department' => 'Quality Assurance',
                'bio' => 'Maria ensures the quality and reliability of our applications through comprehensive testing strategies. She has experience in both manual and automated testing methodologies.',
                'email' => 'maria.garcia@darkheim.dev',
                'skills' => ['Manual Testing', 'Automated Testing', 'Selenium', 'Jest', 'Cypress', 'Bug Tracking'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/mariagarcia'
                ],
                'status' => 'on-leave',
                'joined_date' => Carbon::now()->subMonths(10),
                'priority' => 65,
                'show_on_website' => false,
                'avatar' => '/images/team/maria-garcia.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Taylor',
                'position' => 'Mobile Developer',
                'department' => 'Development',
                'bio' => 'Robert specializes in mobile app development for both iOS and Android platforms. He brings cross-platform expertise using React Native and native development tools.',
                'email' => 'robert.taylor@darkheim.dev',
                'skills' => ['React Native', 'Swift', 'Kotlin', 'Flutter', 'Expo', 'Firebase', 'Mobile UI/UX'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/roberttaylor',
                    'github' => 'https://github.com/roberttaylor',
                    'twitter' => 'https://twitter.com/mobile_rob'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subMonths(8),
                'priority' => 82,
                'show_on_website' => true,
                'avatar' => '/images/team/robert-taylor.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anna Kowalski',
                'position' => 'Business Analyst',
                'department' => 'Business',
                'bio' => 'Anna bridges the gap between business requirements and technical implementation. She analyzes client needs and translates them into clear technical specifications for our development team.',
                'email' => 'anna.kowalski@darkheim.dev',
                'skills' => ['Business Analysis', 'Requirements Gathering', 'Process Modeling', 'Documentation', 'Stakeholder Management'],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/annakowalski'
                ],
                'status' => 'active',
                'joined_date' => Carbon::now()->subMonths(14),
                'priority' => 77,
                'show_on_website' => true,
                'avatar' => '/images/team/anna-kowalski.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($teamMembers as $member) {
            TeamMember::create($member);
        }
    }
}
