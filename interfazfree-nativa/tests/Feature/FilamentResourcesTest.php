<?php

namespace Tests\Feature;

use App\Models\Perfil;
use App\Models\Nas;
use App\Models\Lote;
use App\Models\Ficha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentResourcesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\PerfilSeeder::class);
        $this->seed(\Database\Seeders\AtributoSeeder::class);
    }

    public function test_can_create_nas()
    {
        $nas = Nas::factory()->create();
        
        $this->assertDatabaseHas('nas', [
            'id' => $nas->id,
            'nombre' => $nas->nombre,
        ]);
    }

    public function test_can_create_lote()
    {
        $perfil = Perfil::first();
        $nas = Nas::factory()->create();

        $lote = Lote::create([
            'nombre' => 'Test Lote',
            'cantidad' => 10,
            'longitud_password' => 8,
            'tipo_password' => 'alfanumerico',
            'perfil_id' => $perfil->id,
            'nas_id' => $nas->id,
        ]);
        
        $this->assertDatabaseHas('lotes', [
            'nombre' => 'Test Lote',
            'cantidad' => 10,
        ]);
    }

    public function test_can_create_ficha()
    {
        $perfil = Perfil::first();

        $ficha = Ficha::create([
            'username' => 'testuser',
            'password' => 'testpass',
            'estado' => 'sin_usar',
            'perfil_id' => $perfil->id,
        ]);
        
        $this->assertDatabaseHas('fichas', [
            'username' => 'testuser',
            'estado' => 'sin_usar',
        ]);
    }

    public function test_ficha_has_perfil_relationship()
    {
        $perfil = Perfil::first();
        $ficha = Ficha::factory()->create(['perfil_id' => $perfil->id]);
        
        $this->assertInstanceOf(Perfil::class, $ficha->perfil);
        $this->assertEquals($perfil->id, $ficha->perfil->id);
    }

    public function test_lote_has_perfil_and_nas_relationships()
    {
        $perfil = Perfil::first();
        $nas = Nas::factory()->create();
        $lote = Lote::factory()->create([
            'perfil_id' => $perfil->id,
            'nas_id' => $nas->id,
        ]);
        
        $this->assertInstanceOf(Perfil::class, $lote->perfil);
        $this->assertInstanceOf(Nas::class, $lote->nas);
        $this->assertEquals($perfil->id, $lote->perfil->id);
        $this->assertEquals($nas->id, $lote->nas->id);
    }

    public function test_perfil_seeder_creates_default_profiles()
    {
        $this->assertDatabaseHas('perfils', ['nombre' => 'Corrido', 'tipo' => 'corrido']);
        $this->assertDatabaseHas('perfils', ['nombre' => 'Pausado', 'tipo' => 'pausado']);
        $this->assertDatabaseHas('perfils', ['nombre' => 'Recurrente', 'tipo' => 'recurrente']);
    }

    public function test_atributo_seeder_creates_default_attributes()
    {
        $this->assertDatabaseHas('atributos', ['nombre' => 'Fall-Through']);
        $this->assertDatabaseHas('atributos', ['nombre' => 'Simultaneous-Use']);
        $this->assertDatabaseHas('atributos', ['nombre' => 'Access-Period']);
        $this->assertDatabaseHas('atributos', ['nombre' => 'Max-All-Session']);
    }
}
