<?php

namespace MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime;

/**
 * @see WpDateTimeTrait
 */
interface WpDateTimeInterface extends \DateTimeInterface {

	public static function createFromPost( $post, $field = 'date' );

	public function formatI18n( $format );

	public function formatDate();

	public function formatTime();
}
