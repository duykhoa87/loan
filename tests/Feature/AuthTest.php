<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->name = 'Test';
        $this->email = 'test@domain.com';
        $this->password = '123456789';
        $this->id = null;
    }
    
    public function test_register_user()
    {
        $response = $this->postJson('/api/user/register', [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        ]);
        
        $response->assertOk();
    }
    
    public function test_register_duplicated_user()
    {
        $response = $this->postJson('/api/user/register', [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        ]);
        
        $response->assertOk()->assertJson(['email' => ['The email has already been taken.']]);
    }
    
    public function test_generate_token()
    {
        $response = $this->postJson('/api/user/token', [
            'email' => $this->email,
            'password' => $this->password
        ]);
    
        $response->assertOk();
    }
    
    public function test_user_can_make_loan()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        
        $response = $this->postJson('/api/loan/store', [
            'amount' => -1,
            'loan_term' => 12
        ]);
    
        $response->assertOk()->assertJson(['amount' => ['The amount must be greater than 0.']]);
    
        $response = $this->postJson('/api/loan/store', [
            'amount' => null,
            'loan_term' => 12
        ]);

        $response->assertOk()->assertJson(['amount' => ['The amount field is required.']]);

        $response = $this->postJson('/api/loan/store', [
            'amount' => 1000000,
            'loan_term' => 12
        ]);

        $response->assertOk();
        $loan = json_decode($response->getContent());
    }
    
    public function test_approve_loan()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->postJson('/api/loan/store', [
            'amount' => 1000000,
            'loan_term' => 12
        ]);
        $loan = json_decode($response->getContent());
        
        $response = $this->postJson('/api/loan/approve', [
            'id' => $loan->data->id,
        ]);
    
        $response->assertOk();
    }
    
    public function test_unauthorized()
    {
        $response = $this->postJson('/api/loan/store', [
            'amount' => 1000000,
            'loan_term' => 12
        ]);
        $response->assertUnauthorized();
    }
}
