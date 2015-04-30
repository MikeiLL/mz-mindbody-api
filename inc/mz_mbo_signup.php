<?php
function mZ_mindbody_signup() {

	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
mz_pr($_POST);
if(!empty($_POST['website_url'])){
	echo "<h1>Die Robot Spam!</h1>";
	die();
	}
if(!empty($_POST['data']['Client'])) {

//mz_pr($_POST['data']['Client']['MobilePhone']);
if (isset($_POST['data']['Client']['BirthDate'])){
	$_POST['data']['Client']['BirthDate'] = date('c', strtotime($_POST['data']['Client']['BirthDate']));
	}
	
	$options = array(
		'Clients'=>array(
			'Client'=>$_POST['data']['Client']
		)
	);
	$signupData = $mb->AddOrUpdateClients($options);
	
	if($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Added') {
		$validateLogin = $mb->ValidateLogin(array(
			'Username' => $_POST['data']['Client']['Username'],
			'Password' => $_POST['data']['Client']['Password']
		));
		if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {
			$_SESSION['GUID'] = $validateLogin['ValidateLoginResult']['GUID'];
			$_SESSION['client'] = $validateLogin['ValidateLoginResult']['Client'];
		}
		echo "<h3>Congratulations. You are now logged in with your new Mindbody account.</h3><h2>Sign-up for some classes.</h2>";
		//header('location:index.php');
	}
}
$requiredFields = $mb->GetRequiredClientFields();

if(!empty($requiredFields['GetRequiredClientFieldsResult']['RequiredClientFields']['string'])) {
	$requiredFields = $mb->makeNumericArray($requiredFields['GetRequiredClientFieldsResult']['RequiredClientFields']['string']);
} else {
	$requiredFields = false;
}
$requiredFieldsInputs = '';
if(!empty($requiredFields)) {
	// Force single element $requiredFields into array form
	if (!is_array($requiredFields)){
		$requiredFields = array($requiredFields);
	}
	foreach($requiredFields as $field) {
		$requiredFieldsInputs .= "<label for='$field'>{$field}: </label><input type='text' name='data[Client][$field]' id='$field' placeholder='$field' required /><br />";
	}
}

echo "<h3>Sign Up</h3>";
if(!empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action']) && $signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Failed' && !empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'])) {
	foreach($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'] as $message) {
		echo "<pre>".print_r($message,1).'</pre><br />';
	}
}
echo <<<EOD
<form method="POST" style="line-height:2">
    <p class="website_url" style="display:none">Leave this empty-slash-blank: <input type="text" name="website_url" /></p>
	<label for="Username">Username: </label><input type="text" name="data[Client][Username]" id="Username" placeholder="Username" required /><br />
	<label for="Password">Password: </label><input type="password" name="data[Client][Password]" id="Password" placeholder="Password" required /><br />
	<label for="FirstName">First Name: </label><input type="text" name="data[Client][FirstName]" id="FirstName" placeholder="First Name" required /><br />
	<label for="LastName">Last Name: </label><input type="text" name="data[Client][LastName]" id="LastName" placeholder="Last Name" required /><br />
	$requiredFieldsInputs
	<button type="submit">Sign up</button>
</form>
EOD;
}
?>