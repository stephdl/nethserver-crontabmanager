<?php
namespace NethServer\Module;

/**
 * Crontab Manager
 *
 * @author stephane de labrusse <stephdl@de-labrusse.fr>
 * 
 */

class CronTab extends \Nethgui\Controller\TableController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Management', 20);
    }

    public function initialize()
    {
        $columns = array(
            'Key',
            'Advanced',
            'status',
            'Actions',
           );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('crontab', 'cron'))
            ->setColumns($columns)
            ->addTableActionPluggable(new CronTab\Modify('create'), 'PlugService')
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addRowActionPluggable(new CronTab\Modify('update'), 'PlugService')
            ->addRowActionPluggable(new CronTab\Modify('delete'), 'PlugDelete')
        ;

        parent::initialize();
    }

    public function prepareViewForColumnAdvanced(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if ($values['Advanced'] == 'enabled') {
            return $view->translate('Enabled_label');
        }
        return $view->translate('Disabled_label');
    }

    public function prepareViewForColumnstatus(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if ($values['status'] == 'enabled') {
            return $view->translate('Enabled_label');
        }
        return $view->translate('Disabled_label');
    }
}

