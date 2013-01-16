<?php
class WPMUC_Controller_Settings extends WPMUC_Controller
{
/**
  * Save wprc settings
  * 
  * @param array $_GET array
  * @param array $_POST array
  */  
    private static $signup = false;
    public function save($get, $post)
    {      
        $settings = array();
        $settings_model = WPMUC_Loader::getModel('settings');
        
            if (trim($post['settings']['email']) =='' || trim($post['settings']['pass']) =='' )
            {
                $settings_model->save($settings);
                $this->redirectToIndex('apikey_failure');
                return false;
            }
            else
            {
			    if($post['settings']['signup'] == 'true')
                {   
                    self::$signup = true;
                    require_once(WPMUC_CLASSES_DIR.'/wpmuc-monitors.php');
                    $monitors_obj = new WPMUC_Monitors();
                    $response = $monitors_obj->addUser($post['settings']['email'], $post['settings']['pass'], $post['settings']['firstName'],
                                                        $post['settings']['lastName']);
                    
                    if($response['status'] != 'ok') {
                        $this->redirectToIndex($response['status']);
                        return false;
                    }
                    $settings['apikey'] = $response['data'];
                }
                $settings = $post['settings'];
               
            }
        $settings_model->save($settings);
        $flag = $this->addMonitorsData($settings);
        $this->redirectToIndex($flag);
    }

    public function replaceExistingMonitor($get,$post)
    {
        require_once(WPMUC_CLASSES_DIR.'/wpmuc-monitors.php');
        if(!$monitors_obj){
            $monitors_obj = new WPMUC_Monitors();
        }
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
         $id=$get['id'];
        if (is_numeric($id))
        {
            $monitors_obj->deleteFullPageLoadMonitor($id);
            $fullPage=$monitors_obj->addFullPageMonitor();
            $settings['fullpage_monitor_id']=$fullPage['data']['testId'];
            $settings_model->save($settings);
            $flag = 'success';
        }
        else
        {
            $flag = 'failure';
        }
        $this->redirectToIndex($flag);
    }

    public function useExistingMonitor($get,$post)
    {
        $id=$get['id'];
        if (is_numeric($id))
        {
            $settings_model = WPMUC_Loader::getModel('settings');
            $settings = $settings_model->getSettings();
            //$settings['fullpage_monitor_id']=$id;
            $settings['fullpage_monitor_id']='';
            $flag = 'success';
            $settings_model->save($settings);
        }
        else
        {
            $flag = 'failure';
        }

        $this->redirectToIndex($flag);
    }

/**
 * Redirect to the index page
 * 
 * @param string result flag
 */     
    public function redirectToIndex($flag)
    {
        $index_page = admin_url().'admin.php?page='.WPMUC_PLUGIN_FOLDER.'/pages/wpmuc-index.php&result='.$flag;
        header('location: '.$index_page);
    }

    function saveBadges($get,$post)
    {
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        $settings['below']=$post['settings']['below'];
        $settings_model->save($settings);
        $flag = 'success';
        $this->redirectToIndex($flag);
    }

    private function addMonitorsData($settings)
    {
        $settings_model = WPMUC_Loader::getModel('settings');
        $monitors_obj = WPMUC_Loader::getMonitors();
        if(!$settings['apikey']){
           $apikey = trim($monitors_obj->getApiKey());
        } else{
            $apikey = $settings['apikey'];
        }
        $flag = 'failure';
        if(trim($apikey)=='' or $apikey=='false')
        {
            $flag = 'apikey_failure';
            return $flag;
        }
        elseif($apikey!=='')
        {
            $r = $monitors_obj->addContact($settings['email']); //add contact + confirmation
            $flag = 'success';
        }

        $monitors = $monitors_obj->checkExistingMonitors();
        if (trim($monitors['external'])=='')
        {
            $external=$monitors_obj->addExternalMonitor();
            $settings['external_monitor_id'] = $external['data']['testId'];
        }
        else
        {
            $settings['external_monitor_id'] = $monitors['external'];
        }

        if ($monitors['full-page']=='' && !is_array($monitors['full-page']))
        {
            $fullPage=$monitors_obj->addFullPageMonitor();
            $settings['fullpage_monitor_id'] = $fullPage['data']['testId'];
        }
        else
        {
            if (is_array($monitors['full-page']))
            {
                $flag='fullexist&id='.$monitors['full-page']['testId'].'&name='.$monitors['full-page']['url'];
            }
            else
            {
                $settings['fullpage_monitor_id']=$monitors['full-page'];
            }
        }
        
        $monitors_obj->addNotificationRule($settings['fullpage_monitor_id'],'fullPageLoad'); //addNotification fullpage
        $monitors_obj->addNotificationRule($settings['external_monitor_id'],'external'); //addNotification external
        $settings_model->save($settings);
        return $flag;
    }
}
?>