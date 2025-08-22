<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUsers extends Command
{
    protected $signature = 'check:users';
    protected $description = 'Check users in database';

    public function handle()
    {
        $users = User::all(['id', 'nik', 'name', 'role']);

        $this->info("Total users: " . $users->count());

        foreach ($users as $user) {
            $this->line("ID: {$user->id}, NIK: {$user->nik}, Name: {$user->name}, Role: {$user->role}");
        }

        // Check if user with NIK 1234567890 exists
        $problematicUser = User::where('nik', '1234567890')->first();
        if ($problematicUser) {
            $this->info("Found user with NIK 1234567890:");
            $this->line("- ID: {$problematicUser->id}");
            $this->line("- Name: {$problematicUser->name}");
            $this->line("- Role: {$problematicUser->role}");
        } else {
            $this->error("User with NIK 1234567890 not found!");
        }

        return 0;
    }
}
