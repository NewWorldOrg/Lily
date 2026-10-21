<?php

declare(strict_types=1);

namespace Domain\Base;

use Domain\Common\RawInteger;
use Domain\Common\RawString;

interface BaseEnum
{
    public function displayName(): BaseValue;
    public function getValue(): RawInteger|RawString;
}
