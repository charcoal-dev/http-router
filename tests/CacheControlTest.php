<?php
/*
 * This file is a part of "charcoal-dev/http-router" package.
 * https://github.com/charcoal-dev/http-router
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/http-router/blob/main/LICENSE
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Charcoal\Http\Router\Controllers\CacheControl;
use Charcoal\Http\Router\Controllers\CacheStoreDirective;

/**
 * Class CacheControlTest
 */
class CacheControlTest extends TestCase
{
    public function testPublicCacheWithMaxAge()
    {
        $cache = new CacheControl(CacheStoreDirective::PUBLIC, 3600);
        $this->assertEquals(
            "public, max-age=3600, s-maxage=3600",
            $cache->getHeaderValue()
        );
    }

    public function testPrivateCacheWithNoCache()
    {
        $cache = new CacheControl(CacheStoreDirective::PRIVATE, 600, noCache: true);
        $this->assertEquals(
            "private, max-age=600, s-maxage=600, no-cache",
            $cache->getHeaderValue()
        );
    }

    public function testNoStoreCache()
    {
        $cache = new CacheControl(CacheStoreDirective::NO_STORE, 0);
        $this->assertEquals(
            "no-store, max-age=0, s-maxage=0",
            $cache->getHeaderValue()
        );
    }

    public function testMustRevalidateDirective()
    {
        $cache = new CacheControl(CacheStoreDirective::PUBLIC, 1800, mustRevalidate: true);
        $this->assertEquals(
            "public, max-age=1800, s-maxage=1800, must-revalidate",
            $cache->getHeaderValue()
        );
    }

    public function testImmutableAndNoTransformDirectives()
    {
        $cache = new CacheControl(CacheStoreDirective::PUBLIC, 7200, immutable: true, noTransform: true);
        $this->assertEquals(
            "public, max-age=7200, s-maxage=7200, immutable, no-transform",
            $cache->getHeaderValue()
        );
    }

    public function testCustomDirectives()
    {
        $cache = new CacheControl(CacheStoreDirective::PUBLIC, 3600, customDirectives: [
            "stale-while-revalidate=60",
            "stale-if-error=300"
        ]);
        $this->assertEquals(
            "public, max-age=3600, s-maxage=3600, stale-while-revalidate=60, stale-if-error=300",
            $cache->getHeaderValue()
        );
    }

    public function testDefaultSMaxAge()
    {
        $cache = new CacheControl(CacheStoreDirective::PUBLIC, 500);
        $this->assertStringContainsString("s-maxage=500", $cache->getHeaderValue());
    }
}



