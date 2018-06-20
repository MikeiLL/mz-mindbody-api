<?php ?>


<div style="max-width:90%;margin: 0 auto;">

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

    <div class="checkbox">

        <label><input name="remember_me" type="checkbox"> <?php _e('Keep me logged in.', 'mz-mindbody-api'); ?> </label>

    </div>

    <button type="submit" class="btn btn-primary"><?php echo $data->login ?></button> <?php echo $data->or ?>

    </form>

    <a id="createMBOAccount" href="#" data-nonce="<?php echo $data->nonce ?>" data-classID="<?php echo $data->classID ?>" class="btn btn-primary mz_add_to_class"><?php echo $data->registration_button ?></a>

</div>