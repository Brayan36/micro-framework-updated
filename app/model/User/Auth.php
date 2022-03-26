<?php

namespace App\Model\User;

class Auth extends User
{

    public function attempt($data = null): ?array
    {
        if (is_null($data)) {
            return null;
        }
        $userName = $data['username'];
        $password = $data['password'];

        $user = $this->select(['*'])->where(['username', '=', $userName])->limit(1)->getAll();

        $inactive = 3600; // lifetime session 2 hours in seconds
        ini_set('session.gc_maxlifetime', $inactive);
        session_start();

        if ($user) {
            $user = $user[0];
            if (password_verify($password, $user['password'])) {

                unset($user['password'], );
                $_SESSION['user'] = $user;
                $_SESSION['lifetime'] = time(); // Update session
                $_SESSION['time'] = $inactive;
                return $user;
            }
        }
        session_unset();     // unset $_SESSION variable
        session_destroy();   // destroy session data
        return null;
    }

}