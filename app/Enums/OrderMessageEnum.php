<?php

namespace App\Enums;

enum OrderMessageEnum: string
{
    case SUCCESS_MESSAGE = 'Successfully created';
    case FAILED_MESSAGE = 'Something went wrong please try again later';
}
