<?php namespace Limoncello\Tests\Flute\Adapters;

/**
 * Copyright 2015-2017 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Exception;
use Limoncello\Flute\Adapters\PaginationStrategy as PS;
use Limoncello\Tests\Flute\TestCase;

/**
 * @package Limoncello\Tests\Flute
 */
class PaginationStrategyTest extends TestCase
{
    /**
     * Test parse input paging parameters.
     *
     * @throws Exception
     */
    public function testParsingWithDefaultLessThanMaxLimitSize(): void
    {
        $strategy = new PS($defaultPageSize = 30, $maxPageSize = 100);

        $skip = 0;
        $size = 40;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, $size], $parsed);

        $skip = -1;
        $size = 40;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([0, $size], $parsed);

        $skip = 200;
        $size = 40;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, $size], $parsed);

        $skip = 0;
        $size = 200;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, $maxPageSize], $parsed);

        $skip   = 0;
        $size   = -200;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, 1], $parsed);
    }

    /**
     * Test parse input paging parameters.
     *
     * @throws Exception
     */
    public function testParsingWithDefaultGreaterThanMaxLimitSize(): void
    {
        $strategy = new PS($defaultPageSize = 200, $maxPageSize = 100);

        $skip = 0;
        $size = 40;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, $size], $parsed);

        $skip = -1;
        $size = 40;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([0, $size], $parsed);

        $skip = 200;
        $size = 40;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, $size], $parsed);

        $skip = 0;
        $size = 200;
        $parsed = $strategy->parseParameters([PS::PARAM_PAGING_OFFSET => $skip, PS::PARAM_PAGING_LIMIT => $size]);
        $this->assertEquals([$skip, $defaultPageSize], $parsed);
    }
}
