<?php
use MZ_Mindbody\Inc\Core;

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

<?php
// How many columns do we need?
$key = reset($data->horizontal_schedule);
mz_pr(array_filter ((array) (array_shift($key)))); ?>
<table>
    <?php foreach ($data->horizontal_schedule as $day => $classes): ?>
    <tr class="header visible striped" style="display: table-row">
        <th class="mz_date_display" scope="header">
            <?php echo date_i18n($data->date_format, strtotime($day)); ?>
        </th>
        <th class="mz_classDetails" scope="header">
            <?php  _e('Class Name', 'mz-mindbody-api'); ?>
        </th>
        <th class="mz_staffName" scope="header">
            <?php  _e('Instructor', 'mz-mindbody-api'); ?>
        </th>
        <th class="mz_sessionTypeName" scope="header">
            <?php  _e('Class Type', 'mz-mindbody-api'); ?>
        </th>
    </tr>
    <tbody>
        <?php foreach ($classes as $k => $class): ?>
        <tr>
            <td class="mz_date_display">
                <?php echo date_i18n($data->time_format, strtotime($class->startDateTime)); ?>
            </td>
            <td class="mz_classDetails">
                <?php echo $class->className; ?>
            </td>
            <td class="mz_staffName">
                <?php echo $class->staffName; ?>
            </td>
            <td class="mz_sessionTypeName">
                <?php echo $class->sessionTypeName; ?>
            </td>
        </tr>
    </tbody>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>

