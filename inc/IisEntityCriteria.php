<?php

class IisReportsEntityCriteria extends PluginReportsDropdownCriteria
{


    /**
     * @param $report
     * @param $name      (default user)
     * @param $label     (default '')
     **/
    function __construct($report, $name = 'entities_id', $label = '')
    {

        parent::__construct($report, $name, 'glpi_entities', ($label ? $label : _n('Entity', 'Entities', 1)));
    }


    public function displayDropdownCriteria()
    {

        Entity::dropdown([
            'name'     => $this->getName(),
            'value'    => $this->getParameterValue(),
            'right'    => 'all',
            'comments' => $this->getDisplayComments(),
            'entity'   => $this->getEntityRestrict()
        ]);
    }
}
