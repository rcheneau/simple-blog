<?php

declare(strict_types=1);

namespace App\DataFixture;

use DateTimeImmutable;
use Faker\Provider\DateTime;

final class DateTimeImmutableProvider extends DateTime
{
    /** @noinspection PhpUnused */
    public static function dateTimeImmutableBetween(
        \DateTime|string $startDate = '-30 years',
        \DateTime|string $endDate = 'now',
        string|null      $timezone = null
    ): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable(parent::dateTimeBetween($startDate, $endDate, $timezone));
    }
}
