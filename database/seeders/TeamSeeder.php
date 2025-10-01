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
                'name' => 'Dmytro Hovenko',
                'position' => 'Full-Stack Web Developer & Founder',
                'department' => 'Development',
                'bio' => 'Results-driven Full-Stack Web Developer with 1+ years of professional experience in developing scalable web applications using modern technologies. Specialized in PHP, Laravel, Vue.js, and database management with a proven track record of delivering high-quality solutions. Authorized to work in Canada with experience in remote collaboration and agile development methodologies.',
                'email' => 'dmytro.hovenko@gmail.com',
                'skills' => ['PHP', 'Laravel', 'Vue.js', 'JavaScript', 'MySQL', 'MariaDB', 'MSSQL', 'Docker', 'Linux', 'Git', 'HTML5', 'CSS3', 'RESTful APIs'],
                'social_links' => [
                    'website' => 'https://hovenko.com',
                    'linkedin' => 'https://linkedin.com/in/dmytro-hovenko-69316935a',
                    'github' => 'https://github.com/xDarkheim',
                    'email' => 'dmytro.hovenko@gmail.com'
                ],
                'status' => 'active',
                'joined_date' => Carbon::create(2025, 1, 1), // Started freelancing January 2025
                'priority' => 100,
                'show_on_website' => true,
                'avatar' => 'https://hovenko.com/assets/images/dmytro-hovenko.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($teamMembers as $member) {
            TeamMember::updateOrCreate(
                ['email' => $member['email']],
                $member
            );
        }
    }
}
