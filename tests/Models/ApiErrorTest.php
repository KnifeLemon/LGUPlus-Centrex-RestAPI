<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\ApiError;
use PHPUnit\Framework\TestCase;

class ApiErrorTest extends TestCase
{
    public function testFromResponse(): void
    {
        $error = ApiError::fromResponse([
            'SVC_RT'  => '1004',
            'SVC_MSG' => 'AUTH_ERR',
        ]);

        $this->assertSame('1004', $error->code);
        $this->assertSame('AUTH_ERR', $error->message);
    }

    public function testToString(): void
    {
        $error = ApiError::fromResponse([
            'SVC_RT'  => '1004',
            'SVC_MSG' => 'AUTH_ERR',
        ]);

        $this->assertSame('[1004] AUTH_ERR', (string) $error);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $error = ApiError::fromResponse([]);

        $this->assertSame('', $error->code);
        $this->assertSame('', $error->message);
    }
}
