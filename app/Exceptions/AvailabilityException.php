<?php

namespace App\Exceptions;

/**
 * Thrown when the authoritative in-transaction availability recheck fails
 * (i.e. another booking claimed the remaining inventory between the initial
 * pre-check and this request acquiring the resource lock).
 */
class AvailabilityException extends \RuntimeException
{
}
