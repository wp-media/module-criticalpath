<?php

namespace WP_Rocket\Tests\Unit\inc\Engine\CriticalPath\RESTWPPost;

use Brain\Monkey\Functions;
use WP_Rocket\Admin\Options_Data;
use WPMedia\APIClient;
use WPMedia\ProcessorService;
use WPMedia\DataManager;
use WPMedia\RESTWPPost;
use WPMedia\PHPUnit\Unit\TestCase;
use Mockery;

/**
 * @covers \WPMedia\RESTWPPost::register_delete_route
 * @group  CriticalPath
 */
class Test_RegisterDeleteRoute extends TestCase {

	public function testShouldRegisterRoute() {
		Mockery::mock( APIClient::class );
		Mockery::mock( DataManager::class );
		$cpcss_service = Mockery::mock( ProcessorService::class );
		$options       = Mockery::mock( Options_Data::class );
		$instance = new RESTWPPost( $cpcss_service, $options );

		Functions\expect( 'register_rest_route' )
			->once()
			->with(
				RESTWPPost::ROUTE_NAMESPACE,
				'cpcss/post/(?P<id>[\d]+)',
				[
					'methods'             => 'DELETE',
					'callback'            => [ $instance, 'delete' ],
					'permission_callback' => [ $instance, 'check_permissions' ],
				]
			);

		$instance->register_delete_route();
	}
}
