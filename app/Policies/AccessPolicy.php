<?php

namespace App\Policies;

use App\Models\Access;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any access.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function before(User $user, $ability)
    {
        // If the user is a super admin, they can do everything
        if ($user->isSuperUser()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view a list of access.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->hasAccess('access.view');
    }

    /**
     * Determine whether the user can view the access.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Access  $access
     * @return bool
     */
    public function view(User $user, Access $access = null)
    {
        return $user->hasAccess('access.view');
    }

    /**
     * Determine whether the user can create accesses.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasAccess('access.create');
    }

    /**
     * Determine whether the user can update the access.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Access  $access
     * @return bool
     */
    public function update(User $user, Access $access)
    {
        return $user->hasAccess('access.edit');
    }

    /**
     * Determine whether the user can delete the access.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Access  $access
     * @return bool
     */
    public function delete(User $user, Access $access)
    {
        return $user->hasAccess('access.delete');
    }

    /**
     * Determine whether the user can checkout the access.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Access  $access
     * @return bool
     */
    public function checkout(User $user, Access $access = null)
    {
        return $user->hasAccess('access.checkout');
    }

    /**
     * Determine whether the user can checkin the access.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Access  $access
     * @return bool
     */
    public function checkin(User $user, Access $access = null)
    {
        return $user->hasAccess('access.checkin');
    }
} 