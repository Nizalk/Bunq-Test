<?php
// database/migrate.php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Drop existing tables if they exist
Capsule::schema()->dropIfExists('users');
Capsule::schema()->dropIfExists('groups');
Capsule::schema()->dropIfExists('messages');

// Create Users table
Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('username')->unique();
    $table->string('token')->unique();
    $table->timestamps();
});

// Create Groups table
Capsule::schema()->create('groups', function ($table) {
    $table->increments('id');
    $table->string('name')->unique();
    $table->timestamps();
});

// Create Messages table
Capsule::schema()->create('messages', function ($table) {
    $table->increments('id');
    $table->unsignedInteger('group_id');
    $table->unsignedInteger('user_id');
    $table->text('message');
    $table->timestamps();

    $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

echo "Database migrated successfully.\n";
