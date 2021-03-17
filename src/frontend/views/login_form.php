<?php

use MzMindbody\Inc\Core as Core;

?>

<div style="display:none" id="mzLogInContainer">
    <form role="form" class="form-group" style="margin:1em 0;" data-async id="mzLogIn" data-target="#mzSignUpModal" method="POST">

        <h2><?php echo $data->login_to_sign_up; ?></h2>

        <input type="hidden" name="nonce" value="<?php echo $data->signup_nonce; ?>"/>

        <input type="hidden" name="siteID" value="<?php echo $data->siteID; ?>" />

        <div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <label for="username">Username</label>

                <input type="text" size="10" class="form-control" id="username" name="username" placeholder="<?php echo $data->username ?>">

            </div>

        </div>

        <div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <label for="password">Password</label>

                <input type="password" size="10" class="form-control" name="password" id="password" placeholder="<?php echo $data->password ?>">

            </div>

        </div>

        <!--<div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <?php //if (Core\MzMindbodyApi::$advanced_options['allow_remember_me_cookie'] == 'on'): ?>
                    <div class="checkbox">

                        <label><input name="keep_me_logged_in" type="checkbox"> <?php //_e('Keep me logged in.', 'mz-mindbody-api'); ?> </label>

                    </div>
                <?php ///endif; ?>

            </div>

        </div>-->

        <div class="row" style="margin:.5em;">

            <div class="col-12">

                <button type="submit" class="btn btn-primary"><?php echo $data->login; ?></button>

                <a id="createMBOAccount" href="#" data-nonce="<?php echo $data->signup_nonce; ?>" data-classID="<?php echo $data->classID; ?>" class="btn btn-primary btn-xs"><?php echo $data->registration_button; ?></a>

                <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=1&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary btn-xs" id="MBOSite"><?php echo $data->manage_on_mbo; ?></a>

            </div>

        </div>

    </form>


    </form>
</div>