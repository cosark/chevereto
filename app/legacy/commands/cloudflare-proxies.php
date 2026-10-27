<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Chevere\VarSupport\StorableVariable;
use Chevere\Writer\StreamWriter;
use IPLib\Address\Type;
use IPLib\Factory;
use IPLib\Range\Subnet;
use function Chevere\Filesystem\filePhpReturnForPath;
use function Chevere\Writer\streamFor;
use function Chevereto\Legacy\G\fetch_url;

$writer = new StreamWriter(streamFor('php://stdout', 'w'));
$writer->write(
    <<<PLAIN
    Cache Cloudflare trusted proxies
    ---

    PLAIN
);
$trustedProxiesFilepath = PATH_APP_CACHE . 'cloudflare/trusted-proxies.php';
$file = filePhpReturnForPath($trustedProxiesFilepath);
$entries = [];
foreach ([
    Type::T_IPv4 => 'https://www.cloudflare.com/ips-v4',
    Type::T_IPv6 => 'https://www.cloudflare.com/ips-v6',
] as $type => $url) {
    $contents = fetch_url($url, '', [
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_FAILONERROR => 1,
    ]);
    $ips = preg_split('/\s+/', trim($contents), -1, PREG_SPLIT_NO_EMPTY);
    if ($ips === false || $ips === []) {
        throw new LogicException(
            "No IP ranges returned by {$url}"
        );
    }
    foreach ($ips as $ip) {
        $range = Factory::parseRangeString($ip);
        if (! $range instanceof Subnet
            || $range->getAddressType() !== $type
        ) {
            throw new LogicException(
                "Invalid IPv{$type} CIDR range `{$ip}` returned by {$url}"
            );
        }
        $entries[] = $ip;
        $writer->write(
            <<<PLAIN
            - {$ip}

            PLAIN
        );
    }
}
$file->put(new StorableVariable($entries));

$writer->write(
    <<<PLAIN
    ---
    {$trustedProxiesFilepath}
    [OK] Cloudflare trusted proxies cached

    PLAIN
);

return $entries;
