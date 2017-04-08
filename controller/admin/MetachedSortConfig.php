<?php

class MetachedSortConfig extends oxAdminDetails
{
    /**
     * @var int[][]
     */
    private $moduleList;

    /**
     * @var string[][]
     */
    private $moduleTitles;

    /**
     * @var array
     */
    private $sortConfig;

    /**
     * @var int
     */
    private $defaultUnknownPosition;

    public function getTemplateName()
    {
        return 'MetachedSortConfig.tpl';
    }

    public function render()
    {
        parent::render();

        $this->buildModuleList();

        $this->_aViewData['moduleList']             = $this->moduleList;
        $this->_aViewData['moduleTitles']           = $this->moduleTitles;
        $this->_aViewData['defaultUnknownPosition'] = $this->defaultUnknownPosition;
        $this->_aViewData['sortConfig']             = $this->sortConfig;

        return 'MetachedSortConfig.tpl';
    }

    public function save()
    {
        $translator = function ($languageKey) {
            return oxRegistry::getLang()->translateString($languageKey);
        };

        $request = $this->getRequestData($translator);

        $oxidClass = $request['oxidClass'];

        $sortDefinition = (array) oxRegistry::getConfig()->getShopConfVar(
            'moduleSortDefinition',
            null,
            'module:kyoya-de/metached'
        );

        if (isset($request['sorting'])) {
            $sortDefinition[$oxidClass]['sorting'] = array_flip($request['sorting']);
        }

        if (isset($request['unknownPos'])) {
            $sortDefinition[$oxidClass]['unknownPosition'] = $request['unknownPos'];
        }

        oxRegistry::getConfig()->saveShopConfVar(
            'aarr',
            'moduleSortDefinition',
            $sortDefinition,
            null,
            'module:kyoya-de/metached'
        );

        $this->sendResponse(true, $translator('METACHED_MESSAGE_CONFIG_SAVE_SUCCESS'));

        echo json_encode(
            [
                'success' => false,
                'message' => 'Failed to save',
            ]
        );
        exit();
    }

    private function buildModuleList()
    {
        $moduleList = oxNew('oxmodulelist');
        /** @var oxModule[] $modules */
        $modules = $moduleList->getModulesFromDir(__DIR__ . '/../../../../');

        $this->moduleTitles = [];
        $realModules        = [];
        foreach ($modules as $module) {
            $extensions = $module->getExtensions();
            foreach ($extensions as $oxidClass => $extension) {
                $this->moduleTitles[$oxidClass][$extension] = $module->getTitle();

                $realModules[$oxidClass][] = $extension;
            }
        }

        $sorter                       = new ModuleSorter();
        $this->sortConfig             = $sorter->getSortDefinition();
        $this->defaultUnknownPosition = $sorter->getDefaultUnknownPosition();
        $this->moduleList             = $sorter->sortModules($realModules);
        ksort($this->moduleList);
    }

    /**
     * @param $translator
     *
     * @return mixed
     */
    private function getRequestData($translator)
    {
        $requestBody = file_get_contents('php://input');

        if ('' === $requestBody) {
            $this->sendResponse(false, $translator('METACHED_MESSAGE_CONFIG_SAVE_NO_DATA'), [], 400);
        }

        $request = json_decode($requestBody, true);

        if (null === $request) {
            $this->sendResponse(false, $translator('METACHED_MESSAGE_CONFIG_SAVE_INVALID_BODY'), [], 400);
        }

        if (!isset($request['sorting']) && !isset($request['unknownPos'])) {
            $this->sendResponse(false, $translator('METACHED_MESSAGE_CONFIG_SAVE_MISSING_REQUIREMENTS'), [], 400);
        }

        return $request;
    }

    private function sendResponse($success = true, $message = '', array $data = [], $status = 200)
    {
        header('Content-Type: application/json; charset=UTF-8', true, $status);
        header('X-Status: ' . ($success ? 1 : 0));
        header('X-Status-Message: ' . $message);

        echo json_encode($data);

        exit();
    }
}
