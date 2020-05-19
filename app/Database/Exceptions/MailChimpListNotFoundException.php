<?php
declare(strict_types=1);

namespace App\Database\Exceptions;

use App\Exceptions\AbstractApiException;

class MailChimpListNotFoundException extends AbstractApiException
{
    public static function notFoundInDatabase(string $listId): self
    {
        return new self('list #' . $listId . ' not found in DB');
    }
}
