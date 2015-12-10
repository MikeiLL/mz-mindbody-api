<?php

class MZ_MBO_Staff {

	private $mz_mbo_globals;
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	
	public function mZ_mindbody_staff_listing($atts, $account=0) {
	
	  $options = get_option( 'mz_mindbody_options','Option Not Set' );
	  $mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";
	  $mz_staff_cache = "mz_staff_cache";
  
	  // optionally pass in a type parameter. Defaults to week.
		$atts = shortcode_atts( array(
			'account' => '0',
			'gallery' => '0'
				), $atts );
		$account = $atts['account'];
		$gallery = $atts['gallery'];
		
		wp_enqueue_style('mZ_mindbody_schedule_bs', asset_path('styles/main.css'), false, null);
		wp_enqueue_script('modernizr', asset_path('scripts/modernizr.js'), array(), null, true);
		
		if ($gallery == 1)
			wp_enqueue_script('mz_mbo_bootstrap_script', asset_path('scripts/main.js'), array('jquery'), null, true);
		
	  if ( $mz_cache_reset == "on" ){
		delete_transient( $mz_staff_cache );
	  }
	  
	  $mbo_staff_data = get_transient( $mz_staff_cache );
	  
	  if ( '' == $mbo_staff_data )
	  {
	  	$mb = MZ_Mindbody_Init::instantiate_mbo_API();
	  	
		if ($account == 0) {
				//Send the timeframe to the GetClasses class, unless already cached
				$mbo_staff_data = $mb->GetStaff();
			}else{
				$mb->sourceCredentials['SiteIDs'][0] = $account; 
				$mbo_staff_data = $mb->GetStaff();
			}
		set_transient( $mz_staff_cache, $mbo_staff_data, 60 * 60 * 24 );
	
	  }

	  $return = <<<EFD
		   <!-- Page Content -->
    <div class="container">

        <div class="row">
EFD;
		if (isset($mbo_staff_data['GetStaffResult'])):
	  	$mz_staff_list = $mbo_staff_data['GetStaffResult']['StaffMembers']['Staff'];
	  else:
	  	mz_pr($mbo_staff_data);
	  endif;

	  // trying to allow sort order from within MBO - not working
	  //usort($mz_staff_list, $this->sortById());;

	  $mz_empty_tags_pattern = "/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/";
	  
		if ($account != "0") {
			$mz_site_id = $account;
			} else {
			$mz_site_id = $options['mz_mindbody_siteID'];
			}
		$MBO_URL_PARTS = array('http://clients.mindbodyonline.com/ws.asp?studioid=',
											'&stype=-7&sView=week&sTrn=');

	  foreach ($mz_staff_list as $staff_member)
	  {
		if (!empty($staff_member['Bio']) && !empty($staff_member['ImageURL']))
		{
			$mz_staff_name = $staff_member['Name'];
			$mz_staff_bio = $staff_member['Bio'];
			$mz_staff_bio = str_replace($mz_empty_tags_pattern, '', $mz_staff_bio);

			//TODO Abstract this (with regular expressions?).
			if (substr($mz_staff_bio, 0, 3) == '<p>') {
					$mz_staff_bio = substr($mz_staff_bio, 3);
					$mz_replace_dom_start = '<p>';
				} else if (substr($mz_staff_bio, 0, 5) == '<div>'){
					$mz_staff_bio = substr($mz_staff_bio, 5);
					$mz_replace_dom_start = '<div>';
				} else {
					$mz_replace_dom_start = '';
				}
				
			$mz_staff_image = $staff_member['ImageURL'];
			$mz_staff_id = $staff_member['ID'];
		  
			if ($gallery == 0) {
				$return .= '<div class="mz_mbo_staff_profile clearfix">';
				$return .= '<div class="mz_mbo_staff_caption">';
				$return .= '<h3>' . $mz_staff_name . '</h3>';
				$return .= '</div>';
				$return .= '<div class="mz_mbo_staff_bio">';
				$return .= $mz_replace_dom_start . ' <img src="' . $mz_staff_image . '" alt="' . $mz_staff_name . '" class="img-responsive mz_modal_staff_image_body">';
				$return .= $mz_staff_bio;
				$return .= '</div>';
				$return .= '</div>';
				$return .= '<p class="mz_mbo_staff_schedule">';
				$return .= '<a href="'. $MBO_URL_PARTS[0] . $mz_site_id . $MBO_URL_PARTS[1] . $mz_staff_id . '" class="btn btn-info mz-btn-info" target="_blank">See ' . $mz_staff_name .'&apos;s Schedule</a>';
				$return .= '</p>';
				$return .= '<hr style="clear:both"/>';
		  } else {
				$return .=     '<div class="col-lg-3 col-md-4 col-xs-6 mz-staff-thumb">';
				$return .=     '<a class="thumbnail" data-target="#mzStaffModal" '
								. 'data-staffImage="' . rawUrlEncode($mz_staff_image) . '" ' 
								. 'data-staffName="' . $mz_staff_name . '" '
								. 'data-domStart="' . $mz_replace_dom_start . '" '
								. 'data-siteID="' . $mz_site_id . '" '
								. 'data-staffID="' . $mz_staff_id . '" '
								. 'data-staffBio="' . rawUrlEncode($mz_staff_bio) .'" href="' . MZ_MINDBODY_SCHEDULE_URL . 
							'inc/modal_descriptions.php">';
				$return .=             sprintf('<img class="img-responsive mz-staff-image" src="%s" alt="">', $mz_staff_image);
				$return .= 						sprintf('<div class="mz-staff-name">%s</div>',$mz_staff_name);
				$return .=        ' </a>';
				$return .=     '</div>';
			}
	  }
	}
	  
		$return .= '</div>';
		$return .= '</div>';
		$return .= '<div id="mzStaffModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">

			</div>';
	  return $return;
	}

	protected function remove_empties ($matches) {
	  ### Variableize the stuff between the tags.
	  $content = $matches;
	  ### Remove all nbsps, empty tags, brs, and whitespace.
	  $content = str_replace('&nbsp;', '', $content);
	  $content = preg_replace('%<(\w+)[^>]*></\1>%', '', $content);
	  $content = preg_replace('%<br/?>%', '', $content);
	  $content = preg_replace('/\s/s', '', $content);
	  ### If there is still content the tag innards are not empty,
	  ### send back the original match. Otherwise, send empty.
	  return $content ? $matches[0] : '' ;
	}

	// http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
	protected function sortById($a, $b) {
		return $a['ID'] - $b['ID'];
	}
}//EOF MZ_MBO_Staff
?>
