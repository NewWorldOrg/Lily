<?php

declare(strict_types=1);

namespace App\Services\Shared;

enum ServiceError: string
{
    case LoginFailure = 'login_failure';
    case DuplicateEntry = 'duplicate_entry';
    case InvalidEmailAddress = 'invalid_email_address';
    case InvalidPassword = 'invalid_password';
    case InvalidToken = 'invalid_token';
    case InvalidFile = 'invalid_file';
    case InvalidArgument = 'invalid_argument';

    case Forbidden = 'forbidden';
    case Unauthorized = 'unauthorized';
    case NotFound = 'notfound';
    case InternalServerError = 'internal_server_error';
    case FailedToDelete = 'failed_to_delete';
    case FailedToUpdate = 'failed_to_update';

    case UsedItem = 'used_item';
    case SqlError = 'sql_error';
    case Unknown = 'unknown';
    case SameEmailExists = 'same_email_exists';
}
