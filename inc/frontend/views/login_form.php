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

            <?php if (Core\Init::$advanced_options['allow_remember_me_cookie'] == 'on'): ?>
            <div class="checkbox">

                <label><input name="keep_me_logged_in" type="checkbox"> <?php _e('Keep me logged in.', 'mz-mindbody-api'); ?> </label>

            </div>
            <?php endif; ?>

            <div class="modal__footer">

                <button type="submit" class="btn btn-primary"><?php echo $data->login ?></button> <?php echo $data->or ?>

                <a id="createMBOAccount" href="#" data-nonce="<?php echo $data->nonce ?>" data-classID="<?php echo $data->classID ?>" class="btn btn-primary mz_add_to_class"><?php echo $data->registration_button ?></a>

            </div>

        </form>

    </div>
</div>