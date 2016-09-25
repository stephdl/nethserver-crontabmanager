<?php
namespace NethServer\Module\CronTab;


use Nethgui\System\PlatformInterface as Validate;
use Nethgui\Controller\Table\Modify as Table;

/**
 * Crontab Manager
 *
 * @author stephane de labrusse <stephdl@de-labrusse.fr>
 * 
 */
class Modify extends \Nethgui\Controller\Table\Modify
{



    public function initialize()
    {
           $months =  $this->createValidator()->memberOf(array('1m','2m', '3m',
           '4m','5m','6m','7m','8m','9m','10m','11m','12m'));

           $weekdays = $this->createValidator()->memberOf(array('1w','2w','3w',
           '4w','5w','6w','7w'));

           $every = $this->createValidator()->memberOf('*','disabled');

           $eachX = $this->createValidator()->memberOf('*/','disabled');

        $parameterSchema = array(

            array('Name', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::KEY),
            array('Minute', $this->createValidator()->integer()->lessThan(60) , \Nethgui\Controller\Table\Modify::FIELD),
            array('Hour', $this->createValidator()->integer()->lessThan(24), \Nethgui\Controller\Table\Modify::FIELD),
            array('Day', $this->createValidator()->integer()->greatThan(0)->lessThan(32), \Nethgui\Controller\Table\Modify::FIELD),
            array('Month', $months, \Nethgui\Controller\Table\Modify::FIELD),
            array('WeekDay', $weekdays , \Nethgui\Controller\Table\Modify::FIELD),
            array('User', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Cmd', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('Mail', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),

            array('EveryMinute', $every, \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryHour', $every, \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryDay', $every, \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryMonth', $every, \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryWeekDay', $every, \Nethgui\Controller\Table\Modify::FIELD),

            array('EachXMinute', $eachX, \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXHour', $eachX, \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXDay', $eachX, \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXMonth', $eachX, \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXWeekDay', $eachX, \Nethgui\Controller\Table\Modify::FIELD),

            array('AdvancedCron', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('AdvancedUser', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Advanced', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),

        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('AdvancedUser','root');
        $this->setDefaultValue('User','root');
        $this->setDefaultValue('status','enabled');
        $this->setDefaultValue('Mail','enabled');
        $this->setDefaultValue('EveryMinute','*');
        $this->setDefaultValue('EveryHour','*');
        $this->setDefaultValue('EveryDay','*');
        $this->setDefaultValue('EveryMonth','*');
        $this->setDefaultValue('EveryWeekDay','*');

        $this->setDefaultValue('EachXMinute','disabled');
        $this->setDefaultValue('EachXHour','disabled');
        $this->setDefaultValue('EachXDay','disabled');
        $this->setDefaultValue('EachXMonth','disabled');
        $this->setDefaultValue('EachXWeekDay','disabled');

        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        #test if the key exists
        $keyExists = $this->getPlatform()->getDatabase('crontab')->getType($this->parameters['Name']) != '';
        if($this->getIdentifier() === 'create' && $keyExists) {
        $report->addValidationErrorMessage($this, 'Name', 'Service_key_exists_message');
        }
        parent::validate($report);
    }



    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\CronTab\Modify',
            'update' => 'NethServer\Template\CronTab\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);

       $view['WeekDayDatasource'] =   \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(
                '1w' => $view->translate('monday_label'),
                '2w' => $view->translate('tuesday_label'),
                '3w' => $view->translate('wednesday_label'),
                '4w' => $view->translate('thursday_label'),
                '5w' => $view->translate('friday_label'),
                '6w' => $view->translate('saturday_label'),
                '7w' => $view->translate('sunday_label'),
        ));

       $view['MonthDatasource'] =   \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(
                '1m' => $view->translate('january_label'),
                '2m' => $view->translate('february_label'),
                '3m' => $view->translate('march_label'),
                '4m' => $view->translate('april_label'),
                '5m' => $view->translate('may_label'),
                '6m' => $view->translate('june_label'),
                '7m' => $view->translate('july_label'),
                '8m' => $view->translate('august_label'),
                '9m' => $view->translate('september_label'),
                '10m' => $view->translate('october_label'),
                '11m' => $view->translate('november_label'),
                '12m' => $view->translate('december_label'),
		));

        $view['HourDatasource'] = array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt));
        }, array('0','1', '2', '3','4','5','6','7','8','9','10','11','12',
           '13','14','15','16','17','18','19','20','21','22','23'));

        $view['MinuteDatasource'] = array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt));
        }, array('0','1', '2', '3','4','5','6','7','8','9','10','11','12',
           '13','14','15','16','17','18','19','20','21','22','23','24','25','26',
           '27','28','29','30','31','32','33','34','35','36','37','38','39','40',
           '41','42','43','44','45','46','47','48','49','50','51','52','53','54',
           '55','56','57','58','59'));


        $view['DayDatasource'] = array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt));
        }, array('1', '2', '3','4','5','6','7','8','9','10','11','12',
           '13','14','15','16','17','18','19','20','21','22','23','24','25','26',
           '27','28','29','30','31'));
    }

    public function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-crontabmanager-update@post-process');
    }

}
