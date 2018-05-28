<?php

namespace MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime;

/**
 * Extension of DateTime for WordPress.
 */
class WpDateTime extends \DateTime implements WpDateTimeInterface {

	const MYSQL = 'Y-m-d H:i:s';

	use WpDateTimeTrait;
}
