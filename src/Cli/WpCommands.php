<?php
/**
 * This file contains the class which withe methods to retrieve client contracts
 *
 * Methods to retrieve and manipulate user MBO contracts.
 *
 * @package MzMboOnDemand
 */

namespace MZoo\MzMindbody\Cli;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;
use \WP_CLI_Command;;

/**
 * Interface with Mindbody plugin data.
 * @since 2.9.3
 */
class WpCommands extends \WP_CLI_Command {
      /**
   * Display version information.
   * ## OPTIONS
   *
   * [--wponly]
   * : Shows only WP version info, omitting the plugin one.
   */
  function version( $args, $assoc_args ) {
    if ( !empty( $assoc_args['wponly'] ) ) {
      \WP_CLI::line( 'Version of WordPress is ' . get_bloginfo( 'version' ) . '.' );
    } else {
      \WP_CLI::line( 'Version of Mz Mindbody Api is ' . NS\PLUGIN_VERSION . '. WordPress version is ' . get_bloginfo( 'version' ) . '.' );
    }
  }


  /**
   * Clear All saved Mindbody API transients.
     * @since 2.9.3
   */
  function clear_transients() {
        $admin = new \MZoo\MzMindbody\Admin\Admin;
        $message = $admin->clear_plugin_transients();
    \WP_CLI::line( $message );
  }

  /**
   * Cancel API excess alert emails.
     * @since 2.9.3
   */
  function cancel_api_alerts() {
        $admin = new \MZoo\MzMindbody\Admin\Admin;
        $message = $admin->cancel_excess_api_alerts();
    \WP_CLI::line( $message );
  }

  /**
   * Fetch new MBO API token.
     * @since 2.9.3
   */
  function fetch_new_token() {
        $admin = new \MZoo\MzMindbody\Admin\Admin;
        $message = $admin->get_and_save_staff_token();
    \WP_CLI::line( $message );
  }

}
