<?php

namespace ICanBoogie;

use ArrayAccess;

/**
 * An interface for session flash.
 *
 * @extends ArrayAccess<string, mixed>
 *
 * @internal
 */
interface SessionFlash extends ArrayAccess
{
    public const string SESSION_FLASH = '__FLASH__';
}
