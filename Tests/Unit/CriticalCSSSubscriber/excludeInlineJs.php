<?php

namespace WPMedia\CriticalPath\Tests\Unit\CriticalCSSSubscriber;

use Brain\Monkey\Functions;
use Mockery;
use WP_Rocket\Admin\Options_Data;
use WPMedia\CriticalPath\CriticalCSS;
use WPMedia\CriticalPath\CriticalCSSGeneration;
use WPMedia\CriticalPath\CriticalCSSSubscriber;
use WP_Rocket\Tests\Unit\TestCase;

/**
 * @covers \WPMedia\CriticalPath\CriticalCSSSubscriber::exclude_inline_js
 *
 * @group  Subscribers
 * @group  CriticalPath
 */
class Test_ExcludeInlineJs extends TestCase {
	use SubscriberTrait;

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldInsertCriticalCSS( $excluded_inline, $expected_inline ) {
		$this->setUpTests();

		// Run it.
		$this->assertSame( $expected_inline, $this->subscriber->exclude_inline_js( $excluded_inline ) );
	}
}
