<?php namespace Limoncello\Data\Migrations;

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

use Limoncello\Contracts\Data\ModelSchemeInfoInterface;
use Limoncello\Data\Contracts\MigrationContextInterface;

/**
 * @package Limoncello\Data
 */
class MigrationContext implements MigrationContextInterface
{
    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var ModelSchemeInfoInterface
     */
    private $modelSchemes;

    /**
     * @param string                   $modelClass
     * @param ModelSchemeInfoInterface $modelSchemes
     */
    public function __construct(string $modelClass, ModelSchemeInfoInterface $modelSchemes)
    {
        $this->modelClass   = $modelClass;
        $this->modelSchemes = $modelSchemes;
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @return ModelSchemeInfoInterface
     */
    public function getModelSchemes(): ModelSchemeInfoInterface
    {
        return $this->modelSchemes;
    }
}