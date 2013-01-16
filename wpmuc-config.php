<?php
define('WPMUC_PLUGIN_PATH', dirname(__FILE__));
define('WPMUC_PLUGIN_FOLDER', basename(WPMUC_PLUGIN_PATH));

if(defined('WP_PLUGIN_URL'))
{
    define('WPMUC_PLUGIN_URL',WP_PLUGIN_URL.'/'.WPMUC_PLUGIN_FOLDER);
    define('WPMUC_ASSETS_URL',WPMUC_PLUGIN_URL.'/assets');
}
define('WPMUC_ASSETS_DIR',WPMUC_PLUGIN_PATH.'/assets');
define('WPMUC_IMAGES_URL',WPMUC_PLUGIN_URL.'/assets/images');
define('WPMUC_PAGES_DIR',WPMUC_PLUGIN_PATH.'/pages');
define('WPMUC_TEMPLATES_DIR',WPMUC_PLUGIN_PATH.'/pages/templates');
define('WPMUC_WIDGET_DIR',WPMUC_PLUGIN_PATH.'/widgets');
define('WPMUC_WIDGET_TEMPLATES_DIR',WPMUC_PLUGIN_PATH.'/widgets/templates');
define('WPMUC_CLASSES_DIR',WPMUC_PLUGIN_PATH.'/classes');
define('WPMUC_TABLES_DIR',WPMUC_PLUGIN_PATH.'/tables');
define('WPMUC_MODELS_DIR',WPMUC_PLUGIN_PATH.'/models');

define('WPMUC_API_URL','http://www.monitor.us/api');


define('WPMUC_DB_TABLE_CACHED_DATA', 'wpmuc_cached_data');

//set timeout for page load
define('WPMUC_MONITOR_TIMEOUT_PAGE_LOAD', 2500);
//set timeout for response time
define('WPMUC_MONITOR_TIMEOUT_RESPONSE', 2500);
?>