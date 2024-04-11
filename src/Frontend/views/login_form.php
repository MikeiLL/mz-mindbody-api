<?php
/**
 * Login Form
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody\Core as Core;

?>

<div style="display:none" id="mzLogInContainer">
    <form role="form" class="form-group" style="margin:1em 0;" data-async id="mzLogIn" data-target="#mzSignUpModal" method="POST">

        <h2><?php echo $data->login_to_sign_up; ?></h2>

        <input type="hidden" name="nonce" value="<?php echo $data->signup_nonce; ?>"/>

        <input type="hidden" name="siteID" value="<?php echo $data->site_id; ?>" />

        <div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <label for="username">Username</label>

                <input type="text" size="10" class="form-control" id="username" name="username" placeholder="<?php echo $data->username; ?>">

            </div>

        </div>

        <div class="row">

            <div class="form-group col-xs-8 col-sm-6">

                <label for="password">Password</label>

                <input type="password" size="10" class="form-control" name="password" id="password" placeholder="<?php echo $data->password; ?>">

            </div>

        </div>

        <div class="row" style="margin:.5em;">

            <div class="col-12">

                <button type="submit" class="btn btn-primary"><?php echo $data->login; ?></button>
                    <?php echo esc_html( $data->login ); ?>
                </button>

                <a id="createMBOAccount" href="#" data-nonce="<?php echo $data->signup_nonce; ?>" data-classID="<?php echo $data->class_id; ?>" class="btn btn-primary btn-xs"><?php echo $data->registration_button; ?></a>
                    href="#"
                    data-nonce="<?php echo esc_html( $data->signup_nonce ); ?>"
                    data-classID="<?php echo esc_html( $data->class_id ); ?>"
                    class="btn btn-primary btn-xs">
                    <?php echo esc_html( $data->registration_button ); ?>
                </a>

                <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=1&studioid=<?php echo $data->site_id; ?>" class="btn btn-primary btn-xs" id="MBOSite"><?php echo $data->manage_on_mbo; ?></a>
                $mbo_link = 'https://clients.mindbodyonline.com/ws.asp';
                $mbo_link .= '?&amp;sLoc=' . $data->location;
                $mbo_link .= '&studioid=' . $data->site_id;
                ?>

                <a href="<?php echo esc_html( $mbo_link ); ?>"
                    class="btn btn-primary btn-xs"
                    style="text-decoration:none;" id="MBOSite">
                    <?php echo esc_html( $data->manage_on_mbo ); ?>
                </a>

            </div>

        </div>

    </form>


    </form>
</div>
