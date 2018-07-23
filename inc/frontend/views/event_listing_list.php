<?php
if (is_array($data->events)):

    foreach ($data->events as $key => $event):
        var_dump($event);
    endforeach;

else: ?>

<p><?php echo $data->events; ?></p>

<?php endif; ?>