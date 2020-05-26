<?php

namespace WPMedia\CriticalPath\Tests\Unit\AdminSubscriber;

use Brain\Monkey\Functions;
use Mockery;
use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Engine\Admin\Beacon\Beacon;
use WPMedia\CriticalPath\CriticalCSS;
use WPMedia\CriticalPath\AdminSubscriber;
use WP_Rocket\Tests\Unit\TestCase;

/**
 * @covers \WPMedia\CriticalPath\AdminSubscriber::set_async_css_mobile_default_value
 *
 * @group  CriticalPath
 */
class Test_SetAsyncCssMobileDefaultValue extends TestCase {

	public function setUp() {
		parent::setUp();

		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
	}

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldUpdateOption( $versions, $update ) {
		$options = Mockery::mock( Options_Data::class );
		$subscriber = new AdminSubscriber(
			$options,
			Mockery::mock( Beacon::class ),
			Mockery::mock( CriticalCSS::class ),
			'wp-content/cache/critical-css/',
			'wp-content/plugins/wp-rocket/views/cpcss'
		);

        if ( true === $update ) {
            $settings = [
                'async_css_mobile' => 1,
            ];

            $options->shouldReceive( 'set' )
                ->once()
                ->with( 'async_css_mobile', 0 );

            $options->shouldReceive( 'get_options' )
                ->once()
                ->andReturn( $settings );

            Functions\expect( 'update_option' )
                ->once()
                ->with( 'wp_rocket_settings', $settings );
        } else {
            Functions\expect( 'update_option' )->never();
        }

        $subscriber->set_async_css_mobile_default_value( $versions['new'], $versions['old'] );
	}
}
