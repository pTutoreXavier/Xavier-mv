<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\User;

class MailAvailable extends AbstractRule
{
    public function validate($input)
    {
        return User::where('mail', $input)->count() === 0;
    }
}