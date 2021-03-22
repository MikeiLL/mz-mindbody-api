<?php

use MZoo\MzMindbody as NS;

?>
<div class="modal__footer btn-group" class="signupModalFooter">
    <a class="btn btn-primary" data-nonce="<?php esc_html_e($data->nonce); ?>" 
        id="MBOSchedule" target="_blank">
            <?php esc_html_e('My Classes', 'mz-mindbody-api'); ?>
    </a>
    <?php 
    $mbo_link = 'https://clients.mindbodyonline.com/ws.asp';
    $mbo_link .= '?&amp;sLoc=' . $data->location; 
    $mbo_link .= '&studioid=' . $data->site_id;
    ?>
    <a href="<?php esc_html_e($mbo_link); ?>" class="btn btn-primary btn-xs" 
        id="MBOSite">
        <?php esc_html_e('Manage on Mindbody Site', 'mz-mindbody-api'); ?>
    </a>
    <a class="btn btn-primary btn-xs" id="MBOLogout" 
        data-nonce="<?php esc_html_e($data->nonce); ?>">
        <?php esc_html_e('Logout', 'mz-mindbody-api'); ?>
    </a>
</div>
