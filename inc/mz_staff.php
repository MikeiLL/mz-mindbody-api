<?php
function mZ_mindbody_staff_listing() {
require_once MZ_MINDBODY_SCHEDULE_DIR .'/mindbody-api/MB_API.php';
$mb = new my_MB_API();
$options = get_option( 'mz_mindbody_options','Error: Mindbody Credentials Not Set' );

$mb->sourceCredentials = array(
		"SourceName"=>$options['mz_source_name'], 
		"Password"=>$options['mz_mindbody_password'], 
		"SiteIDs"=>array($options['mz_mindbody_siteID'])
	);
$staff = $mb->GetStaff();
$mz_staff_list = $staff['GetStaffResult']['StaffMembers']['Staff'];

//var_dump($mz_staff_list);

//$mZ_plugin_url = plugin_dir_url(__FILE__);
$mz_empty_tags_pattern = "/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/";

foreach ($mz_staff_list as $staff_member) {
  if (!empty($staff_member['Bio']) && !empty($staff_member['ImageURL'])){
//$bio = $employee->Bio[0];
$mz_staff_name = $staff_member['Name'];
$mz_staff_bio = $staff_member['Bio'];
$mz_staff_bio = str_replace($mz_empty_tags_pattern, '', $mz_staff_bio);
//var_dump($mz_staff_bio);
$mz_staff_image = $staff_member['ImageURL'];
$mz_staff_id = $staff_member['ID'];
  //echo explode(" ", $employee->Bio);
    echo "<div class=\"mz_mbo_staff_caption\">";
    echo "<h3>$mz_staff_name<a href='#' class='pull-right'><i class='icon-plus'></i></a></h3></div>";
    echo '<div class="mz_mbo_thumbnail pull-left" style="margin-right:5px;margin-bottom:4px;">';
    echo "<img src=\"".MZ_MINDBODY_SCHEDULE_URL."timthumb/timthumb.php?src=$mz_staff_image&h=200&a=t&w=220\" alt=\"\"></div>";
    echo "<div class='mz_mbo_staff_bio'>";
    echo "<p>$mz_staff_bio</p>";
    echo "<p class='pull-right'><a href=\"http://clients.mindbodyonline.com/ws.asp?studioid=43474&stype=-7&sView=week&sTrn=$mz_staff_id\" class=\"btn btn-primary\">See Schedule</a></p>";
    echo "</div><hr/>";
  }
}


?>
<?php
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
?>