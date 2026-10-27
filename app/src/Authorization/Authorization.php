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

namespace Chevereto\Authorization;

use Chevere\Authorization\Interfaces\RoleInterface;
use Chevere\Authorization\Interfaces\RolesInterface;
use Chevere\Authorization\Role;
use Chevere\Authorization\Roles;
use Chevereto\Permissions\AdminPermission;
use Chevereto\Permissions\ConditionsOverridePermission;
use Chevereto\Permissions\ContentPermission;
use Chevereto\Permissions\IpBanPermission;
use Chevereto\Permissions\ManagerPermission;
use Chevereto\Permissions\SettingsOverridePermission;
use Chevereto\Permissions\UserPermission;

final class Authorization
{
    public static function roles(): RolesInterface
    {
        static $cache;

        return $cache ??= new Roles(
            static::user(),
            static::manager(),
            static::admin()
        );
    }

    public static function user(): RoleInterface
    {
        static $cache;

        return $cache ??= new Role(
            RoleBit::User->value,
            RoleName::User->value,
        );
    }

    public static function manager(): RoleInterface
    {
        static $cache;

        return $cache ??= new Role(
            RoleBit::Manager->value,
            RoleName::Manager->value,
            static::user(),
            ...UserPermission::cases(),
            ...ContentPermission::cases(),
            ...ConditionsOverridePermission::cases(),
            ...SettingsOverridePermission::cases(),
            ...IpBanPermission::cases(),
        );
    }

    public static function admin(): RoleInterface
    {
        static $cache;

        return $cache ??= new Role(
            RoleBit::Admin->value,
            RoleName::Admin->value,
            static::manager(),
            static::user(),
            ...AdminPermission::cases(),
            ...ManagerPermission::cases(),
        );
    }
}
