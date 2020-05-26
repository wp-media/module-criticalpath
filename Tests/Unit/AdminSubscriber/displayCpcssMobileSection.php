<?php

namespace WP_Rocket\Tests\Unit\inc\Engine\CriticalPath\AdminSubscriber;

use Brain\Monkey\Functions;
use WP_Rocket\Tests\Unit\TestCase;

/**
 * @covers \WPMedia\AdminSubscriber::cpcss_section
 *
 * @group  CriticalPath
 */
class Test_DisplayCpcssMobileSection extends TestCase {
	use GenerateTrait;

	protected static $mockCommonWpFunctionsInSetUp = true;

	protected function setUp() {
		parent::setUp();

		$this->setUpMocks();
	}

	protected function tearDown() {
		parent::tearDown();
	}

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldDisplayCPCSSMobileSection( $config, $expected ) {
		$this->setUpMocks();

		foreach ( $config['options'] as $option_key => $option ) {
			$this->options
				->shouldReceive( 'get' )
				->with( $option_key, 0 )
				->andReturn( $option );
		}

		$config['beacon'] = isset( $config['beacon'] ) ? $config['beacon'] : '';

		if ( ! empty( $config['beacon'] ) ) {
			$this->beacon->shouldReceive( 'get_suggest' )
						->once()
						->andReturn( $config['beacon'] );
		}

		Functions\when( 'current_user_can' )->justReturn( $config['current_user_can'] );

		$this->setUpGenerate( 'activate-cpcss-mobile', ['beacon' => $config['beacon'] ] );

		ob_start();
		$this->subscriber->display_cpcss_mobile_section();
		ob_get_clean();

	}
}