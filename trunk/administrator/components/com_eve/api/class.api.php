<?php
/**************************************************************************
	PHP Api Lib
	Copyright (C) 2007  Kw4h

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/
class Api
{
	private $apikey = null;
	private $userid = null;
	private $charid = null;
	private $apisite = "api.eve-online.com";
	private $cachedir = './xmlcache';
	private $cachetime = 60;
	private $timeformat = "Y-n-d H:i:s";
	public $debug = false;
	private $msg = array();
	private $usecache = false;
	
	public function setCredentials($userid, $apikey, $charid = null)
	{
		if (!empty($userid) && !empty($apikey) && is_numeric($userid))
		{
			$this->userid = $userid;
			$this->apikey = $apikey;
		}
		
		if (!empty($charid) && is_numeric($charid))
		{
			$this->charid = $charid;
		}
	}
	
	public function debug($bool)
	{
		if (is_bool($bool))
			$this->debug = $bool;
	}
	
	public function cache($bool)
	{
		if (is_bool($bool))
			$this->usecache = $bool;
	}
	
	public function setCacheDir($dir) {
		$this->cachedir = $dir;
	}
	
	// add error message - both params are strings and are formatted as: "$type: $message"
	private function addMsg($type, $message)
	{
		if (!empty($type) && !empty($message))
		{
			$index = count($this->msg);
			
			$this->msg[$index]['type'] = $type;
			$this->msg[$index]['msg'] = $message;
		}
	}
	
	/**********************
		Retrieve an XML File
		$path	path relative to the $apisite url
		$params	array of paramaters (exclude apikey and userid, and charid)
				$params['characterID'] = 123456789;
	***********************/
	public function retrieveXml($path, $params = null)
	{
		if (!empty($path))
		{
			if (!is_array($params)) {
				$params = array();
			}
			if ($this->userid != null && $this->apikey != null)
			{
				$params['userID'] = $this->userid;
				$params['apiKey'] = $this->apikey;
			}
			
			if ($this->charid != null)
			{
				$params['characterID'] = $this->charid;
			}
			
			// continue when not cached
			if (!$this->isCached($path) || !$this->usecache)
			{
				// poststring
				if (count($params) > 0)
					$poststring = http_build_query($params);
				else
					$poststring = "";
				
				// open connection to the api
				$fp = fsockopen($this->apisite, 80);
				
				if (!$fp)
				{
					if ($this->debug)
						$this->addMsg("Error", "Could not connect to API URL");
				}
				else
				{
					// request the xml
					fputs ($fp, "POST " . $path . " HTTP/1.0\r\n");
					fputs ($fp, "Host: " . $this->apisite . "\r\n");
					fputs ($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
					fputs ($fp, "User-Agent: PHPApi\r\n");
					fputs ($fp, "Content-Length: " . strlen($poststring) . "\r\n");
					fputs ($fp, "Connection: close\r\n\r\n");
					if (strlen($poststring) > 0)
						fputs ($fp, $poststring."\r\n");
					
					// retrieve contents
					$contents = "";
					while (!feof($fp))
					{
						$contents .= fgets($fp);
					}
					
					// close connection
					fclose($fp);
					
					$start = strpos($contents, "\r\n\r\n");
					if ($start !== FALSE)
					{
						$contents = substr($contents, $start + strlen("\r\n\r\n"));
						
						// check if there's an error or not
						$xml = new SimpleXMLElement($contents);
						
						$error = (string) $xml->error;
						if (!empty($error))
						{
							if ($this->debug)
							{
								$this->addMsg("Api", $error);
							}
							
							if ($this->isCached($path))
							{
								return $this->loadCache($path);
							}
							
							return null;
						}
						
						if (!$this->isCached($path))
						{
							$this->store($contents, $this->getCacheFile($path));
						}
						
						return $contents;
					}
					
					if ($this->debug)
					{
						$this->addMsg("Error", "Could not parse contents");
					}
					
					return null;
				}
			}
			else
			{
				return $this->loadCache($path);
			}
		}
		
		if ($this->debug)
		{
			$this->addMsg("Error", "Path is empty");
		}
		
		return null;
	}
	
	private function store($contents, $path)
	{
		if (!file_exists(dirname($path)))
		{
			mkdir(dirname($path), 0777, true);
		}
		
		$fp = fopen($path, "w");
		
		if ($fp)
		{
			fwrite($fp, $contents);
			fclose($fp);
		}
		else
		{
			if ($this->debug)
			{
				$this->addMsg("Error", "Could not open file for writing: " . $path);
			}
		}
		
	}
	
	private function getCacheFile($path)
	{
		$realpath = "";
		if (!empty($this->userid))
		{
			if (!empty($this->charid))
			{
				$realpath = $this->cachedir . '/' . $this->userid . '/' . $this->charid . $path;
			}
			else
			{
				$realpath = $this->cachedir . '/' . $this->userid . $path;
			}
		}
		else
		{
			$realpath = $this->cachedir . $path;
		}
		
		return $realpath;
	}
	
	private function loadCache($path)
	{
		// its cached, open it and use it
		$file = $this->getCacheFile($path);
		
		$fp = fopen($file, "r");
		if ($fp)
		{
			$contents = fread($fp, filesize($file));
			fclose($fp);
		}
		else
		{
			if ($this->debug)
			{
				$this->addMsg("Error", "Could not open file for reading: " . $path);
			}
		}
		
		return $contents;
	}
	
	// checking if the cache expired or not based on TQ time
	// doing this so its easily faked manually and helps me developing without cache probs :P
	private function isCached($path)
	{
		$cachefile = $this->getCacheFile($path);
		if (file_exists($cachefile))
		{
			$fp = fopen($cachefile, "r");
			
			if ($fp)
			{
				$contents = fread($fp, filesize($cachefile));
				fclose($fp);
				
				// check cache
				$xml = new SimpleXMLElement($contents);
				
				$cachetime = (string) $xml->currentTime;
				$time = strtotime($cachetime);
				
				// get GMT time
				$timenow = time();
				$now = $timenow - date('Z', $timenow);
				
				// if now is an hour ahead of the cached time, pretend this file is not cached
				$minutes = $this->cachetime * 60;
				if ($now >= $time + $minutes)
				{
					return false;
				}
				
				return true;
			}
			else
			{
				if ($this->debug)
				{
					$this->addMsg("Error", "Could not open file for writing: " . $path);
				}
			}
		}
		else
		{
			if ($this->debug)
			{
				$this->addMsg("Error", "File does not exist: " . $cachefile);
			}
		}
	}
	
	public function printErrors()
	{
		foreach ($this->msg as $msg)
		{
			echo ("<b>" . $msg['type'] . "</b>: " . $msg['msg'] . "</br>\n");
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Functions to retrieve data
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getAccountBalance($corp = false)
	{
		if ($corp == true)
		{
			$contents = $this->retrieveXml("/corp/AccountBalance.xml.aspx");
		}
		else
		{
			$contents = $this->retrieveXml("/char/AccountBalance.xml.aspx");
		}
		
		return $contents;
	}
	
	public function getSkillInTraining()
	{
		$contents = $this->retrieveXml("/char/SkillInTraining.xml.aspx");
		
		return $contents;
	}
	
	public function getCharacterSheet()
	{
		$contents = $this->retrieveXml("/char/CharacterSheet.xml.aspx");
		
		return $contents;
	}
	
	public function getCharacters()
	{
		$contents = $this->retrieveXml("/account/Characters.xml.aspx");
		
		return $contents;
	}
	
	public function getSkillTree()
	{
		$contents = $this->retrieveXml("/eve/SkillTree.xml.aspx");
		
		return $contents;
	}
	
	public function getRefTypes()
	{
		$contents = $this->retrieveXml("/eve/RefTypes.xml.aspx");
		
		return $contents;
	}
	
	public function getMemberTracking()
	{
		$contents = $this->retrieveXml("/corp/MemberTracking.xml.aspx");
		
		return $contents;
	}
	
	public function getStarbaseList()
	{
		$contents = $this->retrieveXml("/corp/StarbaseList.xml.aspx");
		
		return $contents;
	}
	
	public function getStarbaseDetail($id)
	{
		if (is_numeric($id))
		{
			$params = array();
			$params['itemID'] = $id;
			
			$contents = $this->retrieveXml("/corp/StarbaseDetail.xml.aspx", $params);
			
			return $contents;
		}
		
		return null;
	}
	
	public function getWalletTransactions($transid = null, $corp = false, $accountkey = 1000)
	{
		$params = array();
		
		// accountKey
		if (is_numeric($accountkey))
			$params['accountKey'] = $accountkey;
		else
			$params['accountKey'] = 1000;
		
		// beforeRefID
		if ($transid != null && is_numeric($transid))
			$params['beforeTransID'] = $transid;
			
		$content = null;
		if ($corp == true)
		{
			$contents = $this->retrieveXml("/corp/WalletTransactions.xml.aspx", $params);
		}
		else
		{
			$contents = $this->retrieveXml("/char/WalletTransactions.xml.aspx", $params);
		}
		
		return $contents;
	}
	
	public function getWalletJournal($refid = null, $corp = false, $accountkey = 1000)
	{
		$params = array();
		
		// accountKey
		if (is_numeric($accountkey))
			$params['accountKey'] = $accountkey;
		else
			$params['accountKey'] = 1000;
		
		// beforeRefID
		if ($refid != null && is_numeric($refid))
			$params['beforeRefID'] = $refid;
			
		$content = null;
		if ($corp == true)
		{
			$contents = $this->retrieveXml("/corp/WalletJournal.xml.aspx", $params);
		}
		else
		{
			$contents = $this->retrieveXml("/char/WalletJournal.xml.aspx", $params);
		}
		
		return $contents;
	}
	
}
?>