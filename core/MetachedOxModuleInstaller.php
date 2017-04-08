<?php

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
