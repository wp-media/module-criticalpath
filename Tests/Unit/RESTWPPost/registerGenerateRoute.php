<?php

namespace WPMedia\CriticalPath\Tests\Unit\RESTWPPost;

use Brain\Monkey\Functions;
use WP_Rocket\Admin\Options_Data;
use WPMedia\CriticalPath\APIClient;
use WPMedia\CriticalPath\ProcessorService;
use WPMedia\CriticalPath\DataManager;
use WPMedia\CriticalPath\RESTWPPost;
use WPMedia\PHPUnit\Unit\TestCase;
use Mockery;

/**
 * @covers \WPMedia\CriticalPath\RESTWPPost::register_generate_route
 *
 * @group  CriticalPath
 */
class Test_RegisterGenerateRoute extends TestCase {

	public function testShouldRegisterRoute() {
		Mockery::mock( APIClient::class );
		Mockery::mock( DataManager::class );
		$cpcss_service = Mockery::mock( ProcessorService::class );
		$options = Mockery::mock( Options_Data::class );
		$instance = new RESTWPPost( $cpcss_service, $options );

		Functions\expect( 'register_rest_route' )
			->once()
			->with(
				RESTWPPost::ROUTE_NAMESPACE,
				'cpcss/post/(?P<id>[\d]+)',
				[
					'methods'             => 'POST',
					'callback'            => [ $instance, 'generate' ],
					'permission_callback' => [ $instance, 'check_permissions' ],
				]
			);

		$instance->register_generate_route();
	}
}
