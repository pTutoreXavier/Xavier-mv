<?php

namespace App\auth;

use App\Models\User;

class Auth
{
    public function user()
    {
        if(isset($_SESSION['user']))
        return User::find($_SESSION['user']);
    }

    public function check()
    {
        if(isset($_SESSION['user']))
        return isset($_SESSION['user']);
    }

    public function checkLevel(){
        if(!isset($_SESSION['user']))
            return false;

        $user = User::select('level')->where('id','=',$_SESSION['user'])->first();
        if($user->level === 500){
            return 'user';
        }else if ($user->level === 779) {
            return 'searcher';
        } else {
            return false;
        }
    }

    public function attempt($mail, $mdp)
    {
        $user = User::where('mail',$mail)->first();
        if(!$user) {
            return false;
        }
        if (password_verify($mdp, $user->mdp)) {
            $_SESSION['user'] = $user->id;
            return true;
        }
        return false;

    }

    public function logout ()
    {
        unset($_SESSION['user']);
    }


}