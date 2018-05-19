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
									<a href="<?php
											echo $data->class_modal_link;
											?>" data-classID="<?php
											echo $class->class_title_ID;
											?>" data-target="<?php
											echo $data->data_target;
											?>" data-classDescription="<?php
											echo $class->classDescription;
											?>" data-staffName="<?php
											echo $class->staffName;
											if ($class->staffImage != ''){
												?>" data-staffImage="<?php
											echo $class->staffImage;
											}?>" data-nonce="<?php
											echo $data->data_nonce; ?>"><?php

											echo $class->className; ?></a>
							</td>
							<td class="mz_staffName">
									<?php echo $class->staffName; ?>
							</td>
							<td class="mz_sessionTypeName">
									<?php echo $class->sessionTypeName; ?>
									<?php // echo $class->classDescription; ?>
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