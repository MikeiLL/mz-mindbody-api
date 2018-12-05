<?php
use MZ_Mindbody as NS;
?>
<div class="modal__footer btn-group" class="signupModalFooter">
    <a class="btn btn-primary" data-nonce="<?php echo $data->nonce; ?>" id="MBOSchedule" target="_blank"><?php _e('My Classes', 'mz-mindbody-api'); ?></a>
    <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=<?php echo $data->location; ?>&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary btn-xs" id="MBOSite"><?php _e('Manage on Mindbody Site', 'mz-mindbody-api'); ?></a>
    <a class="btn btn-primary btn-xs" id="MBOLogout" data-nonce="<?php echo $data->nonce; ?>"><?php _e('Logout', 'mz-mindbody-api'); ?></a>
</div>
