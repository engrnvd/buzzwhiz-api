<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private static int $numSeedersRan = 0;

    public function run(): void
    {
        $this->call(NewsCategoriesSeeder::class);
        $this->call(SaveAuthorsSeeder::class);

        if (!static::$numSeedersRan) {
            $this->command->info("Nothing to seed.");
        }
    }

    public function call($class, $silent = false, $parameters = []): void
    {
        // check if the seeder already ran
        $seeder = DB::table('seeders')->where('class', $class)->first();
        if ($seeder) {
            return;
        }

        // run the seeder
        parent::call($class, $silent);

        // add the seeder to the seeded list
        DB::table('seeders')->insert([
            'class' => $class,
            'ran_at' => date('Y-m-d H:i:s'),
        ]);

        static::$numSeedersRan++;
    }
}
