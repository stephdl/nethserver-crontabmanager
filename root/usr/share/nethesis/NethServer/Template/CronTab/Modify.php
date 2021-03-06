<?php
/* @var $view \Nethgui\Renderer\Xhtml */

$view->requireFlag($view::INSET_FORM);

echo $view->panel()
->setAttribute('title', $T('Cron_Command_Title'))
->insert ($view->columns()
->insert ($view->textInput('Name', ($view->getModule()->getIdentifier() === 'create' ? 0 : $view::STATE_DISABLED | $view::STATE_READONLY)))
->insert($view->checkBox('status','enabled')->setAttribute('uncheckedValue', 'disabled')
        ->setAttribute('label', $T('status_label_checkbox'))))

->insert($view->fieldsetSwitch('Advanced', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
    ->insert($view->radioButton('EveryMinute', '*'))
    ->insert($view->fieldsetSwitch('EveryMinute', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert ($view->columns()
        ->insert($view->checkBox('EachXMinute','*/', $view::LABEL_LEFT)->setAttribute('uncheckedValue', 'disabled'))
        ->insert ($view->selector('Minute', $view::SELECTOR_DROPDOWN | $view::LABEL_RIGHT))))
    
    ->insert($view->radioButton('EveryHour', '*'))
    ->insert($view->fieldsetSwitch('EveryHour', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert ($view->columns()
        ->insert($view->checkBox('EachXHour','*/', $view::LABEL_LEFT)->setAttribute('uncheckedValue', 'disabled'))
        ->insert ($view->selector('Hour', $view::SELECTOR_DROPDOWN | $view::LABEL_RIGHT))))
    
    ->insert($view->radioButton('EveryDay', '*'))
    ->insert($view->fieldsetSwitch('EveryDay', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert ($view->columns()
        ->insert($view->checkBox('EachXDay','*/', $view::LABEL_LEFT)->setAttribute('uncheckedValue', 'disabled'))
        ->insert ($view->selector('Day', $view::SELECTOR_DROPDOWN | $view::LABEL_RIGHT))))
    
    ->insert($view->radioButton('EveryMonth', '*'))
    ->insert($view->fieldsetSwitch('EveryMonth', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert ($view->columns()
        ->insert($view->checkBox('EachXMonth','*/', $view::LABEL_LEFT)->setAttribute('uncheckedValue', 'disabled'))
        ->insert ($view->selector('Month', $view::SELECTOR_DROPDOWN | $view::LABEL_RIGHT))))
    
    ->insert($view->radioButton('EveryWeekDay', '*'))
    ->insert($view->fieldsetSwitch('EveryWeekDay', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert ($view->columns()
        ->insert($view->checkBox('EachXWeekDay','*/', $view::LABEL_LEFT)->setAttribute('uncheckedValue', 'disabled'))
        ->insert ($view->selector('WeekDay', $view::SELECTOR_DROPDOWN | $view::LABEL_RIGHT))))
    ->insert ($view->textInput('User')))
    
->insert($view->fieldsetSwitch('Advanced', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE)
    ->insert ($view->columns()
    ->insert ($view->textInput('AdvancedCron'))
    ->insert ($view->textInput('AdvancedUser'))))

->insert ($view->columns()
    ->insert ($view->textArea('Cmd',$view::LABEL_ABOVE)->setAttribute('dimensions', '2x100'))
    ->insert($view->checkBox('Mail','enabled')->setAttribute('uncheckedValue', 'disabled')
            ->setAttribute('label', $T('Mail_label_checkbox'))))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
