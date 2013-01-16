<?php
class WPMUC_Model_Settings extends WPMUC_Model
{
    private $settings_array_name = 'wpmuc_settings';
    private $widget_settings_array_name = 'wpmuc_widgets';
    
/**
 * Save settings
 * 
 * @param array settings array
 */ 
    public function save(array $settings)
    {
        if(trim($settings['email'])=='' || trim($settings['pass'])=='')
        {
            return false;
        }
        $settings = apply_filters('wpmuc_prepare_settings',$settings);
        $model = WPMUC_Loader::getModel('cached-data');
        $model->cleanCache();
        return update_option($this->settings_array_name, $settings);
    }
	/**
 * Save frontend chart settings
 * 
 * @param array settings array
 */ 
    public function saveChartSettings(array $settings)
    {
        return update_option('wpmuc_widgets', $settings);
    }

/**
 * Return associative array of wprc settings
 */     
    public function getSettings()
    {
        return get_option($this->settings_array_name);
    }
	
/**
 * Return associative array of wprc settings
 */  
	public function getWidgetSettings($type = null)
    {
        $settings = get_option($this->widget_settings_array_name);
		
		if($type) return $settings[$type];
		else return $settings;
    }

/**
 * Get setting by name
 * 
 * @param string setting name
 */     
    public function getSetting($setting_name)
    {
        $settings = $this->getSettings();
        
        if(!is_array($settings))
        {
            return false;
        }
        
        if(!array_key_exists($setting_name, $settings))
        {
            return false;
        }
        
        return $settings[$setting_name];
    }

/**
 * Return list of predefined records
 */     
    public function getPredefinedRecords()
    {
        $settings = array(
            'allow_compatibility_reporting' => 1
        );
        
        return $settings;
    }
/**
 * Prepare database
 */     
    public function prepareDB()
    {
        $predefined_records = $this->getPredefinedRecords();
        
        return $this->save($predefined_records);
    }
}
?>