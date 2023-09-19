<?php declare(strict_types=1);

namespace App\Enums\User;

use BenSampo\Enum\Enum;

/**
 * @method static static ACTIVE()
 * @method static static INACTIVE()
 * @method static static SUSPENDED()
 */
final class UserStatus extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 1;
    const SUSPENDED = 2;
}
