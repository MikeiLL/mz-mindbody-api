<?php // This is already in the modal content window \\ ?>
    <table class="client-schedule">
        <?php foreach ($data->classes as $date => $class): ?>
            <?php mz_pr('hello'); ?>
            <?php mz_pr($class); ?>
        <?endforeach; ?>
    <?php foreach ($data->classes as $date => $class): ?>
        <tr>
            <td>
                <?php echo date_i18n($data->date_format, strtotime($date)); ?>
            </td>
            <td>
                <?php echo date_i18n($data->time_format, strtotime($class->startDateTime)) . ' - ' . date_i18n($data->time_format, strtotime($class->endDateTime)); ?>
            </td>
            <td>
                <?php echo $class->className . ' with ' . $class->staffName; ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </table>