<?php

/**
 * Copyright 2015-2020 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
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

declare(strict_types=1);

namespace Whoa\Tests\Templates\Data;

use Whoa\Templates\Package\TemplatesSettings;

/**
 * @package Whoa\Tests\Templates
 */
class Templates extends TemplatesSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        return [

                static::KEY_IS_DEBUG => true,
                static::KEY_TEMPLATES_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, 'Templates']),
                static::KEY_CACHE_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, 'Cache']),
                static::KEY_APP_ROOT_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']),

            ] + parent::getSettings();
    }
}