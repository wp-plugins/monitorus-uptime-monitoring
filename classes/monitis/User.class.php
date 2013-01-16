<?php
/****************************
Class for Subaccounts
Author:  Sandeep Bhola
Monitis Api
****************************/
class User extends RequestSender
{
	public function User() { }
	/*********************
	Function to get User Key
	function name getUserKey
	$userName an e-mail of a registered user which acts as a username
	$password password of that registered user
	$output to assign return type xml or Json
	*********************/
	public function getUserKey($userName, $password, $output = "")
	{
		$params["userName"] = trim($userName);
		$params["password"] = trim($password);
		if($output=="xml") {
			$params["output"] = 'xml';
		} else {
			$params["output"] = "json";
		}
        $params["output"] = "json";
        echo "<pre>";
        print_r($params);
        echo "</pre>";
		return parent::makeGetRequest("userkey", $params);
	}

    public function getSecretKey($apiKey, $output = "")
    {
        $params["userName"] = trim($userName);
        $params["password"] = trim($password);
        if($output=="xml") {
            $params["output"] = 'xml';
        } else {
            $params["output"] = "json";
        }
        $params["output"] = "json";
        echo "<pre>";
        print_r($params);
        echo "</pre>";
        return parent::makeGetRequest("secretkey", $params);
    }
	/*********************
	Function to get Authoriztion Token
	function name getAuthToken
	$output to assign return type xml or Json
	*********************/
	public function getAuthToken($secretkey)
	{
		$params["secretkey"] = $secretkey;
        return parent::makeGetRequest("authToken", $params);
	}

}
?>