<?php

namespace RocketScience;

use Doctrine\Instantiator\Exception\InvalidArgumentException;

final class LaunchLogProcessor
{
    const EXPECTED_RESOURCE_TYPE = 'stream';

    public function groupBy($stream, $groupBy, $filterBySuccessful): array
    {
        if (!is_resource($stream) || self::EXPECTED_RESOURCE_TYPE !== get_resource_type($stream)) {
            throw new InvalidArgumentException('Expected $stream to have type resource of type stream.');
        }

        $isCommentRecord = function (string $s): bool {
            return '#' === $s[0];
        };
        $isMultiPayloadLaunchSubrecord = function (string $s): bool {
            return ' ' === $s[0];
        };
        $year = function (string $s): string {
            return trim(mb_substr($s, 13, 4));
        };
        $month = function (string $s): string {
            return trim(mb_substr($s, 18, 3));
        };
        $isSuccessful = function (string $s): bool {
            return 'S' === trim(mb_substr($s, 193, 1));
        };
        $not = function (callable  $p) {
            return function ($x) use ($p) {
                return !$p($x);
            };
        };
        $true = function () {
            return true;
        };

        $agg = ('year' === $groupBy) ? $year : $month;
        $successFilter = $this->chooseSuccessFilter($filterBySuccessful, $isSuccessful, $not, $true);

        $acc = [];

        while ($line = fgets($stream)) {
            if ($isCommentRecord($line) || $isMultiPayloadLaunchSubrecord($line)) {
                continue;
            }

            if (!$successFilter($line)) {
                continue;
            }

            $key = $agg($line);

            if (isset($acc[$key])) {
                $acc[$key] = $acc[$key] + 1;
            } else {
                $acc[$key] = 1;
            }
        }

        return $acc;
    }

    protected function chooseSuccessFilter($filterBySuccessful, $isSuccessful, $not, $true)
    {
        if (true === $filterBySuccessful) {
            return $isSuccessful;
        }

        if (false === $filterBySuccessful) {
            return $not($isSuccessful);
        }

        return $true;
    }
}
