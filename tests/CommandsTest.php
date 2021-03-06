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

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Contracts\Commands\IoInterface;
use Whoa\Contracts\Container\ContainerInterface;
use Whoa\Contracts\FileSystem\FileSystemInterface;
use Whoa\Contracts\Settings\SettingsProviderInterface;
use Whoa\Templates\Commands\TemplatesCommand;
use Whoa\Templates\Contracts\TemplatesCacheInterface;
use Whoa\Templates\Package\TemplatesSettings;
use Whoa\Tests\Templates\Data\Templates;
use Whoa\Tests\Templates\Data\TestContainer;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @package Whoa\Tests\Templates
 */
class CommandsTest extends TestCase
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
     * Test `Clean` command.
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testClean()
    {
        $container = $this->createContainer();
        $this
            ->addSettingsProvider($container)
            ->addFileSystem($container);

        /** @var Mock $command */
        $command = Mockery::mock(TemplatesCommand::class . '[createCachingTemplateEngine]');
        $command->shouldAllowMockingProtectedMethods();

        /** @var TemplatesCommand $command */

        $command->execute($container, $this->createIo(TemplatesCommand::ACTION_CLEAR_CACHE, 0, 1));

        // Mockery will do checks when the test finished
        $this->assertTrue(true);
    }

    /**
     * Test `Create` command.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreate()
    {
        $container = $this->createContainer();
        $this
            ->addSettingsProvider($container);

        /** @var Mock $cacheMock */
        $cacheMock = Mockery::mock(TemplatesCacheInterface::class);
        $cacheMock->shouldReceive('cache')->zeroOrMoreTimes()->withAnyArgs()->andReturnUndefined();

        $container[TemplatesCacheInterface::class] = $cacheMock;

        $command = new TemplatesCommand();

        $this->assertNotEmpty($command::getName());
        $this->assertNotEmpty($command::getDescription());
        $this->assertNotEmpty($command::getHelp());
        $this->assertNotEmpty($command::getArguments());
        $this->assertEmpty($command::getOptions());

        $command::execute($container, $this->createIo(TemplatesCommand::ACTION_CREATE_CACHE, 0, 2));
    }

    /**
     * Test invalid action command.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvalidAction()
    {
        $container = $this->createContainer();

        /** @var Mock $command */
        $command = Mockery::mock(TemplatesCommand::class . '[createCachingTemplateEngine]');
        $command->shouldAllowMockingProtectedMethods();

        /** @var TemplatesCommand $command */

        $errors = 1;
        $command->execute($container, $this->createIo('XXX', $errors));

        // Mockery will do checks when the test finished
        $this->assertTrue(true);
    }

    /**
     * @param ContainerInterface $container
     * @return self
     */
    private function addSettingsProvider(ContainerInterface $container): self
    {
        $appConfig = [];
        $settings = (new Templates())->get($appConfig);

        /** @var Mock $settingsMock */
        $settingsMock = Mockery::mock(SettingsProviderInterface::class);
        $settingsMock->shouldReceive('get')->once()->with(TemplatesSettings::class)->andReturn($settings);

        $container[SettingsProviderInterface::class] = $settingsMock;

        return $this;
    }

    /**
     * @param ContainerInterface $container
     * @return self
     */
    private function addFileSystem(ContainerInterface $container): self
    {
        /** @var Mock $fsMock */
        $fsMock = Mockery::mock(FileSystemInterface::class);
        $folder = '/some/path';
        $fsMock->shouldReceive('scanFolder')->once()->withAnyArgs()->andReturn([$folder]);
        $fsMock->shouldReceive('isFolder')->once()->with($folder)->andReturn(true);
        $fsMock->shouldReceive('deleteFolderRecursive')->once()->with($folder)->andReturnUndefined();

        $container[FileSystemInterface::class] = $fsMock;

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    private function createContainer(): ContainerInterface
    {
        return new TestContainer();
    }

    /**
     * @param string $action
     * @param int $errors
     * @param int $writes
     * @return IoInterface
     */
    private function createIo(string $action, int $errors = 0, int $writes = 0): IoInterface
    {
        /** @var Mock $ioMock */
        $ioMock = Mockery::mock(IoInterface::class);

        $ioMock->shouldReceive('getArgument')->once()
            ->with(TemplatesCommand::ARG_ACTION)->andReturn($action);

        if ($errors > 0) {
            $ioMock->shouldReceive('writeError')->times($errors)
                ->withAnyArgs()->andReturnSelf();
        }

        if ($writes > 0) {
            $ioMock->shouldReceive('writeInfo')->times($writes)
                ->withAnyArgs()->andReturnSelf();
        }

        /** @var IoInterface $ioMock */

        return $ioMock;
    }
}
