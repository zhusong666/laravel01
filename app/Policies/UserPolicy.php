<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    //第一个User为当前登录用户实例
    //第二个参数则为要进行授权的用户实例
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    public function view(User $currentUser, User $user)
    {
       return $currentUser->id === $user->id;
    }

    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
}












