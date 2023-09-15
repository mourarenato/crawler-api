<?php

namespace Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testSignupWithSuccess()
    {
        $email = $this->faker->unique()->safeEmail();
        $password = 'usertest';

        $response = $this->json('POST', 'api/signup', [
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function testSignupWhenUserAlreadyExists()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/signup', [
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'success' => false,
                'message' => 'User already exists!',
            ]);
    }

    public function testSignupWhenExceptionIsThrown()
    {
        $response = $this->json('POST', 'api/signup', [
            'email' => 'teste@email.com',
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'success' => false,
                'message' => 'User not created. Invalid data',
            ]);
    }

    public function testSigninWithSuccess()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/signin', [
            'email' => $user->email,
            'password' => 'usertest',
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testSigninWhenCredentialsAreInvalids()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/signin', [
            'email' => $user->email,
            'password' => 'testapi'
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'success' => false,
                'message' => 'Login credentials are invalid',
            ]);
    }
}
