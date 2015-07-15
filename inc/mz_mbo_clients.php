<?php

class MZ_MBO_Clients {

	public function mZ_mindbody_login() {

	$mb = instantiate_mbo_API();

		if(!empty($_POST)) {
			$validateLogin = $mb->ValidateLogin(array(
				'Username' => $_POST['username'],
				'Password' => $_POST['password']
			));
	
			if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {
				$_SESSION['GUID'] = $validateLogin['ValidateLoginResult']['GUID'];
				$_SESSION['client'] = $validateLogin['ValidateLoginResult']['Client'];
				$this->displayWelcome();
			} else {
				if(!empty($validateLogin['ValidateLoginResult']['Message'])) {
					echo $validateLogin['ValidateLoginResult']['Message'];
				} else {
					_e('Invalid Login', 'mz-mindbody-api');
					echo '<br />';
				}
				return $this->displayLoginForm();
			}
		} else if(empty($_SESSION['GUID'])) {
			return $this->displayLoginForm();
		} else {
			return $this->displayWelcome();
		}

	}

	public function displayLoginForm() {
		$globals = new Global_Strings();
		$global_strings = $globals->translate_them();
		$password = $global_strings['password'];
		$username = $global_strings['username'];
		$login = $global_strings['login'];
		$create_account_url = home_url().'/'.$global_strings['create_account_url'];
		$registration_button = __('Register with MindBodyOnline', 'mz-mindbody-api');
		$or = $global_strings['or'];
		return <<<EOD
	<form class="mz_mbo_login" method="POST">
		<input type="text" name="username" placeholder="$username" /><br class="btwn_mz_mbo_inputs"/>
		<input type="password" name="password" placeholder="$password" /><br class="btwn_mz_mbo_input_btns"/>
		<button type="submit">$login</button><br class="btwn_mz_mbo_buttons" /> 
		$or <a href="$create_account_url" class="btn mz_add_to_class">$registration_button</a>
	</form>	
EOD;
	}

	private function displayWelcome() {
		$globals = new Global_Strings();
		$global_strings = $globals->translate_them();
		$logout = $global_strings['logout'];
		$logout_url = $global_strings['logout_url'];
		echo '<h3>'.__('Welcome', 'mz-mindbody-api').'&nbsp; '.$_SESSION['client']['FirstName'].' '.$_SESSION['client']['LastName'].'<h3>';
		echo '<br />';
		echo '<a href="'.home_url().'/'.$logout_url.'" class="btn mz_add_to_class">'.$logout.'</a>';
		}
		
	public function mZ_mindbody_logout() {
		if (phpversion() >= 5.4) {
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
					}
				}else{
				if(!session_id()) {
					session_start();
					}
				}
		foreach($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		return $this->displayConfirmation();
	}


	private function displayConfirmation() {
		$globals = new Global_Strings();
		$global_strings = $globals->translate_them();
		$login_url = $global_strings['login_url'];
		$login = $global_strings['login'];
		$return = '<h3>'.__('User Logged Out.', 'mz-mindbody-api').'</h3>';
		$return .= '<br/>';
		$return .= '<a href="'.home_url().'/'.$login_url.'" class="btn mz_add_to_class">'.$login.'</a>';
		return $return;
		}
		
	public function mZ_mindbody_signup() {
	
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
	
	$mb = instantiate_mbo_API();
	
	$requiredFields = $mb->GetRequiredClientFields();

	if(!empty($requiredFields['GetRequiredClientFieldsResult']['RequiredClientFields']['string'])) {
		$requiredFields = $this->makeNumericArray($requiredFields['GetRequiredClientFieldsResult']['RequiredClientFields']['string']);
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

	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}
		
}//EOF MZ_MBO_Clients
?>