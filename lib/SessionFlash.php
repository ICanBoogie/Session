<?php

namespace ICanBoogie;

use ArrayAccess;

/**
 * An interface for session flash.
 *
 * @extends ArrayAccess<string, mixed>
 */
interface SessionFlash extends ArrayAccess
{
    public const SESSION_FLASH = '__FLASH__';
}
