<?php
class my_MB_API extends MB_API {
	protected $sourceCredentials = array(
		"SourceName"=>'REPLACE_WITH_YOUR_SOURCENAME', 
		"Password"=>'REPLACE_WITH_YOUR_PASSWORD', 
		"SiteIDs"=>array('REPLACE_WITH_YOUR_SITE_ID')
	);
	
    function __construct($sourceCredentials = array()) 
    {
        parent::__construct();
    }
    
        public function setCreds($sourceCredentials)
    {
        $this->sourceCredentials = $sourceCredentials;
    }

}
?>