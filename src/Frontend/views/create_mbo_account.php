<?php
/**
 * Create MBO Account Form.
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

?>
<div style="max-width:90%;margin: 0 auto;">

    <h3><?php esc_html_e( 'Create MBO Account', 'mz-mindbody-api' ); ?></h3>

    <form style="margin:1em 0;" class="mz_mbo_signup" data-async id="mzSignUp" data-target="#mzSignUpModal" method="POST">

            <p class="website_url" style="display:none"><?php echo $antispam; ?><input type="text" name="website_url" /></p>
            <input type="hidden" name="nonce" value="<?php echo $data->nonce; ?>"/>
            <input type="hidden" name="classID" value="<?php echo $data->class_id; ?>" />
            <label for="Username"> <?php echo $data->username; ?></label> <input type="text" name="data[Client][Username]" id="Username" placeholder="<?php echo $data->username; ?>" required /><br />
            <label for="Email"> <?php echo $data->clientemail; ?></label> <input type="text" name="data[Client][Email]" id="Email" placeholder="<?php echo $data->clientemail; ?>" required /><br />
            <label for="Password"> <?php echo $data->password; ?></label> <input type="password" name="data[Client][Password]" id="Password" placeholder="<?php echo $data->password; ?>" required /><br />
            <label for="FirstName"> <?php echo $data->firstname; ?></label> <input type="text" name="data[Client][FirstName]" id="FirstName" placeholder="<?php echo $data->firstname; ?>" required /><br />
            <label for="LastName"> <?php echo $data->lastname; ?></label> <input type="text" name="data[Client][LastName]" id="LastName" placeholder="<?php echo $data->lastname; ?>" required /><br />
            <?php echo esc_html( $data->required_fields_inputs ); ?><br />
            <button type="submit" class="btn btn-primary"><?php echo $data->sign_up; ?></button>
                <?php echo esc_html( $data->clientemail ); ?>
            </label>
            <input type="text" name="data[Client][Email]" id="Email"
                placeholder="<?php echo esc_html( $data->clientemail ); ?>" required />
            <br />
            <label for="Password">
                <?php echo esc_html( $data->password ); ?>
            </label>
            <input type="password" name="data[Client][Password]" id="Password"
                placeholder="<?php echo esc_html( $data->password ); ?>" required />
            <br />
            <label for="FirstName">
                <?php echo esc_html( $data->firstname ); ?>
            </label>
            <input type="text" name="data[Client][FirstName]" id="FirstName"
                placeholder="<?php echo esc_html( $data->firstname ); ?>" required />
            <br />
            <label for="LastName">
                <?php echo esc_html( $data->lastname ); ?>
            </label>
            <input type="text" name="data[Client][LastName]" id="LastName"
                placeholder="<?php echo esc_html( $data->lastname ); ?>" required />
            <br />
            <?php echo esc_html( $data->required_fields_inputs ); ?>
            <br />
            <button type="submit" class="btn btn-primary">
                <?php echo esc_html( $data->sign_up ); ?>
            </button>

    </form>


</div>
