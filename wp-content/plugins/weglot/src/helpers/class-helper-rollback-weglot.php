<?php

/**
 * This file need to be compatible with PHP 5.3
 * Example : Don't use short syntax for array()
 */

namespace WeglotWP\Helpers;

// Exit if accessed directly.
use AllowDynamicProperties;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

#[AllowDynamicProperties]
class Helper_Rollback_Weglot extends \Plugin_Upgrader {

	/**
	 * Plugin rollback.
	 *
	 * @param mixed $plugin
	 * @param array<int|string,mixed> $args
	 *
	 * @return array<int|string,mixed>|bool|\WP_Error
	 */
	public function rollback( $plugin, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		/** @phpstan-ignore-next-line */
		$plugin_slug = $this->skin->plugin;

		$plugin_version = $this->skin->options['version'];

		$download_endpoint = 'https://downloads.wordpress.org/plugin/';

		$url = $download_endpoint . $plugin_slug . '.' . $plugin_version . '.zip';

		add_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );

		$this->run(
			array(
				'package'           => $url,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'plugin' => $plugin,
					'type'   => 'plugin',
					'action' => 'update',
				),
			)
		);

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );

		if ( ! $this->result || is_wp_error( $this->result ) ) {
			return $this->result;
		}

		// Force refresh of plugin update information.
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		return true;
	}
}
