<?php
class WPMUC_Controller_Monitors extends WPMUC_Controller
{
    function getMonitorData($get,$post)
    {
        $model = WPMUC_Loader::getModel('monitors');
        $view = trim($get['view']);
        $type = trim($get['type']);
        $location = trim($get['location']);
        $offset = trim($get['offset']);
        $data = $model->getMonitorData($type,$view,$location,$offset);
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        if (    $settings['external_monitor_id']!=='' 
                && $settings['fullpage_monitor_id']=='' 
                && $get['view'] == 'chart' 
                && $get['type']=='full-page'
           )
        {
            require_once(WPMUC_CLASSES_DIR.'/wpmuc-monitors.php');
            $monitors_obj = new WPMUC_Monitors();
            $monitors = $monitors_obj->getAllMonitorsArray();        
            $data = array('msg'=>'no',
                'exist'=>$monitors['full-page']['url'],
                'newchart'=>$monitors_obj->getSiteUrl());
            $this->format_response(json_encode($data));
            return true;
        }
        if ($data!=='')
        {
            $this->format_response($data);
        }
    }

    function getStepNetData($get,$post)
    {
        $model = WPMUC_Loader::getModel('monitors');
        $view = trim($get['view']);
        $type = trim($get['type']);
        $resultId = trim($get['resultId']);
        $year = trim($get['year']);
        $month = trim($get['month']);
        $day = trim($get['day']);
        $data = $model->getStepNetData($type,$view,$resultId,$year,$month,$day);
        if ($data!=='')
        {
            //echo json_encode($data);
            $this->format_response(json_encode($data));
        }
    }

    private function format_response($response)
    {
        echo  '<!--muc_json '.$response.' muc_json-->';
    }
    
    function replaceFullPageMonitor()
    {
        require_once(WPMUC_CLASSES_DIR.'/wpmuc-monitors.php');
        $monitors_obj = new WPMUC_Monitors();
        $monitors = $monitors_obj->getAllMonitorsArray();
        $id=$monitors['full-page']['testId'];
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        $monitors_obj->deleteFullPageLoadMonitor($id);
        $fullPage=$monitors_obj->addFullPageMonitor();
        $settings['fullpage_monitor_id']=$fullPage['data']['testId'];
        $settings_model->save($settings);
    }
    
    
}
