<?php

namespace App\Exceptions;

use RuntimeException;

class ConcurrencyException extends RuntimeException
{
    protected string $entityType;
    protected $entityId;

    public function __construct(string $entityType = 'record', $entityId = null, string $message = '')
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;

        if ($message === '') {
            $message = "This {$entityType} has been modified by another user. Please refresh and try again.";
        }

        parent::__construct($message, 409);
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }
}
