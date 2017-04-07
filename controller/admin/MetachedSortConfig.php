<?php

class MetachedSortConfig extends oxAdminDetails
{
    /**
     * @var int[][]
     */
    private $sortDefinition;

    /**
     * @var string[][]
     */
    private $moduleTitles;

    /**
     * @return array|int[][]
     */
    public function getSortDefinition()
    {
        if (null === $this->sortDefinition) {
            $this->buildModuleList();
        }

        return $this->sortDefinition;
    }

    /**
     * @param $extendedClass
     * @param $moduleClass
     *
     * @return string
     */
    public function getModuleTitle($extendedClass, $moduleClass)
    {
        if (null === $this->moduleTitles) {
            $this->buildModuleList();
        }

        return isset($this->moduleTitles[$extendedClass][$moduleClass]) ?
            $this->moduleTitles[$extendedClass][$moduleClass] : $moduleClass;
    }

    public function saveOrder()
    {
        header('Content-Type: application/json; charset=UTF-8', true, 200);
        echo json_encode([
            'OXIDClass' => oxRegistry::getConfig()->getRequestParameter('oxidClass'),
            'Order' =>  oxRegistry::getConfig()->getRequestParameter('order'),
        ]);
        exit();
    }

    public function getTemplateName()
    {
        return 'MetachedSortConfig.tpl';
    }

    private function buildModuleList()
    {
        $moduleList = oxNew('oxmodulelist');
        /** @var oxModule[] $modules */
        $modules = $moduleList->getModulesFromDir(__DIR__ . '/../../../../');

        $this->moduleTitles = [];
        $realModules = [];
        foreach ($modules as $module) {
            $extensions = $module->getExtensions();
            foreach ($extensions as $oxidClass => $extension) {
                $this->moduleTitles[$oxidClass][$extension] = $module->getTitle();
                $realModules[$oxidClass][] = $extension;
            }

        }

        $sortDefinition = (array) oxRegistry::getConfig()->getConfigParam('moduleSortDefinition');;

        foreach ($realModules as $oxidClass => $realModule) {
            ArrayUtils::mergeSort(
                $realModules[$oxidClass],
                function ($a, $b) use ($oxidClass, $sortDefinition) {
                    if ($sortDefinition[$oxidClass][$a] === $sortDefinition[$oxidClass][$b]) {
                        return 0;
                    }

                    return $sortDefinition[$oxidClass][$a] < $sortDefinition[$oxidClass][$b] ? -1 : 1;
                }
            );
        }

        $this->sortDefinition = $realModules;
    }
}
