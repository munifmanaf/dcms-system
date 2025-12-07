<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;

class ImagePolicy
{
    public function view(User $user, Image $image): bool
    {
        return $user->id === $image->user_id || $user->hasRole('admin');
    }
    
    public function update(User $user, Image $image): bool
    {
        return $user->id === $image->user_id || $user->hasRole('admin');
    }
    
    public function delete(User $user, Image $image): bool
    {
        return $user->id === $image->user_id || $user->hasRole('admin');
    }
}