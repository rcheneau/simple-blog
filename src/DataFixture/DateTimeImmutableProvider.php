<?php

declare(strict_types=1);

namespace App\DataFixture;

use DateTimeImmutable;
use DateTimeInterface;
use Faker\Provider\DateTime;

/**
 * Extends faker's DateTime provider to be able to do the same as dateTimeBetween but with immutable DateTime.
 * Code taken from: https://github.com/nelmio/alice/issues/1096#issuecomment-1156175048
 */
final class DateTimeImmutableProvider extends DateTime
{
    /** @noinspection PhpUnused */
    public static function dateTimeImmutableBetween($startDate = '-30 years', $endDate = 'now', $timezone = null): DateTimeImmutable
    {
        $startTimestamp = $startDate instanceof DateTimeInterface ? $startDate->getTimestamp() : $startDate;
        $dateTime = parent::dateTimeBetween($startTimestamp, $endDate, $timezone);

        return DateTimeImmutable::createFromMutable($dateTime);
    }
}
