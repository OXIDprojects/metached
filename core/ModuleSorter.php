<?php
/**
 * This piece of software is released under the MIT license. Take a look at the LICENSE file.
 *
 * Feel free to copy and change the code, but never remove the original author! Pull requests are also welcome.
 *
 * @version 1.0.0
 * @author  Stefan Krenz <krenz.stefan@googlemail.com>
 */

class ModuleSorter
{
    /**
     * @var int[][]
     */
    private $sortDefinition;

    /**
     * @var int
     */
    private $defaultUnknownPosition;

    /**
     * @param string[][] $extensions
     *
     * @return string[][]
     */
    public function sortModules($extensions)
    {
        $sorted = [];

        foreach ($extensions as $extendedClass => $extension) {
            $sorted[$extendedClass] = $this->sortExtends($extendedClass, $extension);
        }

        return $sorted;
    }

    /**
     * @param string   $extendedClass
     * @param string[] $extensions
     *
     * @return mixed
     */
    public function sortExtends($extendedClass, $extensions)
    {
        $sortDefinition         = $this->getSortDefinition();
        $defaultUnknownPosition = $this->getDefaultUnknownPosition();

        ArrayUtils::mergeSort(
            $extensions,
            function ($a, $b) use ($extendedClass, $sortDefinition, $defaultUnknownPosition) {
                // Check if we've something to sort.
                if (null === $a || null === $b) {
                    return 0;
                }

                // Check if we've a configuration for this OXID class.
                if (!isset($sortDefinition[$extendedClass])) {
                    return 0;
                }

                $definition = $sortDefinition[$extendedClass];

                $unknownPosition = $defaultUnknownPosition;
                if (isset($definition['unknownPosition'])) {
                    $unknownPosition = (int) $definition['unknownPosition'];
                }

                $sorting = $definition['sorting'];

                $knownA = isset($sorting[$a]);
                $knownB = isset($sorting[$b]);

                // Check if both entries are configured. Keep the position if both are unknown.
                if (!$knownA && !$knownB) {
                    return 0;
                }

                // Check if the entry which have to be sorted is configured.
                if (!$knownA) {
                    return $unknownPosition;
                }

                // Check if the entry which is used as sort base is configured.
                if (!$knownB) {
                    return -1 * $unknownPosition;
                }

                // Check if both entries have the same priority (should not be happen!).
                if ($sorting[$a] === $sorting[$b]) {
                    return 0;
                }

                // Sort both entries.
                return $sorting[$a] < $sorting[$b] ? -1 : 1;
            }
        );

        return $extensions;
    }

    /**
     * @return int[][]
     */
    public function getSortDefinition()
    {
        if (null === $this->sortDefinition) {
            $this->sortDefinition = (array) oxRegistry::getConfig()->getShopConfVar(
                'moduleSortDefinition',
                null,
                'module:kyoya-de/metached'
            );
        }

        return $this->sortDefinition;
    }

    /**
     * @return int
     */
    public function getDefaultUnknownPosition()
    {
        if (null === $this->defaultUnknownPosition) {
            $this->defaultUnknownPosition = (int) oxRegistry::getConfig()->getShopConfVar(
                'defaultUnknownPosition',
                null,
                'module:kyoya-de/metached'
            );
        }

        return $this->defaultUnknownPosition;
    }

}
