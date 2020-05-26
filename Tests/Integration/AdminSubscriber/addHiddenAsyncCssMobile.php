<?php

namespace WPMedia\CriticalPath\Tests\Integration\AdminSubscriber;

use WP_Rocket\Tests\Integration\TestCase;

/**
 * @covers \WPMedia\CriticalPath\AdminSubscriber::add_hidden_async_css_mobile
 *
 * @group  AdminOnly
 * @group  CriticalPath
 */
class Test_AddHiddenAsyncCssMobile extends TestCase {
	/**
	 * @dataProvider configTestData
	 */
	public function testShouldAddValueToArray( $hidden_fields, $expected ) {
		$this->assertSame(
			$expected,
			apply_filters( 'rocket_hidden_settings_fields', $hidden_fields )
		);
	}
}
