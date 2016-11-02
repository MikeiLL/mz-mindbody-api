<?php
require_once('mz_activation.php');
function mz_mindbody_add_to_classes() {
	add_action('wp_loaded', 'initializeMBO');
	$clientIDs = array(100004590);
    $classIDs = array(20106);
    
	$additions['ClassIDs'] = $classIDs;
	$additions['ClientIDs'] = $clientIDs;
	$signupData = $mb->AddClientsToClasses($additions);
	echo "<pre>";
	print_r($signupData);
	echo "</pre>";
	//$mb->getXMLRequest();
	//$mb->getXMLResponse();
	$mb->debug();
	if ( $signupData['AddClientsToClassesResult']['ErrorCode'] != 200){
		echo "<h3>I'm sorry. We were unable to add you to the class.</h3>";
		foreach ($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client']['Messages'] as $message){
					echo "<h4>". $message ."</h4>";
			}
			echo "<pre>";
			echo getcwd() . "\n";
			
			echo "</pre>";
		}else{
			$classDetails = $signupData['AddClientsToClassesResult']['Classes']['Class'];
			echo $classDetails['ClassDescription']['Name'];
			echo "<hr>";
			echo $classDetails['Staff']['Name'];
			echo "<hr>";
			echo $classDetails['Location']['Name'];
			echo "<hr>";
			echo $classDetails['Location']['Address'];
		}
	}
?>
