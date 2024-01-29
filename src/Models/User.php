<?php

namespace Sergmoro1\Imageable\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Sergmoro1\Imageable\Traits\HasStorage;
use Sergmoro1\Imageable\Traits\HasImages;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasStorage, HasImages;
    
    protected static function newFactory()
    {
        return \Sergmoro1\Imageable\Database\Factories\UserFactory::new();
    }
}