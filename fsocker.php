<?php
/**
 * @desc Class Wrapper for fsockopen to send a Request without the need of the response
 * @author nosweat
 * @param Array $settings : 'timeout' - seconds for HTTP Max timeout for the request, default is 30
 * @param Array $settings : 'port' - HTTP Port e.g 80 or 443 , default is 80
 * @param Array $settings : 'method' - HTTP Method e.g PUT/GET/POST/HEAD/DELETE, default is POST
 * @param Array $settings : 'headers' - HTTP Headers
 * @param Array $settings : 'content_type' - HTTP Content-Type
 * @version 1.0
 */
class fsocker{
	
	const EOL = "\r\n";
	private $SETTINGS = array();
	private $CONTENT_STRING;
	private static $ALLOWED_METHODS = array(
		"GET","POST","PUT","DELETE","HEAD"
	);
	
	/*
	 * Initial settings
	 */
	public function __construct($settings=null){
		self::filterSettings($settings);
		$ini = array_merge(array(
			'http_version' => "HTTP/1.1",
			'method' => "POST",
			'timeout' => 30,
			'port' => 80,
			'content_type' => "application/x-www-form-urlencoded",
			'connection' => "Close",
			'headers' => array()
		),$settings);
		
		$this->SETTINGS = $ini;
	}
	
	/*
	 * Sets the request content , post or get data
	 */
	public function setContent($content=null){
		$params = array();
		if(!is_null($content)){
			foreach ($content as $key => &$val) {
		      if (is_array($val)){
		      	$val = implode(',', $val);
		      }
		      $params[] = $key.'='.urlencode($val);
		    }
		}
		if(!empty($params)){
	    	$this->CONTENT_STRING = implode('&', $params);
		}
	}
	
	/*
	 * Executes the request
	 */
	public function execute($url=null){
		try{
			if(!is_null($url)){
				 $segment = parse_url($url);
				 if(!($fs = @fsockopen( 
				 	$segment['host'], 
				 	isset($segment['port']) ? $segment['port'] : $this->SETTINGS['port'], 
				 	$errno, $errstr, $this->SETTINGS['timeout']
				 ))){
				 	throw new Exception("Could not connect to host (".$url.")");	
				 }
				 
				 $tpl = $this->SETTINGS['method']." ".$segment['path']." ".$this->SETTINGS['http_version'].self::EOL;
				 $tpl .= "Host: ".$segment['host'].self::EOL;
				 $tpl .= "Content-Type: ".$this->SETTINGS['content_type'].self::EOL;
				 $tpl .= "Content-Length: ".strlen($this->CONTENT_STRING).self::EOL;
				 
				 if(!empty($this->SETTINGS['headers'])){
					 foreach($this->SETTINGS['headers'] as $header){
					 	$tpl .= $header.self::EOL;
					 }
				 }
				 
				 $tpl .= "Connection: ".$this->SETTINGS['connection'].self::EOL.self::EOL;
				 
				 if(!is_null($this->CONTENT_STRING)){
				 	$tpl .= $this->CONTENT_STRING;
				 }
				 
				 fwrite($fs, $tpl);
		   		 fclose($fs);
		   		 return true;
			}else{
				throw new Exception("No URL was specified in fsock::execute($url)");
			}
		} catch (Exception $e){
			echo "fsocker->Exception: ".$e->getMessage();
			die;
		}
	}
	
	private static function filterSettings($settings=null){
		if(!is_null($settings)){
			try{
				if(isset($settings['method']) && !in_array($settings['method'],self::$ALLOWED_METHODS)){
					throw new Exception("Method not allowed (passed argument : ".$settings['method']."). Expects \"".implode(",",self::$ALLOWED_METHODS)."\"");
				}
			} catch (Exception $e) {
				echo "fsocker->Exception: ".$e->getMessage();
				die;
			}
		}
	}
}
?>
