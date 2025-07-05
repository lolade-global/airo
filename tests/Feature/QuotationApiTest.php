<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuotationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_generate_quotation()
    {
        $user = User::factory()->create();

        $payload = [
            'age' => '28,35',
            'currency_id' => 'EUR',
            'start_date' => '2020-10-01',
            'end_date' => '2020-10-30',
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/quotation', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'quotation_id',
                         'total',
                         'currency_id',
                     ]
                 ]);
    }

    public function test_unauthenticated_user_cannot_access_quotation()
    {
        $response = $this->postJson('/api/quotation', []);

        $response->assertStatus(401);
    }

    public function test_it_returns_validation_error_for_missing_fields()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/quotation', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['age', 'currency_id', 'start_date', 'end_date']);
    }

    public function test_it_returns_error_for_invalid_currency()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/quotation', [
            'age' => '28',
            'currency_id' => 'NGN', // invalid
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-10',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_it_fails_for_end_date_before_start_date()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/quotation', [
            'age' => '28',
            'currency_id' => 'EUR',
            'start_date' => '2024-01-10',
            'end_date' => '2024-01-01', // backwards
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_it_throws_error_for_unsupported_age()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/quotation', [
            'age' => '17,72', // both outside supported range
            'currency_id' => 'USD',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-05',
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Server error',
            ]);
    }
}
