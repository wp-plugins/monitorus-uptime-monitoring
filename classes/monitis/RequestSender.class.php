<?php
/***************
RequestSender class
Author:  Sandeep Bhola
Used to send the request to monitus and get outpur
***************/
abstract class RequestSender
{
	public $apiKey;
	public $secretKey;
	public $API_URL;
	/*************
	function to start variables
	$apiKey api key
	$secretKey secret key for your accout
	$apiUrl api URl where request are sent
	*************/
	public function RequestSender($apiKey, $secretKey, $apiUrl)
	{
		$this->apiKey = $apiKey;
		$this->secretKey = $secretKey;
		$this->API_URL = $apiUrl;
	}
	/*************
	function to send request via post
	$action variable that decide the action to be performed by Monitus
	$params array of parameters that would be sent to get result
	*************/
	public function makePostRequest($action, $params = array())
	{
        $url = $this->API_URL;
		$curTime = time();
		$formattedTime = gmdate("Y-m-d H:i:s");//"yyyy-MM-dd HH:mm:ss"
		
		$reqParams = array();
		$reqParams["action"] = $action;
		$reqParams["apikey"] = $this->apiKey;
        $reqParams['timestamp'] = $formattedTime;
		$reqParams["version"] = "2";
		if (count($params)) {
			foreach($params as $paramKey=>$paramValue) {
				//if(trim($paramValue)!==''){
                $reqParams[$paramKey] = $paramValue;
                //}
			}
		}
		$sortedKeys = ksort($reqParams);
		$queryParams = array();
		$paramValueStr = "";
		foreach($reqParams as $sortedKey=>$sortedValue) {
			$paramValueStr .= $sortedKey;
			$paramValueStr .= $reqParams[$sortedKey];
			$queryParams[$sortedKey] = $reqParams[$sortedKey];
		}

		if($action == 'addUser') 
            $checkSum = $this->hmac($paramValueStr, '598IB0T3K34R8RDGE935UQGAB4'); 
        else 
		$checkSum = $this->hmac($paramValueStr, $this->secretKey);
        $queryParams["checksum"] = $this->myUrlEncode($checkSum);
		$queryParamsStr = '';
		foreach($queryParams as $key=>$val) {
			$queryParamsStr .= $key.'='.$val.'&';
		}

		$queryParamsStr = rtrim($queryParamsStr,'&');
        $exCurl = curl_init($url);
		//curl_setopt($exCurl, CURLOPT_URL, $url);
		curl_setopt($exCurl, CURLOPT_HEADER, 0);
		curl_setopt($exCurl, CURLOPT_POST, 1 );
		curl_setopt($exCurl, CURLOPT_POSTFIELDS, $queryParamsStr);
		curl_setopt($exCurl, CURLOPT_REFERER, 0);
        //echo $url.'/'.$queryParamsStr;
        if(isset($queryParams["ouput"]) && trim($queryParams["ouput"])=="xml") {
			$resp = curl_exec($exCurl);
		} else {
			curl_setopt($exCurl, CURLOPT_RETURNTRANSFER, 1);
			$resp = curl_exec($exCurl);
            $resp = json_decode($resp, true);
		}

        curl_close($exCurl);

		return $resp;
	}
	/*************
	function to send request via get
	$action variable that decide the action to be performed by Monitus
	$params array of parameters that would be sent to get result
	*************/
	public function makeGetRequest($action, $params = array())
	{
        $url = $this->API_URL;

        if (trim($this->apiKey)=='')
        {
            $reqParams['apikey']=$params['apikey'];
        }else
        {
            $reqParams["apikey"] = $this->apiKey;
        }

        $timeOfset = get_option('wpmuc_offset')*-1 ;
		$reqParams["action"] = $action;
        $reqParams["output"] = 'json';
		$reqParams["version"] = "2";
        $reqParams['timezone'] = $timeOfset;
		$reqParams["nocache"] = rand();
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
	/*************
	function to create checksum for posted vars
	$stringToSign parameter string to be converted
	$secretAccessKey secret key for your account
	*************/
	public function hmac($stringToSign, $secretAccessKey)
	{
		if (function_exists("hash_hmac")) {
			$hmac = hash_hmac("sha1",$stringToSign,$secretAccessKey,TRUE);
		} elseif(function_exists("mhash")) {
			$hmac = mhash(MHASH_SHA256,$stringToSign,$secretAccessKey);
		} else {
			die("No hash function available!");
		}
		return base64_encode($hmac);
	}
    private function myUrlEncode($string){
        $entities = array('%3B', '%3A');
        $replacements = array(";", ":");
        return str_replace($entities, $replacements, urlencode($string));
    }

}
?>