<?php
use MZ_Mindbody\Inc\Libraries as Libraries;
?>
<div id="mzScheduleNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php _e('Previous Week', 'mz-mindbody-api'); ?></a> -
    <a href="#" class="following" data-offset="1"><?php _e('Following Week', 'mz-mindbody-api'); ?></a>
</div>


<?php // mz_pr($data); ?>

<div id="mzScheduleDisplay" class="">
	<table id="mz_horizontal_schedule">
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
							
							<?php 
							$linkArray = array(
												'data-staffName' => $class->staffName,
												'data-className' => $class->className,
												'data-classDescription' => rawUrlEncode($class->classDescription),
												'class' => $class_name_css = 'modal-toggle mz_get_registrants ' . sanitize_html_class($class->className, 'mz_class_name'),
												'text' => $class->className,
												'data-target' => $data->data_target
												);

							if ($data->atts['show_registrants'] == 1) {
											$get_registrants_nonce = wp_create_nonce( 'mz_MBO_get_registrants_nonce');
											$linkArray['data-nonce'] = $get_registrants_nonce;
											$linkArray['data-classID'] = $class->class_title_ID;
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
									<?php echo $class->staffName; ?>
							</td>
							<td class="mz_sessionTypeName">
									<?php echo $class->sessionTypeName; ?>
							</td>
					</tr>
					<?php endforeach; ?>
			</tbody>
			<?php endforeach; ?>
	</table>
</div>

<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="registrantModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="mzStaffScheduleModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>