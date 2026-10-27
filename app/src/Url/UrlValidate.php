<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Url;

use Chevereto\Config\Config;
use InvalidArgumentException;
use LogicException;

final class UrlValidate
{
    public const URL_SCHEMES = ['http', 'https'];

    public readonly string $host;

    public readonly int $port;

    public readonly string $ip;

    public function __construct(
        public readonly string $url,
    ) {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        if (! in_array($scheme, static::URL_SCHEMES, true)) {
            throw new InvalidArgumentException(
                sprintf('Unsupported URL scheme `%s`', $scheme),
                400
            );
        }
        $host = strtolower(trim($parts['host'] ?? '', '[]'));
        if ($host === '') {
            throw new LogicException('Missing URL host', 400);
        }
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = [$host];
        } else {
            $ips = array_merge(
                gethostbynamel($host) ?: [],
                array_column(dns_get_record($host, DNS_AAAA) ?: [], 'ipv6')
            );
        }
        if ($ips === []) {
            throw new LogicException('Unable to resolve URL host', 400);
        }
        $selfHost = parse_url(Config::host()->hostname(), PHP_URL_HOST);
        $selfIps = is_string($selfHost)
            ? (gethostbynamel($selfHost) ?: [])
            : [];
        foreach ($ips as $ip) {
            $address = \IPLib\Factory::parseAddressString($ip);
            if ($address === null
                || $address->getRangeType() !== \IPLib\Range\Type::T_PUBLIC
                || in_array($ip, $selfIps, true)
            ) {
                throw new LogicException('Unsupported non-public IP address for upload', 400);
            }
        }

        $this->host = $host;
        $this->port = $port;
        $this->ip = $ips[0];
    }

    public function getCurlOptions(): array
    {
        $options = [
            CURLOPT_URL => $this->url,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_FOLLOWLOCATION => false,
        ];
        if (filter_var($this->host, FILTER_VALIDATE_IP) === false) {
            $ip = str_contains($this->ip, ':') ? "[{$this->ip}]" : $this->ip;
            $options[CURLOPT_RESOLVE] = ["{$this->host}:{$this->port}:{$ip}"];
        }

        return $options;
    }
}
