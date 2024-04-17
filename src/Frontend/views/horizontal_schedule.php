<?php
/**
 * Template to isplay schedule in horizontal format
 *
 * May be loaded along with grid schedule to be swapped via DOM request
 *
 * @link  http://mzoo.org
 * @since 2.4.7
 * @package MzMindbody
 *
 * @author Mike iLL/mZoo.org
 */

use MZoo\MzMindbody\Core;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody as NS;

?>
<?php
if ( empty( $data->horizontal_schedule ) ) {
    echo sprintf(
        // translators: Give a start and end date range for displayed classes.
        __( 'No Classes To Display (%1$s - %2$s)', 'mz-mindbody-api' ),
        gmdate( $data->date_format, $data->start_date->getTimestamp() ),
        gmdate( $data->date_format, $data->end_date->getTimestamp() )
    );
}
?>
<table id="mz_horizontal_schedule" class="<?php echo $data->table_class; ?>">
    <?php foreach ( $data->horizontal_schedule as $day => $classes ) : ?>
        <thead>
            <tr class="header visible striped" style="display: table-row">
                <th class="mz_date_display" scope="header">
        <?php echo gmdate( $data->date_format, strtotime( $day ) ); ?>
                </th>
                <th class="mz_classDetails" scope="header">
        <?php esc_html_e( 'Class Name', 'mz-mindbody-api' ); ?>
                </th>
        <?php if ( ! in_array( 'teacher', $data->hide, true ) ) : ?>
                <th class="mz_staffName" scope="header">
            <?php esc_html_e( 'Instructor', 'mz-mindbody-api' ); ?>
                </th>
        <?php endif; ?>
        <?php if ( ! in_array( 'session-type', $data->hide, true ) ) : ?>
                <th class="mz_sessionTypeName" scope="header">
            <?php esc_html_e( 'Class Type', 'mz-mindbody-api' ); ?>
                </th>
        <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if ( ! empty( $classes ) ) : ?>
            <?php foreach ( $classes as $k => $class ) : ?>
            <tr class="mz_schedule_table mz_description_holder mz_location_<?php echo $class->studio_location_id . ' ' . $class->session_type_css . ' ' . $class->class_name_css; ?>">
                <td class="mz_date_display" data-time="<?php echo $class->start_datetime; ?>">
                <?php echo gmdate( $data->time_format, strtotime( $class->start_datetime ) ) . ' - ' . gmdate( $data->time_format, strtotime( $class->end_datetime ) ); ?><br />
                    <span class="mz_hidden mz_time_of_day"><?php echo $class->part_of_day; ?></span>
                <?php if ( ! in_array( 'signup', $data->hide, true ) ) : ?>
                    <?php $class->sign_up_link->output(); ?>
                <?php endif; ?>
                </td>
                <td class="mz_classDetails">

                <?php
                $class->class_name_link->output();
                ?>
                <?php echo $class->display_cancelled; ?>

                </td>
                <?php if ( ! in_array( 'teacher', $data->hide, true ) ) : ?>
                <td class="mz_staffName">

                    <?php
                    $class->staff_name_link->output();
                    ?>
                </td>
                <?php endif; ?>
                <?php if ( ! in_array( 'session-type', $data->hide, true ) ) : ?>
                <td class="mz_sessionTypeName">
                    <?php echo $class->session_type_name; ?>
                    <?php
                    // Display location if showing schedule for more than one location.
                    if ( count( $data->locations_dictionary ) >= 2 ) :
                        esc_html_e( 'at', 'mz-mindbody-api' );
                        echo ' ' . $data->locations_dictionary[ $class->studio_location_id ]['link'];
                    endif;
                    ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>

        <?php else : ?>
            <tr class="mz_schedule_table mz_description_holder mz_description_holder_empty">
                <td class="mz_date_display">

                </td>
                <td class="mz_classDetails">

                </td>
            <?php if ( ! in_array( 'teacher', $data->hide, true ) ) : ?>
                <td class="mz_staffName">

                </td>
            <?php endif; ?>
            <?php if ( ! in_array( 'session-type', $data->hide, true ) ) : ?>
                <td class="mz_sessionTypeName">

                </td>
            <?php endif; ?>
            </tr>

        <?php endif; // if $classes or else block. ?>

        </tbody>
    <?php endforeach; ?>
</table>
