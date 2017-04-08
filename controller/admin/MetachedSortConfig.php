<?php
/**
 * This piece of software is released under the MIT license. Take a look at the LICENSE file.
 *
 * Feel free to copy and change the code, but never remove the original author! Pull requests are also welcome.
 *
 * @version 1.0.0
 * @author  Stefan Krenz <krenz.stefan@googlemail.com>
 */

class MetachedSortConfig extends oxAdminDetails
{
    private static $objectTypeGroups = [
        oxAdminDetails::class    => 'METACHED_OBJECT_TYPE_ADMIN_DETAILS',
        oxAdminList::class       => 'METACHED_OBJECT_TYPE_ADMIN_LIST',
        oxAdminView::class       => 'METACHED_OBJECT_TYPE_ADMIN_VIEW',
        ajaxListComponent::class => 'METACHED_OBJECT_TYPE_ADMIN_AJAX',
        oxWidget::class          => 'METACHED_OBJECT_TYPE_WIDGET',
        oxUBase::class           => 'METACHED_OBJECT_TYPE_CONTROLLER',
        oxBase::class            => 'METACHED_OBJECT_TYPE_MODEL',
        oxView::class            => 'METACHED_OBJECT_TYPE_COMPONENT',
    ];

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

    /**
     * @var string[]
     */
    private $groups;

    public function getTemplateName()
    {
        return 'MetachedSortConfig.tpl';
    }

    public function render()
    {
        parent::render();

        $grouping = $this->getGrouping();

        $this->buildModuleList();
        $this->buildGroups($grouping);

        $this->_aViewData['moduleGroups']           = $this->groups;
        $this->_aViewData['grouping']               = $grouping;
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

    private function buildGroups($groupType)
    {
        $translator = function ($languageKey) {
            return oxRegistry::getLang()->translateString($languageKey);
        };

        $this->groups = [];
        $oxidClasses  = array_keys($this->moduleTitles);

        foreach ($oxidClasses as $oxidClass) {
            if ('alpha' === $groupType) {
                $groupId = strtoupper($oxidClass[0]);
            } elseif ('object' === $groupType) {
                $groupId = $this->getGroupIdFromClassName($oxidClass);
            } else {
                continue;
            }

            $this->groups[$translator($groupId)][] = $oxidClass;
            asort($this->groups[$translator($groupId)]);
        }

        ksort($this->groups);
    }

    private function getGroupIdFromClassName($className)
    {
        if (class_exists($className)) {
            $objectInstance = oxNew($className);
            foreach (self::$objectTypeGroups as $baseClass => $groupId) {
                if ($objectInstance instanceof $baseClass) {
                    return $groupId;
                }
            }
        }

        return 'METACHED_OBJECT_TYPE_OTHER';
    }

    private function buildModuleList()
    {
        $moduleList = oxNew('oxmodulelist');
        /** @var oxModule[] $modules */
        $modules = $moduleList->getModulesFromDir(__DIR__ . '/../../../../');

        $moduleTitles = [];
        $realModules  = [];
        foreach ($modules as $module) {
            $extensions = $module->getExtensions();
            foreach ($extensions as $oxidClass => $extension) {
                $realModules[$oxidClass][]            = $extension;
                $moduleTitles[$oxidClass][$extension] = $module->getTitle();
            }
        }

        $this->moduleTitles           = $moduleTitles;
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

    /**
     * @return string
     */
    private function getGrouping()
    {
        $grouping        = oxRegistry::getSession()->getVariable('metached-grouping') ?: 'alpha';
        $requestGrouping = oxRegistry::getConfig()->getRequestParameter('grouping');

        if (null !== $requestGrouping) {
            oxRegistry::getSession()->setVariable('metached-grouping', $requestGrouping);
            $grouping = $requestGrouping;
        }

        return $grouping;
    }
}
