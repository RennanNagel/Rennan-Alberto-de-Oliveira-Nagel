<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var \App\Models\Client $user */
        $user = Client::factory()->create([
            'is_active' => 1, // garante acesso às rotas protegidas por 'active'
        ]);

        // autentica no guard 'web'
        $this->actingAs($user, 'web');
    }

    public function test_cria_cliente_valido(): void
    {
        $resp = $this->post('/clients', [
            'name'     => 'Teste User',
            'email'    => 'user@example.com',
            'phone'    => '555-0101',
            'password' => '12345678',
            // se o captcha estiver como nullable em testing, esta linha é irrelevante;
            // se estiver required, vai falhar (por isso recomendamos o nullable em testing)
            'g-recaptcha-response' => 'test',
        ]);

        $resp->assertRedirect('/clients');
        $this->assertDatabaseHas('clients', ['email' => 'user@example.com']);
    }

    public function test_nao_permite_email_duplicado(): void
    {
        Client::factory()->create(['email' => 'a@a.com']);

        $resp = $this->post('/clients', [
            'name'     => 'Outro',
            'email'    => 'a@a.com',
            'password' => '12345678',
        ]);

        $resp->assertSessionHasErrors('email');
    }

    public function test_lista_paginada_filtrada(): void
    {
        // cria bastante dado pra paginação
        Client::factory(35)->create();

        $resp = $this->get('/clients?per_page=20&q=a&sort=name&dir=asc');

        $resp->assertOk()->assertSee('Clientes');
    }

    public function test_toggle_ativa_e_desativa_cliente(): void
    {
        /** @var \App\Models\Client $c */
        $c = Client::factory()->create(['is_active' => true]);

        $resp = $this->patch("/clients/{$c->id}/toggle");
        $resp->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'id' => $c->id,
            'is_active' => 0,
        ]);

        // toggla de novo para voltar a ativo
        $resp2 = $this->patch("/clients/{$c->id}/toggle");
        $resp2->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'id' => $c->id,
            'is_active' => 1,
        ]);
    }

    public function test_delecao_em_massa(): void
    {
        $c1 = Client::factory()->create();
        $c2 = Client::factory()->create();

        $resp = $this->delete('/clients', [
            'ids' => [$c1->id, $c2->id],
        ]);

        $resp->assertRedirect('/clients');

        $this->assertDatabaseMissing('clients', ['id' => $c1->id]);
        $this->assertDatabaseMissing('clients', ['id' => $c2->id]);
    }
}
