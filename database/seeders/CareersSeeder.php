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
                'title' => 'Junior Frontend Developer',
                'department' => 'Development',
                'employment_type' => 'full-time',
                'location' => 'Toronto, ON',
                'remote_available' => true,
                'short_description' => 'Join our team as a Junior Frontend Developer and create amazing user experiences.',
                'description' => 'We are looking for a motivated Junior Frontend Developer to join our growing team in Canada. You will work on exciting projects while learning from experienced developers and contributing to our innovative web applications. This is a great opportunity for someone starting their career in web development.',
                'requirements' => [
                    '1-2 years of frontend development experience',
                    'Proficiency in HTML5, CSS3, and JavaScript',
                    'Basic knowledge of Vue.js or React',
                    'Understanding of responsive design principles',
                    'Familiarity with Git version control',
                    'Good problem-solving skills',
                    'Strong willingness to learn and grow',
                    'Good communication skills in English',
                    'College diploma or degree in Computer Science or related field'
                ],
                'benefits' => [
                    'Competitive entry-level salary',
                    'Mentorship program with senior developers',
                    'Learning and development opportunities',
                    'Health insurance coverage',
                    'Remote work options',
                    'Flexible working hours',
                    'Professional development budget',
                    'Career growth opportunities'
                ],
                'salary_range' => '$45,000 - $55,000 CAD',
                'experience_level' => 'junior',
                'skills' => ['HTML5', 'CSS3', 'JavaScript', 'Vue.js', 'Git', 'Responsive Design'],
                'is_active' => true,
                'priority' => 90,
                'application_deadline' => Carbon::now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Junior Backend Developer',
                'department' => 'Development',
                'employment_type' => 'full-time',
                'location' => 'Toronto, ON',
                'remote_available' => true,
                'short_description' => 'Great opportunity for a junior developer to grow with our expanding team.',
                'description' => 'We are offering an excellent opportunity for a Junior Backend Developer to start their career with our innovative development team in Canada. You will work on server-side applications while learning modern backend technologies and contributing to our growing platform.',
                'requirements' => [
                    '1-2 years of backend development experience',
                    'Basic knowledge of PHP or similar languages',
                    'Understanding of databases (MySQL)',
                    'Familiarity with Laravel or similar frameworks',
                    'Basic understanding of RESTful APIs',
                    'Knowledge of Git version control',
                    'Strong problem-solving abilities',
                    'Eagerness to learn new technologies',
                    'Good teamwork and communication skills'
                ],
                'benefits' => [
                    'Competitive entry-level salary',
                    'Comprehensive mentorship program',
                    'Learning and development opportunities',
                    'Health and dental insurance',
                    'Remote work flexibility',
                    'Flexible schedule',
                    'Career advancement opportunities',
                    'Modern development tools and equipment'
                ],
                'salary_range' => '$50,000 - $60,000 CAD',
                'experience_level' => 'junior',
                'skills' => ['PHP', 'Laravel', 'MySQL', 'Git', 'REST APIs'],
                'is_active' => true,
                'priority' => 85,
                'application_deadline' => Carbon::now()->addMonths(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Junior Full-Stack Developer',
                'department' => 'Development',
                'employment_type' => 'full-time',
                'location' => 'Toronto, ON',
                'remote_available' => true,
                'short_description' => 'Perfect entry-level position for aspiring full-stack developers.',
                'description' => 'Join our team as a Junior Full-Stack Developer and gain experience working with both frontend and backend technologies. This role is perfect for recent graduates or developers with some experience who want to expand their skills in a supportive environment.',
                'requirements' => [
                    '1+ year of web development experience',
                    'Basic knowledge of HTML, CSS, JavaScript',
                    'Familiarity with PHP and Laravel',
                    'Understanding of databases (MySQL)',
                    'Basic knowledge of Vue.js or similar framework',
                    'Git version control experience',
                    'Strong desire to learn and grow',
                    'Good analytical and problem-solving skills',
                    'Ability to work in a team environment'
                ],
                'benefits' => [
                    'Competitive salary for entry-level position',
                    'Comprehensive training program',
                    'Mentorship from senior developers',
                    'Health insurance benefits',
                    'Flexible remote work arrangements',
                    'Professional development budget',
                    'Growth opportunities within the company',
                    'Collaborative and supportive work environment'
                ],
                'salary_range' => '$48,000 - $58,000 CAD',
                'experience_level' => 'junior',
                'skills' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel', 'Vue.js', 'MySQL', 'Git'],
                'is_active' => true,
                'priority' => 80,
                'application_deadline' => Carbon::now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($careers as $career) {
            Career::updateOrCreate(
                ['title' => $career['title']],
                $career
            );
        }
    }
}
