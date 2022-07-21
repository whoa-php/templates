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

namespace Whoa\Templates\Package;

use Whoa\Contracts\Application\ContainerConfiguratorInterface;
use Whoa\Contracts\Container\ContainerInterface as WhoaContainerInterface;
use Whoa\Contracts\Settings\Packages\TemplatesSettingsInterface;
use Whoa\Contracts\Settings\SettingsProviderInterface;
use Whoa\Contracts\Templates\TemplatesInterface;
use Whoa\Templates\Contracts\TemplatesCacheInterface;
use Whoa\Templates\Package\TemplatesSettings as C;
use Whoa\Templates\TwigTemplates;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * @package Whoa\Templates
 */
class TwigTemplatesContainerConfigurator implements ContainerConfiguratorInterface
{
    /** @var callable */
    public const CONFIGURATOR = [self::class, self::CONTAINER_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function configureContainer(WhoaContainerInterface $container): void
    {
        $container[TemplatesInterface::class] = function (PsrContainerInterface $container): TemplatesInterface {
            $settings = $container->get(SettingsProviderInterface::class)->get(C::class);
            return new TwigTemplates(
                $settings[TemplatesSettingsInterface::KEY_APP_ROOT_FOLDER],
                $settings[TemplatesSettingsInterface::KEY_TEMPLATES_FOLDER],
                $settings[TemplatesSettingsInterface::KEY_CACHE_FOLDER] ?? null,
                $settings[TemplatesSettingsInterface::KEY_IS_DEBUG] ?? false,
                $settings[TemplatesSettingsInterface::KEY_IS_AUTO_RELOAD] ?? false
            );
        };

        $container[TemplatesCacheInterface::class] =
            function (PsrContainerInterface $container): TemplatesCacheInterface {
                return $container->get(TemplatesInterface::class);
            };
    }
}
