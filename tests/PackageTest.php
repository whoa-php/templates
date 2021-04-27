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

namespace Whoa\Tests\Templates;

use Whoa\Contracts\Settings\SettingsProviderInterface;
use Whoa\Contracts\Templates\TemplatesInterface;
use Whoa\Templates\Contracts\TemplatesCacheInterface;
use Whoa\Templates\Package\TwigTemplatesContainerConfigurator;
use Whoa\Templates\Package\TwigTemplatesProvider;
use Whoa\Templates\Package\TemplatesSettings;
use Whoa\Tests\Templates\Data\Templates;
use Whoa\Tests\Templates\Data\TestContainer;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @package Whoa\Tests\Templates
 */
class PackageTest extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * Test ContainerConfigurator.
     */
    public function testContainerConfigurator()
    {
        $appConfig = [];
        $settings  = (new Templates())->get($appConfig);

        /** @var Mock $settingsMock */
        $settingsMock = Mockery::mock(SettingsProviderInterface::class);
        $settingsMock->shouldReceive('get')->once()->with(TemplatesSettings::class)->andReturn($settings);

        $container = new TestContainer();

        $container[SettingsProviderInterface::class] = $settingsMock;

        TwigTemplatesContainerConfigurator::configureContainer($container);

        $this->assertTrue($container->has(TemplatesInterface::class));
        $this->assertNotNull($container->get(TemplatesInterface::class));

        $this->assertTrue($container->has(TemplatesCacheInterface::class));
        $this->assertNotNull($container->get(TemplatesCacheInterface::class));
    }

    /**
     * Test template provider.
     */
    public function testTemplateProvider()
    {
        $this->assertNotEmpty(TwigTemplatesProvider::getContainerConfigurators());
        $this->assertNotEmpty(TwigTemplatesProvider::getCommands());
    }

    /**
     * Test template settings.
     */
    public function testSettings()
    {
        $appConfig = [];
        $settings  = (new Templates())->get($appConfig);

        $this->assertNotEmpty($settings[Templates::KEY_CACHE_FOLDER]);
        $this->assertNotEmpty($settings[Templates::KEY_TEMPLATES_FOLDER]);

        $sampleTemplatePath = 'Samples' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'test.html.twig';
        $this->assertEquals([$sampleTemplatePath], $settings[Templates::KEY_TEMPLATES_LIST]);
    }
}
