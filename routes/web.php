<?php

use Livewire\Volt\Volt;

Volt::route('/', 'index')->name('home');                          // Home 
Volt::route('/users', 'users.index')->name('users');               // User (list) 
Volt::route('/users/create', 'users.create')->name('user.create');       // User (create) 
Volt::route('/users/{user}/edit', 'users.edit')->name('user.edit');    // User (edit) 