<?php
	/**
	 * @package     Freemius
	 * @copyright   Copyright (c) 2015, Freemius, Inc.
	 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
	 * @since       1.0.3
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		return;
	}

	/**
	 * Freemius SDK Version.
	 *
	 * @var string
	 */
	$this_sdk_version = '2.13.1';

	if ( ! function_exists( 'fs_find_caller_plugin_file' ) ) {
		require_once dirname( __FILE__ ) . '/includes/fs-essential-functions.php';
	}

	if ( ! class_exists( 'Freemius' ) ) {
		require_once dirname( __FILE__ ) . '/includes/class-freemius.php';
	}
