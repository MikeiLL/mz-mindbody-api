<div class="modal__footer">
    <?php mz_pr($data); ?>
    <a style="float:right;margin:0 1em" class="btn btn-primary" id="MBOLogout"><?php _e('Logout', 'mz-mindbody-api'); ?></a>
    <a style="float:right;margin:0 1em" href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=<?php echo $data->location; ?>&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary" id="MBOSite"><?php _e('Manage on Mindbody Site', 'mz-mindbody-api'); ?></a>
    <a style="float:right;margin:0 1em" class="btn btn-primary" data-nonce="<?php echo $data->nonce; ?>" id="MBOSchedule" target="_blank"><?php _e('My Classes', 'mz-mindbody-api'); ?></a>
</div>