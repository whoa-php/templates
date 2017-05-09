<?php namespace Limoncello\l10n\Messages;

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

use Limoncello\l10n\Contracts\Messages\BundleStorageInterface;

/**
 * @package Limoncello\l10n
 */
class BundleStorage implements BundleStorageInterface
{
    /** Encode index */
    const INDEX_DEFAULT_LOCALE = 0;

    /** Encode index */
    const INDEX_DATA = self::INDEX_DEFAULT_LOCALE + 1;

    /**
     * @var array
     */
    private $encodedStorage;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param array $encodedStorage
     */
    public function __construct(array $encodedStorage)
    {
        $this->setEncodedStorage($encodedStorage);
    }

    /**
     * @inheritdoc
     */
    public function has(string $locale, string $namespace, string $key): bool
    {
        assert(is_string($locale) === true && empty($locale) === false);
        assert(is_string($namespace) === true && empty($namespace) === false);
        assert(is_string($key) === true && empty($key) === false);

        $has = isset($this->getEncodedStorage()[$locale][$namespace][$key]);

        return $has;
    }

    /**
     * @inheritdoc
     */
    public function get(string $locale, string $namespace, string $key)
    {
        $locale = $this->lookupLocale($this->getLocales(), $locale, $this->getDefaultLocale());

        $result = $this->has($locale, $namespace, $key) === true ?
            $this->getEncodedStorage()[$locale][$namespace][$key] : null;

        assert($result === null || $this->checkValueWithLocale($result) === true);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasResources(string $locale, string $namespace): bool
    {
        assert(is_string($locale) === true && empty($locale) === false);
        assert(is_string($namespace) === true && empty($namespace) === false);

        $has = isset($this->getEncodedStorage()[$locale][$namespace]);

        return $has;
    }

    /**
     * @inheritdoc
     */
    public function getResources(string $locale, string $namespace): array
    {
        $locale = $this->lookupLocale($this->getLocales(), $locale, $this->getDefaultLocale());

        assert($this->hasResources($locale, $namespace) === true);

        $result = $this->getEncodedStorage()[$locale][$namespace];

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * @return array
     */
    protected function getEncodedStorage(): array
    {
        return $this->encodedStorage;
    }

    /**
     * @param string[] $locales
     * @param string   $locale
     * @param string   $defaultLocale
     *
     * @return string
     */
    protected function lookupLocale(array $locales, string $locale, string $defaultLocale): string
    {
        return locale_lookup($locales, $locale, true, $defaultLocale);
    }

    /**
     * @return string[]
     */
    protected function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @param array $encodedStorage
     *
     * @return self
     */
    protected function setEncodedStorage(array $encodedStorage): self
    {
        assert(count($encodedStorage) === 2);

        $encodedData = $encodedStorage[static::INDEX_DATA];

        // check storage has 3 levels locale -> namespace -> key & value + culture pairs and
        // keys, values and cultures are non-empty strings
        assert(is_array($encodedData) === true && $this->checkEncodedData($encodedData) === true);

        $this->defaultLocale  = $encodedStorage[static::INDEX_DEFAULT_LOCALE];
        $this->encodedStorage = $encodedData;
        $this->locales        = array_keys($encodedData);

        return $this;
    }

    /**
     * @param array $encodedData
     *
     * @return bool
     */
    private function checkEncodedData(array $encodedData): bool
    {
        $isValid = true;
        foreach ($encodedData as $locale => $namespaceResources) {
            $isValid =
                $isValid === true && $isValid = $this->checkNonEmptyString($locale) === true &&
                $isValid === true && $isValid = is_array($namespaceResources) === true &&
                $isValid === true && $isValid = $this->checkNamespaceResources($namespaceResources);
        }

        return $isValid;
    }

    /**
     * @param array $namespaceResources
     *
     * @return bool
     */
    private function checkNamespaceResources(array $namespaceResources): bool
    {
        $isValid = true;
        foreach ($namespaceResources as $namespace => $resources) {
            $isValid =
                $isValid === true && $isValid = $this->checkNonEmptyString($namespace) === true &&
                $isValid === true && $isValid = is_array($resources) === true &&
                $isValid === true && $isValid = $this->checkResources($resources);
        }

        return $isValid;
    }

    /**
     * @param array $resources
     *
     * @return bool
     */
    private function checkResources(array $resources): bool
    {
        $isValid = true;
        foreach ($resources as $key => $valueAndLocale) {
            $isValid =
                $isValid === true && $isValid = $this->checkPair($key, $valueAndLocale);
        }

        return $isValid;
    }

    /**
     * @param string $key
     * @param array  $valueAndLocale
     *
     * @return bool
     */
    private function checkPair(string $key, array $valueAndLocale): bool
    {
        $result = $this->checkNonEmptyString($key) === true && $this->checkValueWithLocale($valueAndLocale) === true;

        return $result;
    }

    /**
     * @param array $valueAndLocale
     *
     * @return bool
     */
    private function checkValueWithLocale(array $valueAndLocale): bool
    {
        $result =
            count($valueAndLocale) === 2 &&
            $this->checkNonEmptyString($valueAndLocale[static::INDEX_PAIR_VALUE]) === true &&
            $this->checkNonEmptyString($valueAndLocale[static::INDEX_PAIR_LOCALE]) === true;

        return $result;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function checkNonEmptyString($value): bool
    {
        $result = is_string($value) === true && empty($value) === false;

        return $result;
    }
}