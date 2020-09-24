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
<?php if (empty($data->horizontal_schedule)) echo sprintf(__('No Classes To Display (%1$s - %2$s)', 'mz-mindbody-api'), date_i18n($data->date_format, $data->start_date->getTimestamp()), date_i18n($data->date_format, $data->end_date->getTimestamp())); ?>
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
        <?php if (!empty($classes)): ?>
        <?php foreach ($classes as $k => $class): ?>

            <tr class="mz_schedule_table mz_description_holder mz_location_<?php echo $class->sLoc . ' ' . $class->session_type_css . ' ' . $class->class_name_css; ?>">
                <td class="mz_date_display" data-time="<?php echo $class->startDateTime; ?>">
                    <?php echo date_i18n($data->time_format, strtotime($class->startDateTime)) . ' - ' . date_i18n($data->time_format, strtotime($class->endDateTime)); ?><br />
                    <span class="mz_hidden mz_time_of_day"><?php echo $class->part_of_day; ?></span>
                    <?php if ( !in_array('signup', $data->hide ) ): ?>
                    <?php $class->sign_up_link->output(); ?>
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
                    $class->staff_name_link->output();
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
        
        <?php else: ?>
        
            <tr class="mz_schedule_table mz_description_holder mz_description_holder_empty">
                <td class="mz_date_display">
                
                </td>
                <td class="mz_classDetails">
                
                </td>
                <?php if ( !in_array('teacher', $data->hide ) ): ?>
                <td class="mz_staffName">
                
                </td>
                <?php endif; ?>
                <?php if ( !in_array('session-type', $data->hide ) ): ?>
                <td class="mz_sessionTypeName">
                    
                </td>
                <?php endif; ?>
            </tr>
            
        <?php endif; // if $classes or else block ?>
        
        </tbody>
    <?php endforeach; ?>
</table>
