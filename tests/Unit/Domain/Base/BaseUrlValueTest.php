<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Base;

use Domain\Base\BaseUrlValue;
use Domain\Exception\InvalidArgumentException;
use Tests\TestCase;

class BaseUrlValueTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testConstruct()
    {
        $urlValueInstance = new class ('https://nw.fyui001.com') extends BaseUrlValue {};
        $this->assertInstanceOf(BaseUrlValue::class, $urlValueInstance);

        $this->expectException(InvalidArgumentException::class);
        new class ('高田憂希') extends BaseUrlValue {};
    }
}
