<?php
class MB_API {
	protected $client;

	protected $appointmentServiceWSDL = "https://api.mindbodyonline.com/0_5/AppointmentService.asmx?WSDL";
	protected $classServiceWSDL = "https://api.mindbodyonline.com/0_5/ClassService.asmx?WSDL";
	protected $clientServiceWSDL = "https://api.mindbodyonline.com/0_5/ClientService.asmx?WSDL";
	protected $dataServiceWSDL = "https://api.mindbodyonline.com/0_5/DataService.asmx?WSDL";
	protected $finderServiceWSDL = "https://api.mindbodyonline.com/0_5/FinderService.asmx?WSDL";
	protected $saleServiceWSDL = "https://api.mindbodyonline.com/0_5/SaleService.asmx?WSDL";
	protected $siteServiceWSDL = "https://api.mindbodyonline.com/0_5/SiteService.asmx?WSDL";
	protected $staffServiceWSDL = "https://api.mindbodyonline.com/0_5/StaffService.asmx?WSDL";

	protected $apiMethods = array();
	protected $apiServices = array();

	public $soapOptions = array('soap_version'=>SOAP_1_1, 'trace'=>true);
	public $debugSoapErrors = true;
	
/*
	** initializes the apiServices and apiMethods arrays
	*/
	public function __construct($sourceCredentials = array()) {
		// set apiServices array with Mindbody WSDL locations
		$this->apiServices = array(
			'AppointmentService' => $this->appointmentServiceWSDL,
			'ClassService' => $this->classServiceWSDL,
			'ClientService' => $this->clientServiceWSDL,
			'DataService' => $this->dataServiceWSDL,
			'FinderService' => $this->finderServiceWSDL,
			'SaleService' => $this->saleServiceWSDL,
			'SiteService' => $this->siteServiceWSDL,
			'StaffService' => $this->staffServiceWSDL
		);
		// set apiMethods array with available methods from Mindbody services

		if (phpversion() >= 5.3) {
            include_once('php_variants/construct_newer.php');
        }else{
            include_once('php_variants/construct_older.php');
        }
		
			//mz_pr($this->apiMethods);
		// set sourceCredentials
		if(!empty($sourceCredentials)) {
			if(!empty($sourceCredentials['SourceName'])) {
				$this->sourceCredentials['SourceName'] = $sourceCredentials['SourceName'];
			}
			if(!empty($sourceCredentials['Password'])) {
				$this->sourceCredentials['Password'] = $sourceCredentials['Password'];
			}
			if(!empty($sourceCredentials['SiteIDs'])) {
				if(is_array($sourceCredentials['SiteIDs'])) {
					$this->sourceCredentials['SiteIDs'] = $sourceCredentials['SiteIDs'];
				} else if(is_numeric($sourceCredentials['SiteIDs'])) {
					$this->sourceCredentials['SiteIDs'] = array($sourceCredentials['SiteIDs']);
				}
			}
		}
	}

	/*
	** magic method will search $this->apiMethods array for $name and call the
	** appropriate Mindbody API method if found
	*/
	public function __call($name, $arguments) {
		// check if method exists on one of mindbody's soap services
		$soapService = false;
		foreach($this->apiMethods as $apiServiceName=>$apiMethods) {
			if(in_array($name, $apiMethods)) {
				$soapService = $apiServiceName;
			}
		}
		if(!empty($soapService)) {
			if(empty($arguments)) {
				return $this->callMindbodyService($soapService, $name);
			} else {
				switch(count($arguments)) {
					case 1:
						return $this->callMindbodyService($soapService, $name, $arguments[0]);
					case 2:
						return $this->callMindbodyService($soapService, $name, $arguments[0], $arguments[1]);
				}
			}
		} else {
			echo "called unknown method '$name'<br />";
		}
	}

	/*
	** return the results of a Mindbody API method
	**
	** string $serviceName   - Mindbody Soap service name
	** string $methodName    - Mindbody API method name
	** array $requestData    - Optional: parameters to API methods
	** boolean $returnObject - Optional: Return the SOAP response object
	*/
	protected function callMindbodyService($serviceName, $methodName, $requestData = array(), $returnObject = false, $debugErrors = false) {
		$request = array_merge(array("SourceCredentials"=>$this->sourceCredentials),$requestData);
		if(!empty($this->userCredentials)) {
			$request = array_merge(array("UserCredentials"=>$this->userCredentials), $request);
		}
		$this->client = new SoapClient($this->apiServices[$serviceName], $this->soapOptions);
		try {
			$result = $this->client->$methodName(array("Request"=>$request));
			if($returnObject) {
				return $result;
			} else {
				return json_decode(json_encode($result),1);
			}
		} catch (SoapFault $s) {
			if($this->debugSoapErrors && $debugErrors) {
				echo 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
				$this->debug();
				return false;
			}
		} catch (Exception $e) {
		    if($this->debugSoapErrors && $debugErrors) {
	    	    echo 'ERROR: ' . $e->getMessage();
	    	    return false;
	        }
		}
	}

	public function getXMLRequest() {
		return $this->client->__getLastRequest();
	}
	
	public function getXMLResponse() {
		return $this->client->__getLastResponse();
	}

	public function debug() {

		echo "<textarea rows='6' cols='90'>".print_r($this->getXMLRequest(),1)."</textarea>";
		echo "<br/>";
		echo "<textarea rows='6' cols='90'>".print_r($this->getXMLResponse(),1)."</textarea>";

	}

	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}

	public function replace_empty_arrays_with_nulls(array $array) {
		foreach($array as &$value) {
			if(is_array($value)) {
				if(empty($value)) {
					$value = null;
				} else {
					$value = $this->replace_empty_arrays_with_nulls($value);
				}
			}
		}
		return $array;
	}
	
  	/*
	** Used by versions of php < 5.3
	*/
    private function extract_client($n){
					$start = 1+strpos($n, ' ');
					$end = strpos($n, '(');
					$length = $end - $start;
					return substr($n, $start, $length);
				    }
				
  	/*
	** overrides SelectDataXml method to remove some invalid XML element names
	**
	** string $query - a TSQL query
	*/
	public function SelectDataXml($query, $returnObject = false, $debugErrors = false) {
		$result = $this->callMindbodyService('DataService', 'SelectDataXml', array('SelectSql'=>$query), $returnObject, $debugErrors);
		$xmlString = $this->getXMLResponse();
		// replace some invalid xml element names
		$xmlString = str_replace("Current Series", "CurrentSeries", $xmlString);
		$xmlString = str_replace("Item#", "ItemNum", $xmlString);
		$xmlString = str_replace("Massage Therapist", "MassageTherapist", $xmlString);
		$xmlString = str_replace("Workshop Instructor", "WorkshopInstructor", $xmlString);
		$sxe = new SimpleXMLElement($xmlString);
		$sxe->registerXPathNamespace("mindbody", "http://clients.mindbodyonline.com/api/0_5");
		$res = $sxe->xpath("//mindbody:SelectDataXmlResponse");
		if($returnObject) {
			return $res[0];
		} else {
		$arr = $this->replace_empty_arrays_with_nulls(json_decode(json_encode($res[0]),1));
		if(is_array($arr['FunctionDataXmlResult']['Results']['Row'])) {
			$arr['FunctionDataXmlResult']['Results']['Row'] = $this->makeNumericArray($arr['FunctionDataXmlResult']['Results']['Row']);
			}
			return $arr;
		}
	}
}

?>
