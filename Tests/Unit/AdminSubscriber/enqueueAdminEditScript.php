<?php

namespace WP_Rocket\Tests\Unit\inc\Engine\CriticalPath\AdminSubscriber;

use Brain\Monkey\Functions;
use WP_Rocket\Tests\Unit\TestCase;

/**
 * @covers \WPMedia\AdminSubscriber::enqueue_admin_edit_script
 * @uses   ::rocket_get_constant
 *
 * @group  CriticalPath
 */
class Test_EnqueueAdminEditScript extends TestCase {
	use GenerateTrait;

	protected static $mockCommonWpFunctionsInSetUp = true;

	public function setUp() {
		parent::setUp();

		$this->setUpMocks();
	}

	protected function tearDown() {
		unset( $GLOBALS['post'] );
		unset( $GLOBALS['pagenow'] );
		parent::tearDown();
	}

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldEnqueueAdminScript( $config, $expected ) {
		if ( in_array( $config['page'], [ 'edit.php', 'post.php' ], true ) ) {
			$this->setUpTest( $config );
		}

		$GLOBALS['pagenow'] = $config['pagenow'];

		if ( $expected ) {
			$this->assertExpected( $config );
		} else {
			$this->assertNotExpected();
		}

		$this->subscriber->enqueue_admin_edit_script( $config['page'] );
	}

	private function assertExpected( $config ) {
		Functions\expect( 'wp_create_nonce' )
			->once()
			->with( 'wp_rest' )
			->andReturn( 'wp_rest_nonce' );

		Functions\expect( 'rest_url' )
			->once()
			->with( "wp-rocket/v1/cpcss/post/{$config['post']->ID}" )
			->andReturn( 'http://example.org/wp-rocket/v1/cpcss/post/' . $config['post']->ID );

		Functions\expect( 'wp_enqueue_script' )
			->once()
			->with(
				'wpr-edit-cpcss-script',
				rocket_get_constant( 'WP_ROCKET_ASSETS_JS_URL' ) . 'wpr-cpcss.js',
				[],
				rocket_get_constant( 'WP_ROCKET_VERSION' ),
				true
			)
			->andReturnNull();

		Functions\expect( 'wp_localize_script' )
			->once()
			->with(
				'wpr-edit-cpcss-script',
				'rocket_cpcss',
				$config['wp_localize_script']
			)
			->andReturnNull();
	}

	private function assertNotExpected() {
		Functions\expect( 'wp_enqueue_script' )->never();
		Functions\expect( 'rest_url' )->never();
		Functions\expect( 'wp_create_nonce' )->never();
		Functions\expect( 'wp_localize_script' )->never();
	}
}