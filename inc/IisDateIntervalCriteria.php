<?php

class IisDateIntervalCriteria extends PluginReportsDateIntervalCriteria
{
    function getSubName()
    {

        $start = $this->getStartDate();
        $end = $this->getEndDate();
        $title = $this->getCriteriaLabel($this->getName());

        if (empty($start) && empty($end)) {
            return '';
        }
        if (empty($title)) {
            if ($this->getName() == 'date-interval') {
                $title = __('Date interval', 'reports');
            }
            if ($this->getName() == 'time-interval') {
                $title = __('Time interval', 'reports');
            }
        }

        if (empty($start)) {
            return $title . ' ' . sprintf(__('%2$s %1$s'), __('Before'), Html::convDate($end));
        }

        if (empty($end)) {
            return $title . ' ' . sprintf(__('%2$s %1$s'), __('After'), Html::convDate($start));
        }

        return sprintf(__('%1$s (%2$s)'), $title, Html::convDate($start) . ' ' . __('and') . ' ' . Html::convDate($end) . ' ' . __('between', 'iistools'));
    }

    public function displayCriteria()
    {

        $this->getReport()->startColumn();
        $name = $this->getCriteriaLabel($this->getName());
        if ($name) {
            echo "$name, ";
        }
        echo $this->getCriteriaLabel($this->getName() . "_1") . '&nbsp;:';
        $this->getReport()->endColumn();

        $this->getReport()->startColumn();
        Html::showDateField($this->getName() . "_1", [
            'value' => $this->getStartDate(),
            'maybeempty' => false
        ]);
        $this->getReport()->endColumn();

        $this->getReport()->startColumn();
        if ($name) {
            echo "$name, ";
        }
        echo '<div style="text-align:center;">' . $this->getCriteriaLabel($this->getName() . "_2") . '&nbsp;' . __('and') . '</div>';
        $this->getReport()->endColumn();

        $this->getReport()->startColumn();
        Html::showDateField($this->getName() . "_2", [
            'value' => $this->getEndDate(),
            'maybeempty' => false
        ]);
        $this->getReport()->endColumn();
    }
}
