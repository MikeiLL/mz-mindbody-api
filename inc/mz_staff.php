<?php
function mZ_mindbody_staff_listing()
{
  require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

  $options = get_option( 'mz_mindbody_options','Option Not Set' );
  $mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";
  $mz_staff_cache = "mz_staff_cache";

  if ( $mz_cache_reset == "on" ){
    delete_transient( $mz_staff_cache );
  }
  if ( false === ( $staff = get_transient( $mz_staff_cache ) ) )
  {
    //Send the timeframe to the GetClasses class, unless already cached
    $staff = $mb->GetStaff();
  }

  $return = '';

  $mz_staff_list = $staff['GetStaffResult']['StaffMembers']['Staff'];

  // trying to allow sort order from within MBO - not working
  usort($mz_staff_list, 'sortById');;

  $mz_empty_tags_pattern = "/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/";

  foreach ($mz_staff_list as $staff_member)
  {
    if (!empty($staff_member['Bio']) && !empty($staff_member['ImageURL']))
    {
      $mz_staff_name = $staff_member['Name'];
      $mz_staff_bio = $staff_member['Bio'];
      $mz_staff_bio = str_replace($mz_empty_tags_pattern, '', $mz_staff_bio);
      $mz_staff_image = $staff_member['ImageURL'];
      $mz_staff_id = $staff_member['ID'];

      $return .= '<div class="mz_mbo_staff_profile clearfix">';
      $return .= '<div class="mz_mbo_staff_caption">';
      $return .= '<h3>' . $mz_staff_name . '</h3>';
      $return .= '</div>';
      $return .= '<div class="mz_mbo_staff_bio">';
      $return .= '<div class="mz_mbo_staff_photo">';
      $return .= '<img src="' . $mz_staff_image . '" alt="">';
      $return .= '<p class="mz_mbo_staff_schedule">';
      $return .= '<a href="http://clients.mindbodyonline.com/ws.asp?studioid=' . $options['mz_mindbody_siteID'] . '&stype=-7&sView=week&sTrn=' . $mz_staff_id . '" class="btn btn-info">See ' . $mz_staff_name .'&apos;s Schedule</a>';
      $return .= '</p>';
      $return .= '</div>';
      $return .= '</div>';
      $return .= $mz_staff_bio;
      $return .= '</div>';
      $return .= '<hr/>';
    }
  }
  return $return;
}

function remove_empties ($matches) {
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
function sortById($a, $b) {
    return $a['ID'] - $b['ID'];
}
?>
