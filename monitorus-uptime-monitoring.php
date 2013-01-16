<?php
/*
	Plugin Name: Monitor.us uptime monitoring
	Plugin URI: http://www.monitor.us
	Description: Show everyone how stable and fast your blog is with independent authoritative professional monitoring service. Check how really stable you blog hosting is. Be the first to know when your blog is down or slow. The plug-in periodically measures your blog's main page load and uptime monitoring from checkpoints in US and EU. The measurement results are available on clean and clear charts integrated into your WordPress admin dashboard or directly on your blog main page.
	Version: 1.0
	Author: MonitorUs	 
	Author URI: http://www.monitor.us
*/

// <<<<<<<<<<<< includes --------------------------------------------------
require_once(dirname(__FILE__).'/wpmuc-config.php');
require_once(WPMUC_PLUGIN_PATH.'/wpmuc-loader.php'); 

// define plugin name (path)
define('WPMUC_PLUGIN_NAME',WPMUC_PLUGIN_FOLDER.'/'.basename(__FILE__));

WPMUC_Loader::includeMainClass();
$muc = 'WPMUC_MainClass';


// >>>>>>>>>>>> -----------------------------------------------------------      

// <<<<<<<<<<<< functions -------------------------------------------------
/**
 * Initialize plugin environment
 */ 
add_action('init', array($muc, 'init'));


/*
 * add widgets
 */
if ( function_exists('register_sidebar_widget') ){

    register_sidebar_widget('Page Load', array($muc,'widgetWpmucChartFull'));
    register_widget_control('Page Load', array($muc,'widgetWpmucChartFullControl'));
    register_sidebar_widget('Response Time', array($muc,'widgetWpmucChartExternal'));
    register_widget_control('Response Time', array($muc,'widgetWpmucChartExternalControl'));
    register_sidebar_widget('Badge', array($muc,'badgesContent'));
}
/*
 * add dashboard widgets
 */
add_action('wp_dashboard_setup', array($muc,'addDashboardWidgetsChartExternal'));
add_action('wp_dashboard_setup', array($muc,'addDashboardWidgetsChartFull'));

/**
 * Print language texts for javascript
 */ 
add_action('wp_print_scripts', array($muc, 'printJsLanguage'));

/**
 * Add plugin menu items
 */
add_action('admin_menu', array($muc, 'addMenuItems'));

/**
 * Prepares settings before inserting into the database
 */
add_filter('wpmuc_prepare_settings', array($muc, 'prepareSettings') );


/**
 * On Plugin activation
 */ 
register_activation_hook(__FILE__, array($muc, 'onActivation'));

/*
 * On Plugin deactivation
 */
register_deactivation_hook(__FILE__, array($muc, 'onDeactivation'));
// >>>>>>>>>>>> -----------------------------------------------------------
?>