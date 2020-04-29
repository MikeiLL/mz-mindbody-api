<?php
namespace MZ_Mindbody\Inc\Common;

use MZ_Mindbody as NS;

class Token_Management {
	/**
	 * Intermittently fetch, store and serve mbo api UserTokens.
	 * @since 2.5.7
	 *
	 * The MBO V6 API serves and requires UserTokens which expire after seven days. So
	 * we will store a valid UserToken in the db options table, intermittently updating it.
	 *
	 * wp_schedule_event
	 *
	 *
	 */
}
