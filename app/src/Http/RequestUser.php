<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevereto\Http;

// use App\Services\ParseUserAgent;
// use App\Services\UserAgent;
// use DeviceDetector\DeviceDetector;

use Chevere\Authorization\Interfaces\PermissionInterface;
use Chevere\Authorization\Interfaces\RolesInterface;

final class RequestUser
{
    public function __construct(
        public readonly RolesInterface $roles,
        public readonly int $id = 0,
        public readonly string $status = '',
        public readonly string $ipAddress = '',
    ) {
    }

    public function hasPermission(PermissionInterface ...$permission): bool
    {
        return $this->roles->permissions()->contains(...$permission);
    }
}
