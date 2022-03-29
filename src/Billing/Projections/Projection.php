<?php
declare(strict_types=1);

namespace Billing\Projections;

interface Projection
{
    public function whenConsumerRestarted(): void;
}
