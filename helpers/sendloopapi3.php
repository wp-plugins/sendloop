<?php
/*
 * Sendloop API Wrapper
 * @copyright Sendloop.com
 * @author Sendloop.com
 * @version 1.2
 * @license GNU GPL v3
 */

class SendloopAPI3
{
	private $APIKey = '';
	private $APICommand = '';
	private $Subdomain = 'app';
	private $ResponseFormat = '';
	private $APIBaseURL = 'app.sendloop.com';
	public $Result = '';

	public function __construct($APIKey, $Subdomain = null, $ResponseFormat = 'json')
	{
		$this->SetResponseFormat($ResponseFormat);

		if (empty($APIKey))
		{
			echo 'APIKey is empty';
			return false;
		}
		$this->APIKey = $APIKey;

		if (empty($Subdomain))
		{
			$this->Subdomain = null;
		}
		else
		{
			$this->Subdomain = $Subdomain;
		}

	}

	public function SetResponseFormat($ResponseFormat = 'json')
	{
		$this->ResponseFormat = 'php';
		if ($ResponseFormat == 'xml')
		{
			$this->ResponseFormat = 'xml';
		}
		else if ($ResponseFormat == 'json')
		{
			$this->ResponseFormat = 'json';
		}
	}

	private function MakeURL($APICommand)
	{
		if (is_null($this->Subdomain) == true)
		{
			$APIURL = 'https://'.$this->APIBaseURL . '/api/v3/' . $APICommand . '/' . $this->ResponseFormat;
		}
		else
		{
			$APIURL = 'https://' . $this->Subdomain . '.' . $this->APIBaseURL . '/api/v3/' . $APICommand . '/' . $this->ResponseFormat;
		}
		return $APIURL;
	}

	public function run($APICommand, $Parameters = array())
	{

		$cURL = curl_init();

		$APIURL = $this->MakeURL($APICommand);

		$ParametersArray = array('APIKey=' . $this->APIKey);
		foreach ($Parameters as $Key => $Value)
		{
			$ParametersArray[] = "$Key=$Value";
		}
		$ParametersString = implode('&', $ParametersArray);

		curl_setopt($cURL, CURLOPT_URL, $APIURL);
		curl_setopt($cURL, CURLOPT_POST, 1);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $ParametersString);

		$Result = curl_exec($cURL);

		if ($this->ResponseFormat == 'php')
		{
			$Result = unserialize($Result);
		}

		curl_close($cURL);

		$this->Result = $Result;

		return true;
	}


}