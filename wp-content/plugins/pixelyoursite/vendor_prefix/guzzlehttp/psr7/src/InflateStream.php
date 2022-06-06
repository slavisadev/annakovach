<?php

declare (strict_types=1);
namespace PYS_PRO_GLOBAL\GuzzleHttp\Psr7;

use PYS_PRO_GLOBAL\Psr\Http\Message\StreamInterface;
/**
 * Uses PHP's zlib.inflate filter to inflate zlib (HTTP deflate, RFC1950) or gzipped (RFC1952) content.
 *
 * This stream decorator converts the provided stream to a PHP stream resource,
 * then appends the zlib.inflate filter. The stream is then converted back
 * to a Guzzle stream resource to be used as a Guzzle stream.
 *
 * @link http://tools.ietf.org/html/rfc1950
 * @link http://tools.ietf.org/html/rfc1952
 * @link http://php.net/manual/en/filters.compression.php
 */
final class InflateStream implements \PYS_PRO_GLOBAL\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    public function __construct(\PYS_PRO_GLOBAL\Psr\Http\Message\StreamInterface $stream)
    {
        $resource = \PYS_PRO_GLOBAL\GuzzleHttp\Psr7\StreamWrapper::getResource($stream);
        // Specify window=15+32, so zlib will use header detection to both gzip (with header) and zlib data
        // See http://www.zlib.net/manual.html#Advanced definition of inflateInit2
        // "Add 32 to windowBits to enable zlib and gzip decoding with automatic header detection"
        // Default window size is 15.
        \stream_filter_append($resource, 'zlib.inflate', \STREAM_FILTER_READ, ['window' => 15 + 32]);
        $this->stream = $stream->isSeekable() ? new \PYS_PRO_GLOBAL\GuzzleHttp\Psr7\Stream($resource) : new \PYS_PRO_GLOBAL\GuzzleHttp\Psr7\NoSeekStream(new \PYS_PRO_GLOBAL\GuzzleHttp\Psr7\Stream($resource));
    }
}