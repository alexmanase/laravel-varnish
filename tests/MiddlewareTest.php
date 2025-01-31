<?php

namespace Spatie\Varnish\Test;

use Route;
use Spatie\Varnish\Middleware\CacheWithVarnish;

class MiddlewareTest extends TestCase
{
    /** @test */
    public function it_adds_headers_to_a_response_signaling_that_it_may_be_cached()
    {
        $this->getRoute()->middleware(CacheWithVarnish::class);

        $this->get('/cache-me')
            ->assertHeader('X-Cacheable', '1')
            ->assertHeader('Cache-Control', 'public, s-maxage=86400');
    }

    /** @test */
    public function it_uses_the_config_value_to_determine_the_name_of_the_header()
    {
        $this->app['config']->set('varnish.cacheable_header_name', 'X-My-Custom-Header');

        $this->getRoute()->middleware(CacheWithVarnish::class);

        $this->get('/cache-me')
            ->assertHeader('X-My-Custom-Header', '1')
            ->assertHeader('Cache-Control', 'public, s-maxage=86400');
    }

    /** @test */
    public function it_uses_the_config_value_to_determine_the_max_age()
    {
        $this->app['config']->set('varnish.cache_time_in_minutes', 5);

        $this->getRoute()->middleware(CacheWithVarnish::class);

        $this->get('/cache-me')
            ->assertHeader('X-Cacheable', '1')
            ->assertHeader('Cache-Control', 'public, s-maxage=300');
    }

    /** @test */
    public function it_accepts_an_argument_to_determine_the_max_age()
    {
        $this->getRoute()->middleware(CacheWithVarnish::class.':10');

        $this->get('/cache-me')
            ->assertHeader('X-Cacheable', '1')
            ->assertHeader('Cache-Control', 'public, s-maxage=600');
    }

    private function getRoute()
    {
        return Route::get('cache-me', function () {
            return 'cache me';
        });
    }
}
