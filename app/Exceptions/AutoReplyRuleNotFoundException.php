<?php

namespace App\Exceptions;

use Exception;

class AutoReplyRuleNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('No active auto-reply rule is available.');
    }
}