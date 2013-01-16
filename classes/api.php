<?php
include("monitis/conf.properties.php");
include_once("monitis/OutputType.class.php");
include_once("monitis/RequestSender.class.php");

include_once("monitis/User.class.php");
include_once("monitis/SubAccount.class.php");

include_once("monitis/Layout.class.php");

include_once("monitis/Contact.class.php");

include_once("monitis/BaseMonitor.class.php");
include_once("monitis/InternalMonitor.class.php");
include_once("monitis/Agent.class.php");
include_once("monitis/CPUMonitor.class.php");
include_once("monitis/MemoryMonitor.class.php");
include_once("monitis/DriveMonitor.class.php");
include_once("monitis/ProcessMonitor.class.php");
include_once("monitis/LoadAverageMonitor.class.php");
include_once("monitis/InternalHttpMonitor.class.php");
include_once("monitis/Ping.class.php");

include_once("monitis/CustomMonitor.class.php");

include_once("monitis/TransactionMonitor.class.php");

include_once("monitis/FullpageloadMonitor.class.php");

include_once("monitis/VisitorTracking.class.php");

include_once("monitis/CloudInstance.class.php");

include_once("monitis/ExternalMonitor.class.php");
/****************************
Class that adds objects in array
Author:  Sandeep Bhola
Monitis Api
****************************/
class ExtensionBridge
{
	// variables to assign values to object
	public $apiKey;
	public $secretKey;
	public $API_URL;	
    // array containing all the extended classes
    private $_exts = array();
    public $_this;
        
    function __construct(){
		$_this = $this;		
	}
    
    public function addObject($object)
    {
		$object->apiKey = $this->apiKey;
		$object->secretKey = $this->secretKey;
		if($object->API_URL!="") {
			//
		} else 
			$object->API_URL = $this->API_URL;
        $this->_exts[]=$object;
	}
    
    public function __get($varname)
    {
        foreach($this->_exts as $ext)
        {
            if(property_exists($ext, $varname))
            return $ext->$varname;
        }		
    }
    
    public function __call($method, $args)
    {
        foreach($this->_exts as $ext)
        {
			if(method_exists($ext, $method))
			return call_user_func_array(array($ext,$method), $args);			
        }
        throw new Exception("This Method {$method} doesn't exists");
    }
}
/****************************
Class for Creating objects array
Author:  Sandeep Bhola
Monitis Api
****************************/
class Extender extends ExtensionBridge{
	private static $minstance; 
	function __construct($apiKey, $secretKey, $apiUrl)
	{
		parent::__construct($apiKey, $secretKey, $apiUrl);
	}

	public static function singleton($apiKey, $secretKey, $serverUrl, $isNewUser){
        if(!self::$minstance || $isNewUser) {
        	if($isNewUser) include("monitis/conf.properties.php");
            $c = __CLASS__;
            self::$minstance = new $c($apiKey, $secretKey, $serverUrl);
			self::$minstance->apiKey = $apiKey;
			self::$minstance->secretKey = $secretKey;
			self::$minstance->API_URL = $serverUrl;
         }
        return self::$minstance;
	}
}
?>