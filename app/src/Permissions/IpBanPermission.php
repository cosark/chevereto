<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Permissions;

use Chevere\Authorization\Interfaces\PermissionInterface;
use Chevere\Authorization\Traits\PermissionTrait;

enum IpBanPermission: string implements PermissionInterface
{
    use PermissionTrait;

    case Delete = 'ip-ban.delete';
}
