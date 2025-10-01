<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeamMember::create([
            'name' => 'Dmytro Hovenko',
            'position' => 'Full-Stack Developer',
            'department' => 'Development',
            'bio' => 'Full-Stack Developer passionate about creating modern web applications and delivering exceptional user experiences. 

Experienced with PHP, Laravel, JavaScript, and modern web technologies. Always eager to learn new technologies and solve challenging problems.

Portfolio: https://hovenko.com',
            'email' => 'dmytro@hovenko.com',
            'avatar' => 'images/hovenko-profile.jpg',
            'skills' => [
                'PHP',
                'Laravel',
                'JavaScript',
                'HTML5',
                'CSS3',
                'MySQL',
                'Microsoft SQL',
                'RESTful APIs',
                'Git',
                'Bootstrap',
                'jQuery',
                'Linux',
                'Web Development',
                'Problem Solving'
            ],
            'social_links' => [
                'website' => 'https://hovenko.com',
                'linkedin' => 'https://www.linkedin.com/in/dmytro-hovenko-69316935a/',
                'github' => 'https://github.com/xdarkheim',
                'email' => 'dmytro@hovenko.com'
            ],
            'status' => 'active',
            'joined_date' => now()->subYear(),
            'priority' => 10,
            'show_on_website' => true
        ]);
    }
}
