<?php
function mZ_mindbody_login() {

require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

    if(!empty($_POST)) {
        $validateLogin = $mb->ValidateLogin(array(
            'Username' => $_POST['username'],
            'Password' => $_POST['password']
        ));
	
        if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {
            $_SESSION['GUID'] = $validateLogin['ValidateLoginResult']['GUID'];
            $_SESSION['client'] = $validateLogin['ValidateLoginResult']['Client'];
            displayWelcome();
        } else {
            if(!empty($validateLogin['ValidateLoginResult']['Message'])) {
                echo $validateLogin['ValidateLoginResult']['Message'];
            } else {
                echo "Invalid Login<br />";
            }
            displayLoginForm();
        }
    } else if(empty($_SESSION['GUID'])) {
        displayLoginForm();
    } else {
        displayWelcome();
    }

}

function displayLoginForm() {
	echo <<<EOD
<form method="POST">
	<input type="text" name="username" placeholder="username" />
	<input type="password" name="password" placeholder="password" />
	<button type="submit">Log in</button> or <a href="create_account">Register with MindBodyOnline</a>
</form>	
EOD;
}

function displayWelcome() {
	echo "Welcome ".$_SESSION['client']['FirstName'].' '.$_SESSION['client']['LastName'];
	echo "<br />";
	echo "<a href='logout'>Log out</a>";
	}
?>