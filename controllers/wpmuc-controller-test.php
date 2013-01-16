<?php
class WPMUC_Controller_Test extends WPMUC_Controller
{
    function test()
    {

        require_once(WPMUC_CLASSES_DIR.'/wpmuc-monitors.php');
        $monitors_obj = new WPMUC_Monitors();
        $result = $monitors_obj->getMonitors('external');
        echo 'Result:<pre>';
        print_r($result);
        echo '</pre>';
        exit;
    }
}
