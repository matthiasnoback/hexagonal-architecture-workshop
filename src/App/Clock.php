<?php

namespace App;

use DateTimeImmutable;

interface Clock
{
    public function getCurrentTime(): DateTimeImmutable;
}
