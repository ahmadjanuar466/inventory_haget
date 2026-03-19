<?php

namespace App\Services\Usermanagement;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Arr;

class UserProfileImpl implements UserProfileServices
{
    public function createProfile(array $attributes): UserProfile
    {
        return UserProfile::create($this->filterAttributes($attributes));
    }

    public function updateProfile(UserProfile $profile, array $attributes): UserProfile
    {
        $profile->fill($this->filterAttributes($attributes));
        $profile->save();

        return $profile->refresh();
    }

    public function attachUser(UserProfile $profile, User $user): UserProfile
    {
        $profile->users_id = $user->id;
        $profile->save();

        return $profile->refresh();
    }

    /**
     * Keep only the attributes that belong to the profile table.
     */
    protected function filterAttributes(array $attributes): array
    {
        return Arr::only($attributes, [
            'nama_lengkap',
            'email',
            'alamat',
            'no_telp',
            'avatars',
            'birth_date',
            'users_id',
        ]);
    }
}
