<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\DateTimeWithDatetimeIntervalFailedException;
use DateInterval;
use Random\RandomException;

final class DateTime extends \DateTimeImmutable implements \Stringable
{
    public const string FORMAT = 'Y-m-d\TH:i:sP';
    private const int MAX_RETRIES = 4;

    /**
     * @throws DateTimeException
     */
    public static function now(): DateTime
    {
        return self::create();
    }

    /**
     * @throws DateTimeException
     */
    public static function fromString(string $dateTime): DateTime
    {
        return self::create(dateTime: $dateTime);
    }

    /**
     * @throws DateTimeException
     */
    public static function tryFromString(?string $dateTime): ?DateTime
    {
        if (null === $dateTime) {
            return null;
        }

        return self::fromString(dateTime: $dateTime);
    }

    /**
     * @throws DateTimeException
     */
    private static function create(string $dateTime = ''): DateTime
    {
        $maxRetries = 0;
        $exception = null;

        do {
            try {
                $date = new self($dateTime);
            } catch (\Throwable $e) {
                $date = null;
                $exception = $e;
                ++$maxRetries;
            }
        } while ($maxRetries < self::MAX_RETRIES && !$date instanceof self);

        if (!$date instanceof self) {
            assert(assertion: $exception instanceof \Throwable);

            throw new DateTimeException(exception: $exception);
        }

        return $date;
    }

    /**
     * @throws DateTimeException
     */
    public static function fromDateTimeImmutable(\DateTimeImmutable $dateTimeImmutable): DateTime
    {
        $numberOfRetries = 5;
        $dateTime = null;
        $exception = null;

        do {
            try {
                $dateTime = DateTime::create(dateTime: $dateTimeImmutable->format(format: DateTime::FORMAT));
            } catch (DateTimeException $e) {
                $exception = $e;
            }
        } while ($numberOfRetries-- > 0 && !$dateTime instanceof DateTime);

        if (!$dateTime instanceof DateTime) {
            assert(assertion: $exception instanceof \Throwable);

            throw new DateTimeException(exception: $exception);
        }

        return $dateTime;
    }

    /**
     * @phpstan-param non-empty-string $addOrDeduct
     *
     * @throws RandomException
     * @throws DateTimeWithDatetimeIntervalFailedException
     */
    public static function createWithInterval(
        string $addOrDeduct,
        ?DateInterval $interval = null,
        ?int $numberOfDays = null,
        ?self $dateTime = null,
    ): self {
        $dateTime ??= new self();

        $numberOfDays ??= random_int(min: 1, max: 40);
        $format = sprintf('%d days', $numberOfDays);

        if (!$interval instanceof DateInterval) {
            $maxAttempts = 5;
            do {
                $interval ??= DateInterval::createFromDateString(datetime: $format);
            } while ($maxAttempts-- > 0 && !$interval instanceof DateInterval);
        }

        if (!$interval instanceof DateInterval) {
            throw DateTimeWithDatetimeIntervalFailedException::withInterval(intervalFormat: $format);
        }

        assert('add' === $addOrDeduct || 'sub' === $addOrDeduct);

        return $dateTime->{$addOrDeduct}($interval);
    }

    /**
     * @phpstan-param non-empty-string $addOrDeduct
     *
     * @throws RandomException
     * @throws DateTimeWithDatetimeIntervalFailedException
     */
    public function withInterval(
        string $addOrDeduct,
        ?DateInterval $interval = null,
        ?int $numberOfDays = null,
    ): self {
        return self::createWithInterval(
            addOrDeduct: $addOrDeduct,
            interval: $interval,
            numberOfDays: $numberOfDays,
            dateTime: $this,
        );
    }

    public function __toString(): string
    {
        return $this->format(format: DateTime::FORMAT);
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
