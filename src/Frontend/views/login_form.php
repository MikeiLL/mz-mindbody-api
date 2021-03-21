<?php

use MZoo\MzMindbody\Core as Core;

?>

<div style="display:none" id="mzLogInContainer">
    <form role="form" class="form-group" style="margin:1em 0;" data-async id="mzLogIn" data-target="#mzSignUpModal" method="POST">

        <h2><?php esc_html_e($data->login_to_sign_up); ?></h2>

        <input type="hidden" name="nonce" value="<?php esc_html_e($data->signup_nonce); ?>"/>

        <input type="hidden" name="siteID" value="<?php esc_html_e($data->siteID); ?>" />

        <div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <label for="username">Username</label>

                <input type="text" size="10" class="form-control" id="username" name="username" placeholder="<?php esc_html_e($data->username); ?>">

            </div>

        </div>

        <div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <label for="password">Password</label>

                <input type="password" size="10" class="form-control" name="password" id="password" placeholder="<?php esc_html_e($data->password); ?>">

            </div>

        </div>

        <!--<div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <?php // if (Core\MzMindbodyApi::$advanced_options['allow_remember_me_cookie'] == 'on'): ?>
                    <div class="checkbox">

                        <label><input name="keep_me_logged_in" type="checkbox"> <?php // esc_html_e('Keep me logged in.', 'mz-mindbody-api'); ?> </label>

                    </div>
                <?php // endif; ?>

            </div>

        </div>-->

        <div class="row" style="margin:.5em;">

            <div class="col-12">

                <button type="submit" class="btn btn-primary">
                    <?php esc_html_e($data->login); ?>
                </button>

                <a id="createMBOAccount" 
                    href="#" 
                    data-nonce="<?php esc_html_e($data->signup_nonce); ?>" 
                    data-classID="<?php esc_html_e($data->classID); ?>" 
                    class="btn btn-primary btn-xs">
                    <?php esc_html_e($data->registration_button); ?>
                </a>
                
                <?php 
                $mbo_link = 'https://clients.mindbodyonline.com/ws.asp';
                $mbo_link .= '?&amp;sLoc=' . $data->location; 
                $mbo_link .= '&studioid=' . $data->siteID;
                ?>
                
                <a href="<?php esc_html_e($mbo_link); ?>" 
                    class="btn btn-primary btn-xs" 
                    style="text-decoration:none;" id="MBOSite">
                    <?php esc_html_e($data->manage_on_mbo); ?>
                </a>

            </div>

        </div>

    </form>


    </form>
</div>
