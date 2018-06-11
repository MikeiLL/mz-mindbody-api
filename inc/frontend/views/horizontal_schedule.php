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
<?php if (empty($data->horizontal_schedule)) _e('Error Retrieving Schedule', 'mz-mindbody-api'); ?>
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
                <?php if ( !in_array('teacher', $data->hide ) ): ?>
                <th class="mz_staffName" scope="header">
                    <?php _e('Instructor', 'mz-mindbody-api'); ?>
                </th>
                <?php endif; ?>
                <?php if ( !in_array('session-type', $data->hide ) ): ?>
                <th class="mz_sessionTypeName" scope="header">
                    <?php _e('Class Type', 'mz-mindbody-api'); ?>
                </th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classes as $k => $class): ?>

            <tr class="mz_schedule_table mz_description_holder mz_location_<?php echo $class->sLoc . ' ' . $class->session_type_css . ' ' . $class->class_name_css; ?>">
                <td class="mz_date_display" data-time="<?php echo $class->startDateTime; ?>">
                    <?php echo date_i18n($data->time_format, strtotime($class->startDateTime)) . ' - ' . date_i18n($data->time_format, strtotime($class->endDateTime)); ?><br />
                    <span class="mz_hidden mz_time_of_day"><?php echo $class->part_of_day; ?></span>
                    <?php if ( !in_array('signup', $data->hide ) ): ?>
                    <a class="btn btn-primary" href="<?php echo $class->mbo_url; ?>" target="_blank"><?php _e('Sign-Up', 'mz-mindbody-api'); ?></a>
                    <?php endif; ?>
                </td>
                <td class="mz_classDetails">

                    <?php
                    $class->class_name_link->output();
                    ?>
                    <?php echo $class->displayCancelled; ?>

                </td>
                <?php if ( !in_array('teacher', $data->hide ) ): ?>
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
                <?php endif; ?>
                <?php if ( !in_array('session-type', $data->hide ) ): ?>
                <td class="mz_sessionTypeName">
                    <?php echo $class->sessionTypeName; ?>
                    <?php
                    // Display location if showing schedule for more than one location
                    if(count($data->locations_dictionary) >= 2):
                        _e('at', 'mz-mindbody-api');
                        echo ' ' . $data->locations_dictionary[$class->sLoc]['link'];
                    endif;
                    ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    <?php endforeach; ?>
</table>

