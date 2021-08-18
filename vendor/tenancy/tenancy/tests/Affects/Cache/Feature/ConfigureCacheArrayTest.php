<?php

declare(strict_types=1);

/*
 * This file is part of the tenancy/tenancy package.
 *
 * Copyright Tenancy for Laravel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://tenancy.dev
 * @see https://github.com/tenancy
 */

namespace Tenancy\Tests\Affects\Cache\Feature;

use Illuminate\Cache\ArrayStore;
use Tenancy\Tests\Affects\Cache\UsesArrayDriver;

class ConfigureCacheArrayTest extends DriverTest
{
    use UsesArrayDriver;

    /** @var string */
    protected $storeClass = ArrayStore::class;
}
