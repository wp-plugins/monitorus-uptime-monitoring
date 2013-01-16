<?php
/**
 * MainClass
 * 
 * Main class of the plugin
 * Class encapsulates all hook handlers
 * 
 */
class WPMUC_MainClass
{    
/**
 * Initialize plugin environment
 */ 
   public static function init()
   { 
        if(is_admin())
        {
            
            wp_enqueue_style('wpmuc_style', WPMUC_ASSETS_URL.'/css/wpmuc.css');
            wp_enqueue_script('wpmuc', WPMUC_ASSETS_URL.'/js/wpmuc.js');
            wp_enqueue_script('jquery', WPMUC_ASSETS_URL.'/scripts/jquery-1.7.2.min.js');
            wp_enqueue_script('wpmuc_highcharts', WPMUC_ASSETS_URL.'/scripts/highcharts.js');
            wp_enqueue_script('wpmuc_easyloader', WPMUC_ASSETS_URL.'/scripts/easyui/easyloader.js');            
            // include router
            WPMUC_Loader::includeRouter();
            WPMUC_Router::execute();            
        }
       else
           {
               wp_enqueue_style('wpmuc_style', WPMUC_ASSETS_URL.'/css/wpmuc.css');
               wp_enqueue_script('jquery');
               wp_deregister_script('jquery');
               wp_enqueue_script('jquery', WPMUC_ASSETS_URL.'/scripts/jquery-1.7.2.min.js');
               
			   wp_enqueue_script('wpmuc_highcharts', WPMUC_ASSETS_URL.'/scripts/highcharts.js');
               wp_enqueue_script('wpmuc_easyloader', WPMUC_ASSETS_URL.'/scripts/easyui/easyloader.js');
           }
   } 

    public static function onActivation()
    {
        self::prepareDB();
        $widgets= get_option('sidebars_widgets', array());

        if (!in_array('badge',$widgets['sidebar-1']))
        {
            $widgets['sidebar-1'][]='badge';
            update_option('sidebars_widgets',$widgets);
        }

        $option['external']['name']='Response Time';
        $option['external']['height']=200;
        $option['external']['width']=200;
		$option['external']['location'] = 'all';
        $option['external']['view'] = 'all';
        
		$option['full-page']['name']='Page Load';
        $option['full-page']['height']=200;
        $option['full-page']['width']=200;
        $option['full-page']['location'] = 'all';
        $option['full-page']['view'] = 'all';
		update_option('wpmuc_widgets',$option);
    }



    public static function onDeactivation()
    {
        self::cleanDB();
        delete_option( 'wpmuc_settings' );
        delete_option( 'wpmuc_widgets' );
    }

/**
 * Prepare database of use
 */ 
    public static function prepareDB()
    {
        $cd_model = WPMUC_Loader::getModel('cached-data');
        $cd_model->prepareDB();
    }

    public static function cleanDB()
    {
        $cd_model = WPMUC_Loader::getModel('cached-data');
        $cd_model->cleanDb();
    }

/**
 * Print language texts for javascript
 */     
    public static function printJsLanguage()
    {
        if(!is_admin())
        {
            return false;
        }
        
        WPMUC_Loader::includeLanguageManager();
        WPMUC_LanguageManager::printJsLanguage();
    }

/**
 * Add menu items
 */     
    public static function addMenuItems()
    {
        $wpmuc_index_slug = WPMUC_PAGES_DIR.'/wpmuc-index.php';
	    add_menu_page('wpmuc-index.php',__('Monitor.Us','monitor-us-client'),'manage_options',$wpmuc_index_slug,'','',71.1);
    }

    public function prepareSettings($settings)
    {
        $settings_model = WPMUC_Loader::getModel('settings');
        $user_password = $settings_model->getSetting('pass');
        $email = trim($settings['email']);
        $pass = trim($settings['pass']);
        $apikey = trim($settings['apikey']);

        if($user_password==$pass)
        {
            $settings['pass'] = $pass;
        }
        else
        {
            $settings['pass'] = md5($pass);
        }
        $settings['email'] = $email;
        $settings['apikey'] = $apikey;
        return $settings;
    }

    public static function addDashboardWidgetsChartExternal() {
        wp_add_dashboard_widget('dashboard_widget_ext', '<strong>Response Time</strong>', array('WPMUC_MainClass','dashboardWidgetChartExternalFunction'));
    }

    public static  function dashboardWidgetChartExternalFunction() {
        echo WPMUC_Loader::includeWidget('external-monitor');
    }

    public static function getWidgetName($type)
    {
        $widget=get_option('wpmuc_widgets');
        return $widget[$type]['name'];
    }
    public static function getWidgetHeight($type)
    {
        $widget=get_option('wpmuc_widgets');
        return $widget[$type]['height'];
    }
	public static function getWidgetWidth($type)
    {
        $widget=get_option('wpmuc_widgets');
        return $widget[$type]['width'];		
    }
    function widgetWpmucChartFullControl() {
        $widgetname=self::getWidgetName('full-page');
        $widgetHeight=self::getWidgetHeight('full-page');
        $widgetWidth=self::getWidgetWidth('full-page');
        if (!empty($_REQUEST['widget_full_title']) && !empty($_REQUEST['widget_full_height']) && !empty($_REQUEST['widget_full_width'])) {
            $option=get_option('wpmuc_widgets');
            $option['full-page']['name']=$_REQUEST['widget_full_title'];
                if(preg_match('/^([0-9])+$/',$_REQUEST['widget_full_height']))
                {
                    if ($_REQUEST['widget_full_height']>=200 && $_REQUEST['widget_full_height']<=10000)
                    {
                        $option['full-page']['height']=$_REQUEST['widget_full_height'];
                    }
                }
				if(preg_match('/^([0-9])+$/',$_REQUEST['widget_full_width']))
                {
                    if($_REQUEST['widget_full_width']>=200 && $_REQUEST['widget_full_width']<=10000)
					{
						$option['full-page']['width']=$_REQUEST['widget_full_width'];
					}
                }
            update_option('wpmuc_widgets', $option);
        }
        ?>
    <p>
    <label for="widget_full_title" >Title :</label>
    <input class="widefat" type="text" id="widget_full_title" name="widget_full_title" value="<?php echo $widgetname ?>" />
     </p>
     <p>
    <label for="widget_full_height" >Heigth px: </label>
    <input class="widefat" type="text" id="widget_full_height" name="widget_full_height" value="<?php echo $widgetHeight ?>" />
     </p>
	 <p>
    <label for="widget_full_width" >Width px: </label>
    <input class="widefat" type="text" id="widget_full_width" name="widget_full_width" value="<?php echo $widgetWidth ?>" />
     </p>
    <?php
    }

    function widgetWpmucChartExternalControl() {
        $widgetname=self::getWidgetName('external');
        $widgetHeight=self::getWidgetHeight('external');
        $widgetWidth=self::getWidgetWidth('external');
		if (!empty($_REQUEST['widget_external_title']) && !empty($_REQUEST['widget_external_height']) && !empty($_REQUEST['widget_external_width']))
		{
            $option=get_option('wpmuc_widgets');
            $option['external']['name']=$_REQUEST['widget_external_title'];
            if(preg_match('/^([0-9])+$/',$_REQUEST['widget_external_height']))
            {
                if ($_REQUEST['widget_external_height'] >=200 && $_REQUEST['widget_external_height'] <= 10000)
				{
                    $option['external']['height']=$_REQUEST['widget_external_height'];
                }
            }
			if(preg_match('/^([0-9])+$/',$_REQUEST['widget_external_width']))
            {
                if($_REQUEST['widget_external_width']>=200 && $_REQUEST['widget_external_width']<=10000)
				{
					$option['external']['width']=$_REQUEST['widget_external_width'];
				}
            }
            update_option('wpmuc_widgets', $option);
        }
        ?>
    <p>
        <label for="widget_external_title" >Title :</label>
        <input class="widefat" type="text" id="widget_external_title" name="widget_external_title" value="<?php echo $widgetname ?>" /><br>
     </p>
    <p>
        <label for="widget_external_height" >Heigth px:</label>
        <input class="widefat" type="text" id="widget_external_height" name="widget_external_height" value="<?php echo $widgetHeight ?>" />
     </p>
	     <p>
        <label for="widget_external_width" >Width px:</label>
        <input class="widefat" type="text" id="widget_external_width" name="widget_external_width" value="<?php echo $widgetWidth ?>" />
     </p>
    <?php
    }
    function widgetWpmucChartFull ()
    {
        echo '<h3 class="widget-title"><strong>'.WPMUC_MainClass::getWidgetName('full-page').'</strong></h3>';
        echo WPMUC_Loader::includeWidget('full-page-loading-monitor');
    }

    function widgetWpmucChartExternal ()
    {
        echo '<h3 class="widget-title"><strong>'.WPMUC_MainClass::getWidgetName('external').'</strong></h3>';
        echo WPMUC_Loader::includeWidget('external-monitor');
    }
    function addDashboardWidgetsChartFull() {
        wp_add_dashboard_widget('dashboard_widget_full', '<strong>Page Load</strong>', array('WPMUC_MainClass','dashboardWidgetChartFullFunction'));
    }


    function dashboardWidgetChartFullFunction() {
        echo WPMUC_Loader::includeWidget('full-page-loading-monitor');
    }

    function badgesContent() {
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        if ($settings['below']>0)
        {
            $data = '<div align="center" style="margin-bottom: 15px">';
            $data .=  WPMUC_Loader::badgesBelow($settings['below']);
            $data . '<div>';
        }
        echo $data;
    }
}
?>