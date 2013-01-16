<?php
$settings_model = WPMUC_Loader::getModel('settings');
$monitor_model = WPMUC_Loader::getModel('monitors');
$settings = $settings_model->getSettings();
if (isset($settings['fullpage_monitor_id']) && $settings['fullpage_monitor_id']!=='' ) {
   include_once (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-full-page-loading-monitor.tpl.php');
} else {
    if(!is_admin())
    {
        if (isset($settings['external_monitor_id']))
		{
            include_once (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-full-page-loading-monitor.tpl.php');
        }
		else
        {
            include (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-error-monitor.tpl.php');
        }
    }
	else
    {
        if (isset($settings['external_monitor_id']))
        {
            include_once (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-full-page-loading-monitor.tpl.php');
        }
        else
        {
            include (WPMUC_WIDGET_TEMPLATES_DIR . '/wpmuc-widget-error-monitor.tpl.php');
        }
    }
    
}

