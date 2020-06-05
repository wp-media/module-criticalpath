<?php

namespace WP_Rocket\Tests\Integration;

use League\Container\Container;
use WP_Rocket\Admin\Options;
use WP_Rocket\Event_Management\Event_Manager;

define( 'CRITICALPATH_MODULE_ROOT', dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR );
define( 'WP_ROCKET_PLUGIN_ROOT', CRITICALPATH_MODULE_ROOT );
define( 'CRITICALPATH_MODULE_TESTS_FIXTURES_DIR', dirname( __DIR__ ) . '/Fixtures' );
define( 'WP_ROCKET_TESTS_FIXTURES_DIR', CRITICALPATH_MODULE_TESTS_FIXTURES_DIR );
define( 'CRITICALPATH_MODULE_TESTS_DIR', __DIR__ );
define( 'WP_ROCKET_TESTS_DIR', __DIR__ );

// Manually load the plugin being tested.
tests_add_filter(
	'muplugins_loaded',
	function() {

		$container     = new Container();
		$event_manager = new Event_Manager();

		$container->add(
			'options_api',
			function() {
				return new Options( 'wp_rocket_' );
			}
		);

		$container->add( 'options', 'WP_Rocket\Admin\Options_Data' )
			->withArgument( $container->get( 'options_api' )->get( 'settings', [] ) );

		$container->add( 'template_path', CRITICALPATH_MODULE_ROOT . 'views' );
		$container->add( 'beacon', 'WP_Rocket\Engine\Admin\Beacon\Beacon' )
			->withArgument( $container->get( 'options' ) )
			->withArgument( $container->get( 'template_path' ) . '/settings' );

		$container->addServiceProvider( 'WP_Rocket\Engine\CriticalPath\ServiceProvider' );

		$subscribers = [
			'critical_css_subscriber',
			'rest_cpcss_subscriber',
			'critical_css_admin_subscriber',
		];
		foreach ( $subscribers as $subscriber ) {
			$event_manager->add_subscriber( $container->get( $subscriber ) );
		}
	}
);
