<?php
/**
 * This piece of software is released under the MIT license. Take a look at the LICENSE file.
 *
 * Feel free to copy and change the code, but never remove the original author! Pull requests are also welcome.
 *
 * @version 1.0.0
 * @author  Stefan Krenz <krenz.stefan@googlemail.com>
 */

class MetachedModuleEvents
{
    public function onActivate()
    {
        if (null !== $this->fetchSortDefinition()) {
            return;
        }

        $moduleList = oxNew('oxmodulelist');
        /** @var oxModule[] $modules */
        $modules = $moduleList->getModulesFromDir(__DIR__ . '/../../../../');

        $sortDefinition = [];
        foreach ($modules as $module) {
            $extensions = $module->getExtensions();
            foreach ($extensions as $oxidClass => $extension) {
                $sortDefinition[$oxidClass]['sorting'][] = $extension;
            }
        }

        /** @var array $oxidClasses */
        $oxidClasses = array_keys($sortDefinition);
        foreach ($oxidClasses as $oxidClass) {
            $realModules[$oxidClass]['sorting'] = array_flip($sortDefinition[$oxidClass]['sorting']);
        }

        oxRegistry::getConfig()->saveShopConfVar(
            'aarr',
            'moduleSortDefinition',
            $sortDefinition,
            null,
            'module:kyoya-de/metached'
        );
    }

    /**
     * @return null|array
     */
    private function fetchSortDefinition()
    {
        return oxRegistry::getConfig()->getShopConfVar(
            'moduleSortDefinition',
            null,
            'module:kyoya-de/metached'
        );
    }
}
