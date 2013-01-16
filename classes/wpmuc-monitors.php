<?php
class WPMUC_Monitors
{
    private $monitis_object=null;
    private $apikey='';
    private $secretkey='';
    private static $isNewUser = false;

    public function __construct($signup = false)
    {
        if(!$signup){
            require_once(WPMUC_CLASSES_DIR."/api.php");
            $this->apikey = $this->getApiKey();
            $this->secretkey = $this->getSecretKey();
            $Monitis_object = Extender::singleton($this->apikey, $this->secretKey, $serverUrl, self::$isNewUser);
            $Monitis_object->addObject(new User());
            $Monitis_object->addObject(new SubAccount());
            $Monitis_object->addObject(new Layout());
            $Monitis_object->addObject(new Contact());
            $Monitis_object->addObject(new InternalMonitor());
            $Monitis_object->addObject(new Agent());
            $Monitis_object->addObject(new CPUMonitor());
            $Monitis_object->addObject(new MemoryMonitor());
            $Monitis_object->addObject(new DriveMonitor());
            $Monitis_object->addObject(new ProcessMonitor());
            $Monitis_object->addObject(new LoadAverageMonitor());
            $Monitis_object->addObject(new InternalHttpMonitor());
            $Monitis_object->addObject(new PingMonitor());
            $Monitis_object->addObject(new CustomMonitor($serverUrlcustom));
            $Monitis_object->addObject(new TransactionMonitor());
            $Monitis_object->addObject(new FullpageloadMonitor());
            $Monitis_object->addObject(new CloudInstance());
            $Monitis_object->addObject(new VisitorTracking());
            $Monitis_object->addObject(new ExternalMonitor());
            $this->monitis_object = $Monitis_object;
       } else {
            require_once(WPMUC_CLASSES_DIR."/api.php");
            $Monitis_object = Extender::singleton('', '', $serverUrl, self::$isNewUser);
            $Monitis_object->addObject(new User());
            $this->monitis_object = $Monitis_object;
        }
    }
    function getSiteUrl()
    {
        $server = preg_replace("`^(http|https)://`is", "", site_url());
        return $server;
    }

    /** gets statistic information from the server
     * @param string 'external', 'full-page'
     * @return array information data
     */
    public function getMonitors($mode, $params=array())
    {
        $result = array();
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        $model = WPMUC_Loader::getModel('cached-data');
        switch($mode)
        {
            case 'external':

                $result = $this->monitis_object->makeGetRequest('testresult',
                        array('testId'=>$settings['external_monitor_id'],
                        'apikey'=>$settings['apikey']));

                $model->cacheData(array($mode),$result);
                break;
            case 'full-page':
                $result = $this->monitis_object->makeGetRequest('fullPageLoadTestResult',
                         array('monitorId'=>$settings['fullpage_monitor_id'],
                        'apikey'=>$settings['apikey']));

                $model->cleanCache();
                $model->cacheData(array($mode),$result);
                 break;
            case 'stepnet':
                $result = $this->getTransactionStepNet($params);
                break;
            default: return false;
       }
       return $result;

    }
    /** gets API key from the local database or from the server
     * @return string API key
     */
    public function getApiKey()
    {
         $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        if($settings && trim($settings['apikey']!==''))
        {
            return $settings['apikey'];
        }
        elseif($settings && ($settings['apikey']==null || trim($settings['apikey']=='' )))
        {
            $resultgetUserkey = $this->getUserApiKeyFromServer($settings['email'],$settings['pass']);

            if(!array_key_exists('apikey', $resultgetUserkey))
            {
                return 'false';
            }
            $settings['apikey'] = $resultgetUserkey['apikey'];
            $res = $settings_model->save($settings);
            if($res)
            {
                return $settings['apikey'];
            }
            else
            {
                return false;
            }
        }

    }

    public function getSecretKey($renewSecretKey=false)
    {
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        if($settings && trim($settings['secretkey']!=='') && $renewSecretKey = false )
        {
            return $settings['secretkey'];
        }
        elseif($settings && ($settings['secretkey']==null || trim($settings['secretkey']=='' )) || $renewSecretKey = true)
        {
            $action = 'secretkey';
            $url = WPMUC_API_URL;
            $reqParams["action"] = $action;
            $reqParams["output"] = 'json';
            $reqParams["apikey"] = $this->apikey;
            $sortedKeys = ksort($reqParams);
            $queryParams = array();
            $paramValueStr = "";
            foreach($reqParams as $sortedKey=>$sortedValue) {
                $paramValueStr .= "&".$sortedKey."=";
                $paramValueStr .= $reqParams[$sortedKey];
                $queryParams[$sortedKey] = $reqParams[$sortedKey];
            }
            $curlUrl = $url."?".http_build_query($queryParams, '', '&');
            $exCurl = curl_init();
            curl_setopt($exCurl, CURLOPT_URL, $curlUrl);
            if(isset($params["output"]) && trim($params["output"])=="xml") {
                $data = curl_exec($exCurl);
            } else {
                curl_setopt($exCurl, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($exCurl);
                $data = json_decode($data, true);
            }
            curl_close($exCurl);
            $settings['secretkey']=$data['secretkey'];
            $settings_model->save($settings);
            $this->secretkey= $data['secretkey'];
            return $data['secretkey'];
        }

    }

    /** sets query to tye server and gets response(API key)
     * @return string API key
     */
    protected function getUserApiKeyFromServer($userName, $password, $output = "")
    {
        $action = 'apikey';
        $params = array();
        $sortedKeys = array();
        $reqParams=array();
        $params["userName"] = $userName;
        $params["password"] = $password;
        if($output=="xml") {
            $params["output"] = $output;
        } else {
            $params["output"] = "json";
        }

        $url = WPMUC_API_URL;
        $reqParams["action"] = $action;
        $reqParams["output"] = 'json';
        $reqParams["version"] = "2";
        if (count($params)) {
            foreach($params as $paramKey=>$paramValue) {
                $reqParams[$paramKey] = $paramValue;
            }
        }

        $sortedKeys = ksort($reqParams);
        $queryParams = array();
        $paramValueStr = "";
        foreach($reqParams as $sortedKey=>$sortedValue) {
            $paramValueStr .= "&".$sortedKey."=";
            $paramValueStr .= $reqParams[$sortedKey];
            $queryParams[$sortedKey] = $reqParams[$sortedKey];
        }

        $curlUrl = $url."?".http_build_query($queryParams, '', '&');
        $exCurl = curl_init();
        curl_setopt($exCurl, CURLOPT_URL, $curlUrl);
        if(isset($params["output"]) && trim($params["output"])=="xml") {
            $data = curl_exec($exCurl);
        } else {
            curl_setopt($exCurl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($exCurl);
            $data = json_decode($data, true);
        }
        curl_close($exCurl);
        return $data;

    }

    public function addExternalMonitor()
    {
        $params = $this->getExternalDefaultSettings();
        $res= $this->monitis_object->makePostRequest('addExternalMonitor',$params);
        return $res;
    }

    public function addFullPageMonitor()
    {
        $params = $this->getFullPageDefaultSettings();
        $res= $this->monitis_object->makePostRequest('addFullPageLoadMonitor',$params);
        return $res;
    }

public function getFullPageDefaultSettings()
    {
        $authToken=$this->monitis_object->getAuthToken($this->secretkey);
        $server = $this->getSiteUrl();

        $default = array(
            'name' => $server.'_http',
            'tag' =>$server,
            'locationIds' => '1,2',
            'url' => $server,
			'checkInterval' => 360,360,
            'uptimeSLA' => '',
            'responseSLA'=> '',
            'timeout' => 30000,
            'authToken' => $authToken['authToken'] ,
            'validation' => 'token'
        );
        return $default;
    }

    public function getExternalDefaultSettings()
    {
        $authToken=$this->monitis_object->getAuthToken($this->secretkey);
        $server = $this->getSiteUrl();
        $default = array(
            'name' => $server.'_http',
            'tag' =>$server,
            'locationIds' => '1,3',
            'url' => $server,
            'type' => 'http',
            'interval' => 30,
            'contentMatchString' => '',
            'contentMatchFlag' => 0,
            'postData' => '',
            'params' => '',
            'detailedTestType' => 1,
            'timeout' => 10, 
            'overSSL' => 0,
            'authToken' => $authToken['authToken'] ,
            'validation' => 'token'
        );
        return $default;
    }

    function getExternalMonitorInfo($testId='')
    {
        $params=array();
        $params['testId']=$testId;
        return $this->monitis_object->makeGetRequest('testinfo',$params);
    }

    function getFullPageLoadMonitorInfo($monitorId='')
    {
        $params=array();
        $params['monitorId']=$monitorId;
        return $this->monitis_object->makeGetRequest('fullPageLoadTestInfo',$params);

    }
    
    function getAllMonitorsArray()
    {
        $mon=array();
        $settings_model = WPMUC_Loader::getModel('settings');
        $settings = $settings_model->getSettings();
        $resultEMs = $this->monitis_object->getExternalMonitor('json');
        
        
        
        foreach ($resultEMs['testList'] as $val) {
            $mon['external'][]=$this->getExternalMonitorInfo($val['id']);
        }
        $resultFullPage =$this->monitis_object->makeGetRequest('fullPageLoadTests',
                        array('output'=>'json',
                        'apikey'=>$settings['apikey']));
        $mon['full-page']=$this->getFullPageLoadMonitorInfo($resultFullPage[0]['id']);
        return $mon;

    }
    function checkExistingMonitors()
    {
        $server = $this->getSiteUrl();
        $monitors = $this->getAllMonitorsArray();
        $extExist = false;
        $fullPage = false;
        foreach ($monitors['external'] as $extmon) {
            if ($extmon['url']==$server)
            {
                $extExist =  $extmon['testId'];
            }
        }
        
        if (trim($monitors['full-page']['url'])==trim($server))
        {
            $fullPage = $monitors['full-page']['testId'];
        }
        else
        {
            if (is_array($monitors['full-page'])){
                if (!isset($monitors['full-page']['error']))
                {
                    $fullPage=$monitors['full-page'];
                }
            }
        }

        $result =  array(
                            'external'=>$extExist,
                            'full-page'=>$fullPage
                        );
        return $result;
    }

    function deleteFullPageLoadMonitor($monitorId='')
    {
        $authToken=$this->monitis_object->getAuthToken($this->secretkey);
        $params['monitorId']  = $monitorId;
        $params['validation'] = 'token';
        $params['authToken']  = $authToken['authToken'];
        $res = $this->monitis_object->makePostRequest('deleteFullPageLoadMonitor',$params);
        return $res;
    }



    function addContact($email)
    {
        $authToken=$this->monitis_object->getAuthToken($this->secretkey);
        $params['validation'] = 'token';
        $params['authToken']  = $authToken['authToken'];
        $params['contactType'] = 1; //mail
        $params['account'] = $email; //mail
        $params['firstName'] = 'test';
        $params['lastName'] = 'test';
        $params['timezone'] = 0;
        $result = $this->monitis_object->makePostRequest('addContact',$params);
        if (trim($result['status'])=='ok')
        {
            return  $this->confirmContact($result['data']['contactId'],$result['data']['confirmationKey']);
        }
        return $result;
    }

    function confirmContact($contactId,$confirmationKey)
    {
        $authToken=$this->monitis_object->getAuthToken($this->secretkey);
        $params['validation'] = 'token';
        $params['authToken']  = $authToken['authToken'];
        $params['contactId'] = $contactId;
        $params['confirmationKey'] = $confirmationKey;
        $result = $this->monitis_object->makePostRequest('confirmContact',$params);
        return $result;
    }

    function addNotificationRule($monitorId,$monitorType)
    {
        $authToken=$this->monitis_object->getAuthToken($this->secretkey);
        $params['validation'] = 'token';
        $params['authToken']  = $authToken['authToken'];
        $params['monitorType'] = $monitorType;
        $params['monitorId'] = $monitorId;
        $params['period'] = 'always';
        $params['notifyBackup'] = 0;
        $params['continuousAlerts'] = 0;
        $params['failureCount'] = 1;
        $params['comparingMethod'] = 'greater';

        $result = $this->monitis_object->makePostRequest('addNotificationRule',$params);
        return $result;
    }

    function getTransactionStepNet($params)
    {
        $params['output']='json';
        $res = $this->monitis_object->makeGetRequest('transactionStepNet',$params);
        return $res;
    }
     function addUser($email, $password, $firstName = "", $lastName = ""){
        $params["firstName"] = $firstName;
        $params["lastName"] = $lastName;
        $params["email"] = $email;
        $params["password"] = $password;
        $params["agentApiKey"] = "1DQD6J2UAOGD1DB30F5GL7QQ5U";
        $resp = $this->monitis_object->makePostRequest("addUser", $params);
        if($resp['status'] == 'ok') self::$isNewUser = true;
        return $resp;
    }
}
?>