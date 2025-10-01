<?php

namespace Tests\Unit;

use App\Models\ContactMessage;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_contact_message(): void
    {
        $message = ContactMessage::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'message' => 'Test message',
            'message_type' => 'general',
            'is_read' => false
        ]);

        $this->assertInstanceOf(ContactMessage::class, $message);
        $this->assertEquals('John Doe', $message->name);
        $this->assertEquals('john@example.com', $message->email);
        $this->assertFalse($message->is_read);
    }

    #[Test]
    public function it_can_format_salary_expectation(): void
    {
        $message = ContactMessage::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'salary_expectation' => 50000.00
        ]);

        $this->assertEquals('$50,000', $message->formatted_salary_expectation);
    }

    #[Test]
    public function it_returns_null_for_empty_salary_expectation(): void
    {
        $message = ContactMessage::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'salary_expectation' => null
        ]);

        $this->assertNull($message->formatted_salary_expectation);
    }

    #[Test]
    public function it_can_identify_job_applications(): void
    {
        $jobApplication = ContactMessage::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'message_type' => 'job_application'
        ]);

        $generalMessage = ContactMessage::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Test message',
            'message_type' => 'general'
        ]);

        $this->assertTrue($jobApplication->is_job_application);
        $this->assertFalse($generalMessage->is_job_application);
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $message = ContactMessage::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'is_read' => 1,
            'salary_expectation' => '50000.50'
        ]);

        $this->assertTrue($message->is_read);
        $this->assertEquals(50000.50, $message->salary_expectation);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $message->created_at);
    }
}
