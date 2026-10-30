<?php

namespace Database\Seeders;

use App\Models\AvailableDate;
use App\Models\Contractor;
use App\Models\ContractorShift;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitType;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed sample data for local development.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Administratzailea', 'email' => 'admin@example.com']);
        User::factory()->harrera()->create(['name' => 'Harrera', 'email' => 'harrera@example.com']);
        $teachers = User::factory(5)->create();

        foreach (range(1, 10) as $day) {
            AvailableDate::factory()->create(['date' => today()->addWeekdays($day)]);
        }

        $types = VisitType::all();

        // Visitors currently inside the school.
        Visit::factory(3)->approved()->today()->create(['reviewed_by' => $admin->id])
            ->each(function (Visit $visit) use ($teachers, $types): void {
                $visit->update(['host_id' => $teachers->random()->id]);
                $visit->types()->attach($types->random());
                $visit->visitors()->attach(Visitor::factory()->create(), [
                    'checked_in_at' => now()->subMinutes(fake()->numberBetween(5, 120)),
                    'badge_number' => (string) fake()->unique()->numberBetween(1, 50),
                ]);
            });

        // An approved group arriving later today.
        $expectedGroup = Visit::factory()->group(25)->approved()->today()
            ->create(['reviewed_by' => $admin->id, 'host_id' => $teachers->random()->id]);
        $expectedGroup->types()->attach($types->random());
        $expectedGroup->visitors()->attach(Visitor::factory()->create());

        // Pending group requests from the public booking form.
        Visit::factory(4)->group(fake()->numberBetween(10, 30))->create()
            ->each(function (Visit $visit) use ($types): void {
                $visit->types()->attach($types->random(2));
                $visit->visitors()->attach(Visitor::factory()->create());
            });

        Meeting::factory()->create(['date' => today()])
            ->visitors()->attach(Visitor::factory(3)->create());

        ContractorShift::factory()->withKey()->for(Contractor::factory())->create();
        ContractorShift::factory()->checkedOut()->for(Contractor::factory())->create(['checked_in_at' => now()->subHours(4)]);
    }
}
