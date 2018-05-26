<?php

use MZ_Mindbody\Inc\Core;
use MZ_Mindbody\Inc\Libraries as Libraries;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author    Mike iLL/mZoo.org
 */

?>

<table id="mz_horizontal_schedule" class="<?php echo $data->table_class; ?>">
    <?php foreach ($data->horizontal_schedule as $day => $classes): ?>
        <thead>
            <tr class="header visible striped" style="display: table-row">
                <th class="mz_date_display" scope="header">
                    <?php echo date_i18n($data->date_format, strtotime($day)); ?>
                </th>
                <th class="mz_classDetails" scope="header">
                    <?php _e('Class Name', 'mz-mindbody-api'); ?>
                </th>
                <th class="mz_staffName" scope="header">
                    <?php _e('Instructor', 'mz-mindbody-api'); ?>
                </th>
                <th class="mz_sessionTypeName" scope="header">
                    <?php _e('Class Type', 'mz-mindbody-api'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classes as $k => $class): ?>
            <tr>
                <td class="mz_date_display">
                    <?php echo date_i18n($data->time_format, strtotime($class->startDateTime)); ?><br />
                    <span class="mz_hidden mz_time_of_day"><?php echo $class->part_of_day; ?></span>
                    <a class="btn" href="<?php echo $class->mbo_url; ?>" target="_blank"><?php _e('Sign-Up', 'mz-mindbody-api'); ?></a>
                </td>
                <td class="mz_classDetails">

                    <?php
                    $linkArray = array(
                        'data-staffName' => $class->staffName,
                        'data-className' => $class->className,
                        'data-classDescription' => rawUrlEncode($class->classDescription),
                        'class' => 'modal-toggle mz_get_registrants ' . sanitize_html_class($class->className, 'mz_class_name'),
                        'text' => $class->className,
                        'data-target' => $data->data_target
                    );

                    if ($data->atts['show_registrants'] == 1) {
                        $get_registrants_nonce = wp_create_nonce('mz_MBO_get_registrants_nonce');
                        $linkArray['data-nonce'] = $get_registrants_nonce;
                        $linkArray['data-classID'] = $class->class_instance_ID;
                    }
                    if ($class->staffImage != ''):
                        $linkArray['data-staffImage'] = $class->staffImage;
                    endif;
                    $class_name_link = new Libraries\HTML_Element('a');
                    $class_name_link->set('href', $data->class_modal_link);
                    $class_name_link->set($linkArray);
                    $class_name_link->output();
                    ?>

                </td>
                <td class="mz_staffName">

                    <?php
                    $linkArray = array(

                        // 'data-accountNumber');
                        // "data-sub")
                        'data-staffName' => $class->staffName,
                        'data-staffID' => $class->staffID,
                        'class' => 'modal-toggle ' . sanitize_html_class($class->staffName, 'mz_staff_name'),
                        'text' => $class->staffName,
                        'data-target' => 'mzStaffScheduleModal',
                        'data-nonce' => wp_create_nonce('mz_staff_retrieve_nonce'),
                        'data-siteID' => $data->siteID
                    );

                    if ($class->staffImage != ''):
                        $linkArray['data-staffImage'] = $class->staffImage;
                    endif;

                    $class_name_link = new Libraries\HTML_Element('a');
                    $class_name_link->set('href', $data->class_modal_link);
                    $class_name_link->set($linkArray);
                    $class_name_link->output();
                    ?>
                </td>
                <td class="mz_sessionTypeName">
                    <?php echo $class->sessionTypeName; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    <?php endforeach; ?>
</table>

