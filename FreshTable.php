<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\search;

class FreshTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elegis:fresh-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows you to select a specific migration to refresh.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrations = DB::table('migrations')->pluck('migration');

        if ($migrations->isEmpty()) {
            $this->error('No migrations found in the database.');
            return;
        }


        $selectedMigration = search(
            label: 'Type the migration name you want to refresh.',
            options: fn (string $value) => strlen($value) > 0
                ? $migrations
                    ->filter(fn ($migration) => stripos($migration, $value) !== false)
                    ->values()
                    ->toArray()
                : $migrations->values()->toArray()
        );


        if (!$selectedMigration) {
            $this->info('No migration selected. Operation canceled.');
            return;
        }


        if (!$this->confirm("Are you sure you want to refresh the migration: $selectedMigration?")) {
            $this->info('Operation canceled.');
            return;
        }


        $tableName = $this->extractTableName($selectedMigration);

        if (!$tableName) {
            $this->error('It was not possible to determine the table corresponding to this migration.');
            return;
        }

        $this->info("Disable foreign key check...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (Schema::hasTable($tableName)) {
            $this->info("Deleting table: $tableName...");
            Schema::drop($tableName);
        } else {
            $this->warn("The table $tableName does not exist in the database.");
        }

        $this->info("Removing the migration from the database...");
        DB::table('migrations')->where('migration', $selectedMigration)->delete();

        $this->info("Running php artisan migrate...");
        $this->call('migrate');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Migration applied successfully.');
    }

    private function extractTableName($migration)
    {
        if (preg_match('/create_(.*?)_table/', $migration, $matches)) {
            return $matches[1];
        }


        return null;
    }
}
