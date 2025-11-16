<?php

declare(strict_types=1);

namespace Domain\Base;

use Courage\CoInt\CoInteger;
use Domain\Exception\InvalidArgumentException;

abstract class BaseTime extends CoInteger
{
    public function __construct(private int $timestamp)
    {
        parent::__construct($this->timestamp);
    }

    public static function forStringTime(string $stringTime): static
    {
        $time = strtotime($stringTime);
        if (!$time) {
            throw new InvalidArgumentException();
        }

        return new static($time);
    }

    public static function now(): static
    {
        return new static(time());
    }

    public function timeStamp(): int
    {
        return $this->timestamp;
    }

    public function getDisplay(): string
    {
        return date('Y/m/d', $this->timestamp);
    }

    public function getDetail(): string
    {
        return date('Y/m/d H:i:s', $this->timestamp);
    }

    public function getSqlDate(): string
    {
        return date('Y-m-d', $this->timestamp);
    }

    public function getSqlTimeStamp(): string
    {
        return date('Y-m-d H:i:s', $this->timestamp);
    }

    public function subDay(int $day): static
    {
        return new static (strtotime('-' . $day . 'day', $this->timestamp));
    }

    public function subSecond(int $second): static
    {
        return new static (strtotime('-' . $second . 'second', $this->timestamp));
    }

    public function __toString(): string
    {
        return $this->getDisplay();
    }
}
