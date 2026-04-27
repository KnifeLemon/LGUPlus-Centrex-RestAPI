<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    // 규격서 6.2 DATAS 구조 기반
    private function fullItem(): array
    {
        return [
            'NO'         => '1',
            'NAME'       => '홍길동',
            'PHONE1'     => '0212341234',
            'PHONE2'     => '01012341234',
            'GROUP_NAME' => '영업팀',
            'GROUP_CODE' => 'SALES',
            'ETC'        => '비고내용',
        ];
    }

    public function testFromArrayFull(): void
    {
        $c = Contact::fromArray($this->fullItem());

        $this->assertSame('1', $c->no);
        $this->assertSame('홍길동', $c->name);
        $this->assertSame('0212341234', $c->phone1);
        $this->assertSame('01012341234', $c->phone2);
        $this->assertSame('영업팀', $c->groupName);
        $this->assertSame('SALES', $c->groupCode);
        $this->assertSame('비고내용', $c->etc);
    }

    public function testOptionalFieldsNullWhenAbsent(): void
    {
        $c = Contact::fromArray([
            'NO'    => '2',
            'NAME'  => '김철수',
            'PHONE1' => '0312341234',
        ]);

        $this->assertNull($c->phone2);
        $this->assertNull($c->groupName);
        $this->assertNull($c->groupCode);
        $this->assertNull($c->etc);
    }

    public function testOptionalFieldsNullWhenEmptyString(): void
    {
        $c = Contact::fromArray([
            'NO'         => '3',
            'NAME'       => '박영희',
            'PHONE1'     => '0412341234',
            'PHONE2'     => '',
            'GROUP_NAME' => '',
            'GROUP_CODE' => '',
            'ETC'        => '',
        ]);

        $this->assertNull($c->phone2);
        $this->assertNull($c->groupName);
        $this->assertNull($c->groupCode);
        $this->assertNull($c->etc);
    }

    public function testToArrayOmitsNullFields(): void
    {
        $c = Contact::fromArray([
            'NO'    => '1',
            'NAME'  => '홍길동',
            'PHONE1' => '0212341234',
        ]);

        $arr = $c->toArray();

        $this->assertArrayHasKey('no', $arr);
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('phone1', $arr);
        $this->assertArrayNotHasKey('phone2', $arr);
        $this->assertArrayNotHasKey('groupName', $arr);
    }
}
