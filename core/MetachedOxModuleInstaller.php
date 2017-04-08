<?php
/**
 * This piece of software is released under the MIT license. Take a look at the LICENSE file.
 *
 * Feel free to copy and change the code, but never remove the original author! Pull requests are also welcome.
 *
 * @version 1.0.0
 * @author  Stefan Krenz <krenz.stefan@googlemail.com>
 */

class MetachedOxModuleInstaller extends MetachedOxModuleInstaller_parent
{
    /**
     * @var ModuleSorter
     */
    private $sorter;

    protected function _mergeModuleArrays($aAllModuleArray, $aAddModuleArray)
    {
        $merged = parent::_mergeModuleArrays($aAllModuleArray, $aAddModuleArray);

        return $this->getSorter()->sortModules($merged);
    }

    /**
     * @return ModuleSorter
     */
    private function getSorter()
    {
        if (null === $this->sorter) {
            $this->sorter = new ModuleSorter();
        }

        return $this->sorter;
    }
}
