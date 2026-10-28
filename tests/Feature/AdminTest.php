<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_from_refused_domain(): void
    {
        $user = User::factory()->create(['email' => 'intrus@example.com']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_email_from_accepted_domain(): void
    {
        $user = User::factory()->create(['email' => 'cukilany@gmail.com']);

        $this->actingAs($user)->get('/admin')->assertOk();
    }
}