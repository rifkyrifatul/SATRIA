<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Admin.Level.{level}', function ($user, $level) {
    return $user->isAdmin() && str_replace('admin_', '', $user->admin_level) == $level;
});
