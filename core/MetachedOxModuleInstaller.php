<?php

class MetachedOxModuleInstaller extends MetachedOxModuleInstaller_parent
{
    protected function _mergeModuleArrays($aAllModuleArray, $aAddModuleArray)
    {
        $merged = parent::_mergeModuleArrays($aAllModuleArray, $aAddModuleArray);

        return $this->sortModuleExtensions($merged);
    }

    /**
     * @param string[][] $extensions
     *
     * @return string[][]
     */
    protected function sortModuleExtensions($extensions)
    {
        $sortDefinition = oxRegistry::getConfig()->getConfigParam('moduleSortDefinition');
        $sorted         = [];

        foreach ($extensions as $extendedClass => $extension) {
            if (isset($sortDefinition[$extendedClass])) {
                usort(
                    $extension,
                    function ($a, $b) use ($extendedClass, $sortDefinition) {
                        if ($sortDefinition[$extendedClass][$a] === $sortDefinition[$extendedClass][$b]) {
                            return 0;
                        }

                        return $sortDefinition[$extendedClass][$a] < $sortDefinition[$extendedClass][$b] ? -1 : 1;
                    }
                );
            }

            $sorted[$extendedClass] = $extension;
        }

        return $sorted;
    }
}
