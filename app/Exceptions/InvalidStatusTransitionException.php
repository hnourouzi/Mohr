<?php

namespace App\Exceptions;

use Exception;
use App\Enums\ContentStatus;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(ContentStatus $from, ContentStatus $to)
    {
        parent::__construct("انتقال وضعیت از «{$from->label()}» به «{$to->label()}» مجاز نیست.");
    }
}