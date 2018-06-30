<?php
use MZ_Mindbody\Inc\Core as Core;
?>


<div class="modal__wrapper">
    <div class="modal__content">

        <h3><?php _e('Sign-In', 'mz-mindbody-api'); ?></h3>

        <form style="margin:1em 0;" data-async id="mzLogIn" data-target="#mzSignUpModal" method="POST">

            <input type="hidden" name="nonce" value="<?php echo $data->nonce; ?>"/>

            <input type="hidden" name="classID" value="<?php echo $data->classID; ?>" />

            <div class="form-group">

            <label for="username">Username</label>

            <input type="text" class="form-control" id="username" name="username" placeholder="<?php echo $data->username ?>">

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input type="password" class="form-control" name="password" id="password" placeholder="<?php echo $data->password ?>">

            </div>

            <?php if (Core\MZ_Mindbody_Api::$advanced_options['allow_remember_me_cookie'] == 'on'): ?>
            <div class="checkbox">

                <label><input name="keep_me_logged_in" type="checkbox"> <?php _e('Keep me logged in.', 'mz-mindbody-api'); ?> </label>

            </div>
            <?php endif; ?>

            <div class="modal__footer">

                <a style="float:right;margin:0 1em" href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=<?php echo $data->location; ?>&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary" id="MBOSite"><?php _e('Manage on Mindbody Site', 'mz-mindbody-api'); ?></a>

                <a style="float:right;" id="createMBOAccount" href="#" data-nonce="<?php echo $data->nonce ?>" data-classID="<?php echo $data->classID ?>" class="btn btn-primary"><?php echo $data->registration_button ?></a>

                <button style="float:right;" type="submit" class="btn btn-primary"><?php echo $data->login ?></button> <?php echo $data->or ?>

            </div>

        </form>

    </div>
</div>