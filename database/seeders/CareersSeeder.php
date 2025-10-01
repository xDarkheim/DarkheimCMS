<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

class CareersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Career::create([
            'title' => 'Junior Frontend Developer',
            'department' => 'Development',
            'employment_type' => 'full-time',
            'location' => 'Remote / Poland',
            'remote_available' => true,
            'short_description' => 'Looking for a motivated Junior Frontend Developer eager to learn and grow in a supportive environment.',
            'description' => 'We are looking for a passionate Junior Frontend Developer to join our team and grow their skills in modern web development.

This is a perfect opportunity for newcomers to the industry who want to work with modern technologies and learn from experienced developers.

What you will learn:
- Modern JavaScript frameworks (React, Vue.js)
- Responsive web design principles
- Frontend build tools and workflows
- API integration and state management
- Best practices in web development',
            'requirements' => [
                '0-1 year of web development experience',
                'Basic knowledge of HTML, CSS, and JavaScript',
                'Familiarity with responsive design principles',
                'Understanding of version control (Git)',
                'Strong willingness to learn and improve',
                'Good problem-solving attitude',
                'Basic English communication skills',
                'University degree or bootcamp certificate preferred'
            ],
            'benefits' => [
                'Mentorship from experienced developers',
                'Learning budget for courses and books',
                'Flexible working hours',
                'Remote work opportunities',
                'Health insurance coverage',
                'Paid vacation and sick leave',
                'Modern development tools',
                'Career growth opportunities',
                'Supportive team environment',
                'Regular code reviews and feedback'
            ],
            'salary_range' => '$1,200 - $2,000 per month',
            'experience_level' => 'junior',
            'skills' => [
                'HTML5',
                'CSS3',
                'JavaScript',
                'Git',
                'Responsive Design',
                'Basic React/Vue.js',
                'Problem Solving',
                'Willingness to Learn'
            ],
            'is_active' => true,
            'priority' => 10,
            'application_deadline' => now()->addMonths(3)
        ]);

        Career::create([
            'title' => 'Junior Backend Developer',
            'department' => 'Development',
            'employment_type' => 'full-time',
            'location' => 'Remote / Poland',
            'remote_available' => true,
            'short_description' => 'Seeking a Junior Backend Developer to learn server-side development with PHP and Laravel.',
            'description' => 'Join our backend team as a Junior Developer and learn to build robust server-side applications.

This position is ideal for someone starting their career in backend development who wants to master PHP and Laravel framework.

What you will learn:
- PHP programming and Laravel framework
- Database design and optimization
- API development and integration
- Server management and deployment
- Code testing and quality assurance',
            'requirements' => [
                '0-1 year of backend development experience',
                'Basic knowledge of PHP or similar programming language',
                'Understanding of databases (MySQL preferred)',
                'Familiarity with basic web concepts',
                'Knowledge of version control (Git)',
                'Strong motivation to learn',
                'Good analytical thinking',
                'Basic English communication skills'
            ],
            'benefits' => [
                'Mentorship program',
                'Professional development support',
                'Flexible schedule',
                'Remote work option',
                'Health insurance',
                'Learning resources provided',
                'Career advancement opportunities',
                'Collaborative team culture',
                'Regular training sessions'
            ],
            'salary_range' => '$1,300 - $2,200 per month',
            'experience_level' => 'junior',
            'skills' => [
                'PHP',
                'MySQL',
                'Basic Laravel',
                'Git',
                'HTML/CSS',
                'Problem Solving',
                'Database Basics',
                'Learning Mindset'
            ],
            'is_active' => true,
            'priority' => 9,
            'application_deadline' => now()->addMonths(3)
        ]);

        Career::create([
            'title' => 'Junior Full-Stack Developer',
            'department' => 'Development',
            'employment_type' => 'full-time',
            'location' => 'Remote / Poland',
            'remote_available' => true,
            'short_description' => 'Entry-level Full-Stack Developer position for someone eager to work on both frontend and backend.',
            'description' => 'Perfect opportunity for a Junior Full-Stack Developer to gain experience in both frontend and backend technologies.

You will work on complete web applications from start to finish, learning the full development cycle under guidance of experienced developers.

Growth opportunities:
- Learn modern web development stack
- Work on real client projects
- Participate in code reviews
- Attend tech talks and workshops
- Contribute to open-source projects',
            'requirements' => [
                '0-2 years of web development experience',
                'Basic knowledge of HTML, CSS, JavaScript',
                'Some experience with PHP or similar backend language',
                'Understanding of databases',
                'Familiarity with Git',
                'Strong desire to learn both frontend and backend',
                'Good communication skills',
                'Problem-solving mindset'
            ],
            'benefits' => [
                'Comprehensive mentorship',
                'Learning and development budget',
                'Flexible working arrangements',
                'Remote work possibilities',
                'Health and dental insurance',
                'Professional certification support',
                'Modern development environment',
                'Team building activities',
                'Career progression plan'
            ],
            'salary_range' => '$1,400 - $2,300 per month',
            'experience_level' => 'junior',
            'skills' => [
                'HTML5',
                'CSS3',
                'JavaScript',
                'PHP',
                'MySQL',
                'Git',
                'Basic Laravel',
                'Problem Solving',
                'Full-Stack Basics'
            ],
            'is_active' => true,
            'priority' => 8,
            'application_deadline' => now()->addMonths(4)
        ]);
    }
}
