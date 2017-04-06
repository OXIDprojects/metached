<?php

class MetachedSortConfig extends oxAdminDetails
{
    protected $_sThisTemplate = 'MetachedSortConfig.tpl';

    private $sortDefinition;

    public function render()
    {
        return parent::render();
    }

    public function getSortDefinition()
    {
        if (null === $this->sortDefinition) {
            $this->sortDefinition = (array) oxRegistry::getConfig()->getConfigParam('moduleSortDefinition');
        }

        return $this->sortDefinition;
    }
}
