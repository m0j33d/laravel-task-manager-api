<?php declare(strict_types=1);

namespace App\Enums\Task;

use BenSampo\Enum\Enum;

/**
 * @method static static ACTIVE()
 * @method static static INACTIVE()
 * @method static static SUSPENDED()
 */
final class TaskStatus extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 1;

}
