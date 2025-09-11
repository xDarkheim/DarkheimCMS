<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationData;

class OrganizationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Departments
        $departments = [
            ['key' => 'engineering', 'label' => 'Engineering', 'order' => 1],
            ['key' => 'design', 'label' => 'Design', 'order' => 2],
            ['key' => 'marketing', 'label' => 'Marketing', 'order' => 3],
            ['key' => 'sales', 'label' => 'Sales', 'order' => 4],
            ['key' => 'hr', 'label' => 'Human Resources', 'order' => 5],
            ['key' => 'finance', 'label' => 'Finance', 'order' => 6],
            ['key' => 'operations', 'label' => 'Operations', 'order' => 7],
            ['key' => 'customer-support', 'label' => 'Customer Support', 'order' => 8],
        ];

        foreach ($departments as $dept) {
            OrganizationData::updateOrCreate(
                ['type' => 'department', 'key' => $dept['key']],
                [
                    'value' => $dept['key'],
                    'label' => $dept['label'],
                    'order' => $dept['order'],
                    'is_active' => true
                ]
            );
        }

        // Positions
        $positions = [
            ['key' => 'frontend-developer', 'label' => 'Frontend Developer', 'order' => 1],
            ['key' => 'backend-developer', 'label' => 'Backend Developer', 'order' => 2],
            ['key' => 'fullstack-developer', 'label' => 'Full Stack Developer', 'order' => 3],
            ['key' => 'ui-ux-designer', 'label' => 'UI/UX Designer', 'order' => 4],
            ['key' => 'product-manager', 'label' => 'Product Manager', 'order' => 5],
            ['key' => 'marketing-manager', 'label' => 'Marketing Manager', 'order' => 6],
            ['key' => 'sales-representative', 'label' => 'Sales Representative', 'order' => 7],
            ['key' => 'hr-specialist', 'label' => 'HR Specialist', 'order' => 8],
            ['key' => 'devops-engineer', 'label' => 'DevOps Engineer', 'order' => 9],
            ['key' => 'data-analyst', 'label' => 'Data Analyst', 'order' => 10],
            ['key' => 'project-manager', 'label' => 'Project Manager', 'order' => 11],
            ['key' => 'team-lead', 'label' => 'Team Lead', 'order' => 12],
            ['key' => 'cto', 'label' => 'Chief Technology Officer', 'order' => 13],
            ['key' => 'ceo', 'label' => 'Chief Executive Officer', 'order' => 14],
        ];

        foreach ($positions as $pos) {
            OrganizationData::updateOrCreate(
                ['type' => 'position', 'key' => $pos['key']],
                [
                    'value' => $pos['key'],
                    'label' => $pos['label'],
                    'order' => $pos['order'],
                    'is_active' => true
                ]
            );
        }

        // Skills
        $skills = [
            ['key' => 'javascript', 'label' => 'JavaScript', 'order' => 1],
            ['key' => 'typescript', 'label' => 'TypeScript', 'order' => 2],
            ['key' => 'php', 'label' => 'PHP', 'order' => 3],
            ['key' => 'laravel', 'label' => 'Laravel', 'order' => 4],
            ['key' => 'vuejs', 'label' => 'Vue.js', 'order' => 5],
            ['key' => 'react', 'label' => 'React', 'order' => 6],
            ['key' => 'nodejs', 'label' => 'Node.js', 'order' => 7],
            ['key' => 'python', 'label' => 'Python', 'order' => 8],
            ['key' => 'mysql', 'label' => 'MySQL', 'order' => 9],
            ['key' => 'postgresql', 'label' => 'PostgreSQL', 'order' => 10],
            ['key' => 'mongodb', 'label' => 'MongoDB', 'order' => 11],
            ['key' => 'git', 'label' => 'Git', 'order' => 12],
            ['key' => 'docker', 'label' => 'Docker', 'order' => 13],
            ['key' => 'aws', 'label' => 'AWS', 'order' => 14],
            ['key' => 'html', 'label' => 'HTML', 'order' => 15],
            ['key' => 'css', 'label' => 'CSS', 'order' => 16],
            ['key' => 'figma', 'label' => 'Figma', 'order' => 17],
            ['key' => 'photoshop', 'label' => 'Photoshop', 'order' => 18],
            ['key' => 'communication', 'label' => 'Communication', 'order' => 19],
            ['key' => 'teamwork', 'label' => 'Team Leadership', 'order' => 20],
            ['key' => 'problem-solving', 'label' => 'Problem Solving', 'order' => 21],
            ['key' => 'project-management', 'label' => 'Project Management', 'order' => 22],
        ];

        foreach ($skills as $skill) {
            OrganizationData::updateOrCreate(
                ['type' => 'skill', 'key' => $skill['key']],
                [
                    'value' => $skill['key'],
                    'label' => $skill['label'],
                    'order' => $skill['order'],
                    'is_active' => true
                ]
            );
        }

        // Employment Types
        $employmentTypes = [
            ['key' => 'full-time', 'label' => 'Full Time', 'order' => 1],
            ['key' => 'part-time', 'label' => 'Part Time', 'order' => 2],
            ['key' => 'contract', 'label' => 'Contract', 'order' => 3],
            ['key' => 'internship', 'label' => 'Internship', 'order' => 4],
        ];

        foreach ($employmentTypes as $type) {
            OrganizationData::updateOrCreate(
                ['type' => 'employment_type', 'key' => $type['key']],
                [
                    'value' => $type['key'],
                    'label' => $type['label'],
                    'order' => $type['order'],
                    'is_active' => true
                ]
            );
        }

        // Experience Levels
        $experienceLevels = [
            ['key' => 'entry', 'label' => 'Entry Level', 'order' => 1],
            ['key' => 'junior', 'label' => 'Junior', 'order' => 2],
            ['key' => 'mid', 'label' => 'Mid Level', 'order' => 3],
            ['key' => 'senior', 'label' => 'Senior', 'order' => 4],
            ['key' => 'lead', 'label' => 'Lead', 'order' => 5],
            ['key' => 'principal', 'label' => 'Principal', 'order' => 6],
        ];

        foreach ($experienceLevels as $level) {
            OrganizationData::updateOrCreate(
                ['type' => 'experience_level', 'key' => $level['key']],
                [
                    'value' => $level['key'],
                    'label' => $level['label'],
                    'order' => $level['order'],
                    'is_active' => true
                ]
            );
        }

        // Locations
        $locations = [
            ['key' => 'remote', 'label' => 'Remote', 'order' => 1],
            ['key' => 'office', 'label' => 'Office', 'order' => 2],
            ['key' => 'hybrid', 'label' => 'Hybrid', 'order' => 3],
        ];

        foreach ($locations as $location) {
            OrganizationData::updateOrCreate(
                ['type' => 'location', 'key' => $location['key']],
                [
                    'value' => $location['key'],
                    'label' => $location['label'],
                    'order' => $location['order'],
                    'is_active' => true
                ]
            );
        }

        // Statuses
        $statuses = [
            ['key' => 'active', 'label' => 'Active', 'order' => 1],
            ['key' => 'inactive', 'label' => 'Inactive', 'order' => 2],
            ['key' => 'on-leave', 'label' => 'On Leave', 'order' => 3],
        ];

        foreach ($statuses as $status) {
            OrganizationData::updateOrCreate(
                ['type' => 'status', 'key' => $status['key']],
                [
                    'value' => $status['key'],
                    'label' => $status['label'],
                    'order' => $status['order'],
                    'is_active' => true
                ]
            );
        }
    }
}
