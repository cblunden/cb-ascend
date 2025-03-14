<?php

namespace App\Traits;

trait StaticallyConstructable
{
    public static function make(...$params): static
    {
        return new static(...$params);
    }
}
