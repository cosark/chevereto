<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Legacy\Classes;

class Follow
{
    public static array $table_fields = [
        'date',
        'date_gmt',
        'user_id',
        'followed_user_id',
        'ip',
    ];

    public static function insert(
        int $user_id,
        int $followed_user_id,
    ): array {
        return [];
    }

    public static function delete(
        int $user_id,
        int $followed_user_id,
    ): array {
        return [];
    }

    public static function doesFollow(int $user_id, int $followed_id): bool
    {
        return false;
    }

    public static function getFollowersCount(
        int $user_id,
    ): array {
        return [];
    }

    public static function getSingle(
        int $user_id,
        int $followed_user_id,
    ): array {
        return [];
    }

    public static function getAll(
        int $user_id,
        int $followed_user_id,
        array $sort = []
    ): array {
        return [];
    }

    public static function get(
        int $user_id,
        int $followed_user_id,
        array $sort = [],
        ?int $limit = null
    ): array {
        return [];
    }
}
