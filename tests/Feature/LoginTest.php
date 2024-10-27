<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class); //para crear migracion de usuario UserSeeder
    }

    #[Test] //para que se ejecute la funcion es necesario poner Test o una notacion antes de la funcion
    public function an_existing_user_can_login(): void
    {
        $this->withoutExceptionHandling(); //muestra los errores
        #teniendo
        $credentials = ['email' => 'example@example.com', 'password' => 'password'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        //$response->dump();
        //dd($response->json());
        #esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function a_non_existing_user_cannot_login(): void
    {
        #teniendo
        $credentials = ['email' => 'example@noexisting.com', 'password' => 'password'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(401);
        $response->assertJsonFragment(['status' => 401, 'message' => 'Unauthorized']);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        #teniendo
        $credentials = ['password' => 'password'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials); //postjson para que mande cabeceras no lo tome como paginas web direccionadas sino api

        #esperando
        $response->dd();
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        #teniendo
        $credentials = ['email' => 'asdasdaa', 'password' => 'password'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials); //postjson para que mande cabeceras no lo tome como paginas web direccionadas sino api


        #esperando
        //$response->dd();
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_a_string(): void
    {
        #teniendo
        $credentials = ['email' => 123456, 'password' => 'password'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials); //postjson para que mande cabeceras no lo tome como paginas web direccionadas sino api
       // $response->dd();

        #esperando
        
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        #teniendo
        $credentials = ['email' => 'example@noexisting.com'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_have_at_lease_8_characters(): void
    {
        #teniendo
        $credentials = ['email' => 'example@example.com', 'password' => 'abc'];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);
        //$response->dd();

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }
}
