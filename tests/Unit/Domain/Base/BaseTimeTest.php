<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Base;

use Domain\Base\BaseTime;
use Domain\Exception\InvalidArgumentException;
use Tests\TestCase;

class BaseTimeTest extends TestCase
{
    public function testConstruct()
    {
        $baseTimeInstance = new class (time()) extends BaseTime {};
        $this->assertInstanceOf(BaseTime::class, $baseTimeInstance);
    }

    public function testForStringTime()
    {
        $stringTime = '1993-03-16';
        $time = strtotime($stringTime);
        $timeInstance = BaseTimeForTest::forStringTime($stringTime);
        $this->assertEquals(
            $time,
            $timeInstance->getRawValue(),
        );

        $this->expectException(InvalidArgumentException::class);
        BaseTimeForTest::forStringTime('高田憂希');
    }

    public function testGetDisplay()
    {
        $time = new class (strtotime('1993-03-16')) extends BaseTime {};
        $this->assertEquals(
            '1993/03/16',
            $time->getDisplay(),
        );
    }

    public function testGetDetail()
    {
        $time = new class (strtotime('1993-03-16')) extends BaseTime {};
        $this->assertEquals(
            '1993/03/16 00:00:00',
            $time->getDetail(),
        );
    }

    public function testGetSqlDate()
    {
        $time = new class (strtotime('1993/03/16')) extends BaseTime {};
        $this->assertEquals(
            '1993-03-16',
            $time->getSqlDate(),
        );
    }

    public function testGetSqlTimeStamp()
    {
        $time = new class (strtotime('1993/03/16')) extends BaseTime {};
        $this->assertEquals(
            '1993-03-16 00:00:00',
            $time->getSqlTimeStamp(),
        );
    }
}

class BaseTimeForTest extends BaseTime
{
}
