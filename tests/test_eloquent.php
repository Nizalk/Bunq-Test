<?php
// test_eloquent.php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

use Elkha\ChatBackend\Models\User;

// Create a new user
$user = User::create([
    'username' => 'testuser',
    'token' => bin2hex(random_bytes(16)),
]);

echo "User created: " . $user->username . "\n";

// Fetch the user
$foundUser = User::find($user->id);
echo "User fetched: " . $foundUser->username . "\n";

// Update the user
$foundUser->username = 'updateduser';
$foundUser->save();
echo "User updated: " . $foundUser->username . "\n";

// Delete the user
$foundUser->delete();
echo "User deleted.\n";
