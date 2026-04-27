<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\ChannelStatus;
use PHPUnit\Framework\TestCase;

class ChannelStatusTest extends TestCase
{
    public function testFromArray(): void
    {
        $cs = ChannelStatus::fromArray(['STATUS' => 'SIP/10013010-aaba']);

        $this->assertSame('SIP/10013010-aaba', $cs->status);
    }

    public function testToArray(): void
    {
        $cs = ChannelStatus::fromArray(['STATUS' => 'SIP/10013010-aaba']);

        $this->assertSame(['status' => 'SIP/10013010-aaba'], $cs->toArray());
    }

    public function testMissingStatusDefaultsToEmpty(): void
    {
        $cs = ChannelStatus::fromArray([]);
        $this->assertSame('', $cs->status);
    }
}
