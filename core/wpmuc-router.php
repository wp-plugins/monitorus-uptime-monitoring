<?php
class WPMUC_Router
{
    public static function execute($controller = '', $action = '')
    {
        if(($controller<>'' && $action=='') || ($controller=='' && $action<>''))
        { 
            return false;
        }

        $do = ($action=='') ? self::getParam('wpmuc_action') : $action;
        $controller_name = ($controller=='') ? self::getParam('wpmuc_c') : $controller; 
        
        if($do<>'')
        {
            if($controller_name)
            {  
                $controllerObject = WPMUC_Loader::getController($controller_name);

                if($controllerObject)
                { 
                    if(method_exists($controllerObject, $do))
                    {
                        call_user_func_array(array($controllerObject, $do), array($_GET, $_POST));
                    }
                }
            }
            exit;
        }
    }
    
    private static function getParam($param_name)
    {
        if(!array_key_exists($param_name, $_GET))
        {
            return false;
        }
      
        $param = strip_tags($_GET[$param_name]);
        
        if(!preg_match('/^[-a-zA-Z_]*/',$param))
        {
            return false;
        }
        
        return $param;  
    }
}
?>