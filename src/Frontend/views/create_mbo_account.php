
<div style="max-width:90%;margin: 0 auto;">

    <h3><?php esc_html_e('Create MBO Account', 'mz-mindbody-api'); ?></h3>

    <form style="margin:1em 0;" class="mz_mbo_signup" data-async id="mzSignUp" data-target="#mzSignUpModal" method="POST">

            <p class="website_url" style="display:none"><?php esc_html_e($antispam); ?><input type="text" name="website_url" /></p>
            <input type="hidden" name="nonce" value="<?php esc_html_e($data->nonce); ?>"/>
            <input type="hidden" name="classID" value="<?php esc_html_e($data->classID); ?>" />
            <label for="Username"> <?php esc_html_e($data->username; ?></label> <input type="text" name="data[Client][Username]" id="Username" placeholder="<?php echo $data->username); ?>" required /><br />
            <label for="Email"> <?php esc_html_e($data->clientemail; ?></label> <input type="text" name="data[Client][Email]" id="Email" placeholder="<?php echo $data->clientemail); ?>" required /><br />
            <label for="Password"> <?php esc_html_e($data->password; ?></label> <input type="password" name="data[Client][Password]" id="Password" placeholder="<?php echo $data->password); ?>" required /><br />
            <label for="FirstName"> <?php esc_html_e($data->firstname; ?></label> <input type="text" name="data[Client][FirstName]" id="FirstName" placeholder="<?php echo $data->firstname); ?>" required /><br />
            <label for="LastName"> <?php esc_html_e($data->lastname; ?></label> <input type="text" name="data[Client][LastName]" id="LastName" placeholder="<?php echo $data->lastname); ?>" required /><br />
            <?php esc_html_e($data->requiredFieldsInputs); ?><br />
            <button type="submit" class="btn btn-primary"><?php esc_html_e($data->sign_up); ?></button>

    </form>


</div>
