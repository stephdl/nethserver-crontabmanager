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
           $minutes = $this->createValidator()->memberOf(array('0','1', '2', '3',
           '4','5','6','7','8','9','10','11','12',
           '13','14','15','16','17','18','19','20','21','22','23','24','25','26',
           '27','28','29','30','31','32','33','34','35','36','37','38','39','40',
           '41','42','43','44','45','46','47','48','49','50','51','52','53','54',
           '55','56','57','58','59'));

           $hours =  $this->createValidator()->memberOf(array('0','1', '2', '3',
           '4','5','6','7','8','9','10','11','12',
           '13','14','15','16','17','18','19','20','21','22','23'));


           $days =  $this->createValidator()->memberOf(array('1', '2', '3','4','5',
           '6','7','8','9','10','11','12',
           '13','14','15','16','17','18','19','20','21','22','23','24','25','26',
           '27','28','29','30','31'));

           $months =  $this->createValidator()->memberOf(array('jan','feb','mar','apr',
           'may','jun','jul','aug','sep','oct','nov','dec'));

           $weekdays = $this->createValidator()->memberOf(array('mon','tue','wed',
           'thu','fri','sat','sun'));

        $parameterSchema = array(
            array('UserDatasource', FALSE, array($this, 'provideMembersDatasource')), // this parameter will never be submitted: set an always-failing validator
            array('Name', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::KEY),
            array('Minute', $minutes , \Nethgui\Controller\Table\Modify::FIELD),
            array('Hour', $hours, \Nethgui\Controller\Table\Modify::FIELD),
            array('Day', $days, \Nethgui\Controller\Table\Modify::FIELD),
            array('Month', $months, \Nethgui\Controller\Table\Modify::FIELD),
            array('WeekDay', $weekdays , \Nethgui\Controller\Table\Modify::FIELD),
            array('User', Validate::USERNAME, \Nethgui\Controller\Table\Modify::FIELD),
            array('Cmd', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryMinute', $this->createValidator()->memberOf('*','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryHour', $this->createValidator()->memberOf('*','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryDay', $this->createValidator()->memberOf('*','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryMonth', $this->createValidator()->memberOf('*','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EveryWeekDay', $this->createValidator()->memberOf('*','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXMinute', $this->createValidator()->memberOf('*/','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXHour', $this->createValidator()->memberOf('*/','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXDay', $this->createValidator()->memberOf('*/','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXMonth', $this->createValidator()->memberOf('*/','disabled'), \Nethgui\Controller\Table\Modify::FIELD),
            array('EachXWeekDay', $this->createValidator()->memberOf('*/','disabled'), \Nethgui\Controller\Table\Modify::FIELD),

        );

        $this->setSchema($parameterSchema);

        $this->setDefaultValue('status','enabled');
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

    public function provideMembersDatasource()
    {
        $platform = $this->getPlatform();
        if (is_null($platform)) {
            return array();
        }

        $users = $platform->getTableAdapter('accounts', 'user');

        $values = array();

        // Build the datasource rows couples <key, label>
        foreach ($users as $username => $row) {
            $values[] = array($username, sprintf('%s %s (%s)', $row['FirstName'], $row['LastName'], $username));
        }
        array_unshift($values, array('root', sprintf('%s %s (%s)', 'Master','Administrator', 'root')));
        return $values;
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
                'mon' => $view->translate('monday_label'),
                'tue' => $view->translate('tuesday_label'),
                'wed' => $view->translate('wednesday_label'),
                'thu' => $view->translate('thursday_label'),
                'fri' => $view->translate('friday_label'),
                'sat' => $view->translate('saturday_label'),
                'sun' => $view->translate('sunday_label'),
        ));

       $view['MonthDatasource'] =   \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(
                'jan' => $view->translate('january_label'),
                'feb' => $view->translate('february_label'),
                'mar' => $view->translate('march_label'),
                'apr' => $view->translate('april_label'),
                'may' => $view->translate('may_label'),
                'jun' => $view->translate('june_label'),
                'jul' => $view->translate('july_label'),
                'aug' => $view->translate('august_label'),
                'sep' => $view->translate('september_label'),
                'oct' => $view->translate('october_label'),
                'nov' => $view->translate('november_label'),
                'dec' => $view->translate('december_label'),
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

