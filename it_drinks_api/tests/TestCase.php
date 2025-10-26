<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Limpia completamente la base sin usar VACUUM ni DROP manuales
        $this->artisan('migrate:fresh', ['--seed' => false, '--force' => true]);

        //  Asegura Passport listo
        $this->ensurePassportKeysExist();
        $this->ensurePersonalAccessClientExists();
    }


    protected function ensurePassportKeysExist(): void
    {
        // Crea las claves si no existen
        if (!file_exists(storage_path('oauth-private.key')) || !file_exists(storage_path('oauth-public.key'))) {
            Artisan::call('passport:keys', ['--force' => true]);
        }
    }

    protected function ensurePersonalAccessClientExists(): void
    {
        // Comprueba si ya hay un cliente personal
        if (DB::table('oauth_clients')->where('personal_access_client', true)->count() === 0) {
            Artisan::call('passport:client', [
                '--personal' => true,
                '--name' => 'Testing Personal Access Client',
                '--no-interaction' => true,
            ]);
        }
    }
}
