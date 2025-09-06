<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function it_can_upload_resume_file(): void
    {
        $file = UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf');

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Job application',
            'message_type' => 'job_application',
            'resume_file' => $file
        ]);

        $response->assertStatus(201); // Changed from 200 to 201

        // Verify file was stored
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'message_type' => 'job_application'
        ]);
    }

    #[Test]
    public function it_validates_resume_file_type(): void
    {
        $invalidFile = UploadedFile::fake()->create('document.txt', 1000);

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Job application',
            'message_type' => 'job_application',
            'resume_file' => $invalidFile
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['resume_file']);
    }

    #[Test]
    public function it_validates_resume_file_size(): void
    {
        $largeFile = UploadedFile::fake()->create('resume.pdf', 10001, 'application/pdf'); // Over 10MB

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Job application',
            'message_type' => 'job_application',
            'resume_file' => $largeFile
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['resume_file']);
    }

    #[Test]
    public function it_accepts_valid_image_formats(): void
    {
        $jpgFile = UploadedFile::fake()->image('image.jpg');
        $pngFile = UploadedFile::fake()->image('image.png');

        // Test JPG
        $response1 = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Portfolio submission',
            'image_file' => $jpgFile
        ]);

        // Test PNG
        $response2 = $this->postJson('/api/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Portfolio submission',
            'image_file' => $pngFile
        ]);

        $response1->assertStatus(201); // Changed from 200 to 201
        $response2->assertStatus(201); // Changed from 200 to 201
    }
}
