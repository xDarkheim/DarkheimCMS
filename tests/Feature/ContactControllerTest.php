<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_submit_general_contact_message()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Test Company',
            'service' => 'Web Development',
            'budget' => '10000-25000',
            'message' => 'I need a website for my business.',
            'message_type' => 'general'
        ];

        $response = $this->postJson('/api/contact', $data);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Message sent successfully']);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message_type' => 'general'
        ]);
    }

    #[Test]
    public function it_can_submit_job_application()
    {
        Storage::fake('public');
        $resumeFile = UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf');

        $data = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1987654321',
            'message' => 'I would like to apply for a position.',
            'message_type' => 'job_application',
            'position_interest' => 'Full Stack Developer',
            'resume_file' => $resumeFile,
            'portfolio_url' => 'https://janesmith.dev',
            'experience_summary' => '5 years of experience in web development',
            'availability' => 'Immediately',
            'salary_expectation' => 70000
        ];

        $response = $this->postJson('/api/contact', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message_type' => 'job_application',
            'position_interest' => 'Full Stack Developer'
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/contact', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    #[Test]
    public function it_validates_email_format()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'message' => 'Test message'
        ];

        $response = $this->postJson('/api/contact', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_validates_resume_file_for_job_applications()
    {
        $invalidFile = UploadedFile::fake()->create('document.txt', 1000);

        $data = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message' => 'Job application',
            'message_type' => 'job_application',
            'resume_file' => $invalidFile
        ];

        $response = $this->postJson('/api/contact', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['resume_file']);
    }
}
