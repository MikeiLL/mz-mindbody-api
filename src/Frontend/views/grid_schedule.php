<?php
/**
 * Template to display schedule in grid format
 *
 * May be loaded along with horizontal schedule to be swapped via DOM request
 *
 * @package MzMindbody
 * @since 2.4.7
 *
 * @author Mike iLL/mZoo.org
 */

use MZoo\MzMindbody\Core;
use MZoo\MzMindbody\Libraries as Libraries;

?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" style="width:0;height:0;position:absolute;overflow:hidden;">
    <defs>
        <symbol viewBox="0 0 1300 1100" aria-labelledby="essi-bootstrap-log-in-title" id="si-bootstrap-log-in">
            <title id="essi-bootstrap-log-in-title">icon log-in</title>
            <path d="M550 0h400q165 0 257.5 92.5T1300 350v400q0 165-92.5 257.5T950 1100H550q-21 0-35.5-14.5T500 1050V950q0-21 14.5-35.5T550 900h450q41 0 70.5-29.5T1100 800V300q0-41-29.5-70.5T1000 200H550q-21 0-35.5-14.5T500 150V50q0-21 14.5-35.5T550 0zM338 233l324 284q16 14 16 33t-16 33L338 867q-16 14-27 9t-11-26V700H50q-21 0-35.5-14.5T0 650V450q0-21 14.5-35.5T50 400h250V250q0-21 11-26t27 9z"/>
        </symbol>
        <symbol viewBox="0 0 1218 1012" aria-labelledby="fpsi-bootstrap-ok-title" id="si-bootstrap-ok"><title id="fpsi-bootstrap-ok-title">icon ok</title><path d="M427 579L998 8q8-8 18-8t17 8l177 177q8 7 8 17t-8 18l-783 784q-7 8-17.5 8t-17.5-8L8 620q-8-8-8-18t8-17l177-177q7-8 17-8t18 8l171 171q7 7 18 7t18-7z"/></symbol>
        <symbol viewBox="0 0 1203 1216.333251953125" aria-labelledby="jusi-bootstrap-wrench-title" id="si-bootstrap-wrench"><title id="jusi-bootstrap-wrench-title">icon wrench</title><path d="M756 61.333q164-92 306 9l-259 138 145 232 251-126q6 89-34 156.5t-117 110.5q-60 34-127 39.5t-126-16.5l-596 596q-15 16-36.5 16t-36.5-16l-111-110q-15-15-15-36.5t15-37.5l600-599q-34-101 5.5-201.5T756 61.333z"/></symbol>
    </defs>
</svg>
<style type="text/css">
    .icon {width:12px;height:12px;fill:#959ea9}
    a, a.icon-link, a.icon-link:hover, a.icon-link:visited, a.icon-link:link, a.icon-link:active { text-decoration:none !important;}
    a.icon-link:hover .icon {fill: indianRed}
</style>
<h4 class="mz_grid_date">
    <?php
    $this_week_start = gmdate( $data->date_format, $data->start_date->getTimestamp() );
    ?>
    <?php
    printf(
            // translators: What is date of start of currently displayed week.
        __( 'Week of %1$s', 'mz-mindbody-api' ),
        $this_week_start
    );
    ?>
</h4>
<?php
if ( empty( $data->grid_schedule ) ) {
    echo sprintf(
        // translators: Give a start and end date range for displayed classes.
        __( 'No Classes To Display (%1$s - %2$s)', 'mz-mindbody-api' ),
        gmdate( $data->date_format, $data->start_date->getTimestamp() ),
        gmdate( $data->date_format, $data->end_date->getTimestamp() )
    );
}
?>
<table class="<?php echo $data->table_class; ?>">
    <thead>
        <tr>
            <th scope="header"></th>
            <?php foreach ( $data->week_names as $name ) : ?>
                <th scope="header"><?php echo $name; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $data->grid_schedule as $time => $days ) : ?>
        <tr>
            <td class="mz_hidden"><?php echo $days['part_of_day']; ?></td>
            <td><?php echo $days['display_time']; ?></td>
            <?php foreach ( $days['classes'] as $day_slot ) : ?>
                <td><?php $classes = count( $day_slot ); ?>
                <?php
                foreach ( $day_slot as $class ) :
                    $classes--;
                    ?>
                    <div class="mz_schedule_table mz_description_holder mz_location_<?php echo $class->studio_location_id . ' ' . $class->session_type_css . ' ' . $class->class_name_css; ?>">
                    <?php $class->class_name_link->output(); ?>&nbsp;
                    <?php
                    if ( ! in_array( 'teacher', $data->hide, true ) ) :
                        esc_html_e( 'with', 'mz-mindbody-api' );
                        ?>
                        <?php

                        $class->staff_name_link->output();
                    endif;
                    ?>
                    <?php echo $class->display_cancelled; ?>
                        <br />
                    <?php
                    if ( ! in_array( 'signup', $data->hide, true ) ) :
                        ?>
                        <?php $class->grid_sign_up_link->output(); ?><br/>
                    <?php endif; ?>
                    <?php
                    if ( ! in_array( 'duration', $data->hide, true ) ) :
                        esc_html_e( 'Duration:', 'mz-mindbody-api' );
                        ?>
                            &nbsp;
                        <?php
                        echo $class->class_duration->format( '%H:%I' );
                    endif;
                    ?>
                    <?php
                    if ( ! in_array( 'location', $data->hide, true ) ) :
                        // Display location if showing schedule for more than one location.
                        if ( count( $data->locations_dictionary ) >= 2 ) :
                                echo '<br/>' . $data->locations_dictionary[ $class->studio_location_id ]['link'];
                        endif;
                    endif;
                    ?>
                    </div>
                    <?php if ( $classes >= 1 ) : ?>
                    <hr/>
                    <?php endif; ?>
                <?php endforeach; ?>
                </td>
            <?php endforeach; ?>

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
