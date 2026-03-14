<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\HelpdeskArticle;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Helpdesk Agent',
            'email' => 'agent@backend.com',
            'role' => UserRole::AGENT->value,
            'password' => 'agent',
        ]);

        User::factory()->create([
            'name' => 'Helpdesk User',
            'email' => 'user@backend.com',
            'role' => UserRole::USER->value,
            'password' => 'user',
        ]);

        User::factory()
            ->count(10)
            ->hasEvents(3)
            ->create(['role' => UserRole::USER->value]);

        HelpdeskArticle::insert([
            [
                'question' => 'Hogyan hozhatok létre új eseményt?',
                'answer' => 'Új eseményt az „Esemény hozzáadása” gombra kattintva hozhat létre. Adja meg az esemény címét, a tervezett időpontot, valamint opcionálisan egy rövid leírást. Az esemény mentése után az megjelenik az eseménylistában.',
            ],
            [
                'question' => 'Módosíthatom egy meglévő esemény adatait?',
                'answer' => 'Igen. Az eseménylistában válassza ki a módosítani kívánt eseményt, majd kattintson a szerkesztés gombra. Itt megváltoztathatja a címet, az időpontot vagy a leírást. A módosítások mentése után az adatok azonnal frissülnek.',
            ],
            [
                'question' => 'Mi történik, ha törlök egy eseményt?',
                'answer' => 'Egy esemény törlése végleges művelet. A törlés után az esemény eltűnik az eseménylistából, és később már nem állítható vissza. Ha bizonytalan, érdemes inkább az esemény időpontját vagy leírását módosítani.',
            ],
            [
                'question' => 'Miért nem látom az eseményemet az eseménylistában?',
                'answer' => 'Előfordulhat, hogy az esemény időpontja a múltban van, vagy a lista rendezése miatt nem azonnal látható. Ellenőrizze a lista rendezési beállításait, illetve győződjön meg arról, hogy az esemény mentése sikeresen megtörtént.',
            ],
            [
                'question' => 'Milyen adatokat kell megadni egy eseményhez?',
                'answer' => 'Egy esemény létrehozásához kötelező megadni a címet és az időpontot. A leírás mező opcionális, de hasznos lehet, ha részletesebb információt szeretne rögzíteni az eseményről.',
            ],
            [
                'question' => 'Több eseményt is létrehozhatok ugyanarra a napra?',
                'answer' => 'Igen, a rendszer nem korlátozza, hogy egy adott napra hány eseményt hozhat létre. Ha több esemény van ugyanarra az időpontra, azok egymás alatt jelennek meg az eseménylistában.',
            ],
        ]);
    }
}
