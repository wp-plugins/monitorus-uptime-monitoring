<?php
$settings_model = WPMUC_Loader::getModel('settings');
$settings = $settings_model->getSettings();
if (isset($settings['external_monitor_id']))
{
    include_once (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-external-monitor.tpl.php');
}
else
{
    include (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-error-monitor.tpl.php');
}