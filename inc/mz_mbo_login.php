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
        return displayLoginForm();
    } else {
        return displayWelcome();
    }

}

function displayLoginForm() {
$create_account_url = home_url()."/create-account";
	return <<<EOD
<form class="mz_mbo_login" method="POST">
	<input type="text" name="username" placeholder="username" /><br class="btwn_mz_mbo_inputs"/>
	<input type="password" name="password" placeholder="password" /><br class="btwn_mz_mbo_input_btns"/>
	<button type="submit">Log in</button><br class="btwn_mz_mbo_buttons" /> 
	or <a href="$create_account_url" class="btn mz_add_to_class">Register with MindBodyOnline</a>
</form>	
EOD;
}

function displayWelcome() {
	echo "<h3>Welcome ".$_SESSION['client']['FirstName'].' '.$_SESSION['client']['LastName']."<h3>";
	echo "<br />";
	echo "<a href='".home_url()."/logout' class='btn mz_add_to_class'>Log out</a>";
	}
?>