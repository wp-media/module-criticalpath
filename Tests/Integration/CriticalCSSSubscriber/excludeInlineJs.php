<?php
namespace WPMedia\CriticalPath\Tests\Integration\CriticalCSSSubscriber;

use WP_Rocket\Tests\Integration\TestCase;

/**
 * @covers \WPMedia\CriticalPath\CriticalCSSSubscriber::exclude_inline_js
 *
 * @group  Subscribers
 * @group  CriticalCss
 */
class Test_ExcludeInlineJs extends TestCase {

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldExcludeInlineJs( $excluded_inline, $expected_inline ) {
		$this->assertSame( $expected_inline, apply_filters( 'rocket_excluded_inline_js_content', $excluded_inline ) );
	}
}
