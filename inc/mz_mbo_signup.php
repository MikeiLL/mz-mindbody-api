<?php
function mZ_mindbody_signup() {

	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

if(!empty($_POST['website_url'])){
	echo '<h1>'. __('Die Robot Spam!', 'mz-mindbody-api') . '</h1>';
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
		echo '<h3>' . __('Congratulations. You are now logged in with your new Mindbody account.', 'mz-mindbody-api') . '</h3>';
		echo '<h2>' . __('Sign-up for some classes.', 'mz-mindbody-api') . '</h2>';
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
		$requiredFieldsInputs .= "<label for='$field'>{$field} </label><input type='text' name='data[Client][$field]' id='$field' placeholder='$field' required /><br />";
	}
}

if(!empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action']) && $signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Failed' && !empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'])) {
	foreach($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'] as $message) {
		echo "<pre>".print_r($message,1).'</pre><br />';
	}
}


$globals = new Global_Strings();
$global_strings = $globals->translate_them();
$password = $global_strings['password'];
$username = $global_strings['username'];
$antispam = __('Leave this empty-slash-blank', 'mz-mindbody-api');
$firstname = __('First Name', 'mz-mindbody-api');
$lastname = __('Last Name', 'mz-mindbody-api');
$sign_up = __('Sign up', 'mz-mindbody-api');
return <<<EOD
<form  class="mz_mbo_signup" method="POST">
    <p class="website_url" style="display:none">$antispam<input type="text" name="website_url" /></p>
	<label for="Username"> $username</label><input type="text" name="data[Client][Username]" id="Username" placeholder="$username" required /><br />
	<br/><label for="Password"> $password</label><input type="password" name="data[Client][Password]" id="Password" placeholder="$password" required /><br />
	<br/><label for="FirstName"> $firstname</label><input type="text" name="data[Client][FirstName]" id="FirstName" placeholder="$firstname" required /><br />
	<br/><label for="LastName"> $lastname</label><input type="text" name="data[Client][LastName]" id="LastName" placeholder="$lastname" required /><br />
	$requiredFieldsInputs
	<button type="submit">$sign_up</button>
</form>
EOD;
}
?>