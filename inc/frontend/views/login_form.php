<?php ?>

<form class="mz_mbo_login" method="POST">
    <input type="text" name="username" placeholder="<?php echo $data->username ?>" /><br class="btwn_mz_mbo_inputs"/>
    <input type="password" name="password" placeholder="<?php echo $data->password ?>" /><br class="btwn_mz_mbo_input_btns"/>
    <button type="submit"><?php echo $data->login ?></button><br class="btwn_mz_mbo_buttons" />
    <?php echo $data->or ?><a href="#" class="btn mz_add_to_class"><?php echo  $data->registration_button ?></a>
</form>