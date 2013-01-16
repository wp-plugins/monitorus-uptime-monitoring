<?php
class WPMUC_Controller_FrontendChart extends WPMUC_Controller
{
/**
  * Save frontend chart settings
  * 
  * @param array $_GET array
  * @param array $_POST array
  */  
    public function save($get, $post)
    {  
		$settings_model = WPMUC_Loader::getModel('settings');
		$settings = $settings_model->getWidgetSettings();
		if(count($settings) == 0)
		{
			$settings['external']['name']='Response Time';
			$settings['external']['height']=200;
			$settings['external']['width']=200;
			
			$settings['full-page']['name']='Page Load';
			$settings['full-page']['height']=200;
			$settings['full-page']['width']=200;
		}		
		if($post['fixExternalLocation']){
			$settings['external']['location'] = $post['wpmuc_widgets']['external']['location'];
		} else {
			$settings['external']['location'] = 'all';
		}
		if($post['fixExternalView']){
			$settings['external']['view'] = $post['wpmuc_widgets']['external']['view'];
		} else {
			$settings['external']['view'] = 'all';
		}
		if($post['fixFullLocation']){
			$settings['full-page']['location'] = $post['wpmuc_widgets']['full-page']['location'];
		} else {
			$settings['full-page']['location'] = 'all';
		}
		if($post['fixFullView']){
			$settings['full-page']['view'] = $post['wpmuc_widgets']['full-page']['view'];
		} else {
			$settings['full-page']['view'] = 'all';
		}
		
		$settings_model->saveChartSettings($settings);
        $flag = 'Settings are saved';
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
}
?>