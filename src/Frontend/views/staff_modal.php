<?php

use MZoo\MzMindbody as NS;

?>
<div class="mz_modalStaffDescription">

	<p>
		<?php
		if ( ! empty( $data->staff_details->ImageURL ) ) :
			?>
			<img src="<?php echo $data->staff_details->ImageURL; ?>" class="mz_modal_staff_image_body"/>
			<?php
		endif;
		if ( ! empty( $data->staff_details->Bio ) ) :
			?>
			<?php echo $data->staff_details->Bio; ?>
			<?php
		else :
			?>
			<?php esc_html_e( 'No biography for this staff member yet.', 'mz-mindbody-api' ); ?>
			<?php
		endif;
		?>
	</p>
		<?php echo $data->staff_details->ScheduleButton; ?>

</div>


