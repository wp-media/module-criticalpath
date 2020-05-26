<?php

namespace WPMedia\CriticalPath\Tests\Unit\AdminSubscriber;

use WP_Rocket\Tests\Unit\FilesystemTestCase;

/**
 * @covers \WPMedia\CriticalPath\AdminSubscriber::cpcss_actions
 * @uses   ::rocket_direct_filesystem
 *
 * @group  CriticalPath
 */
class Test_CpcssActions extends FilesystemTestCase {
	use GenerateTrait;

	protected $path_to_test_data = '/AdminSubscriber/cpcssActions.php';

	protected static $mockCommonWpFunctionsInSetUp = true;

	public function setUp() {
		parent::setUp();

		$this->setUpMocks();
	}

	protected function tearDown() {
		unset( $GLOBALS['post'] );
		parent::tearDown();
	}

	/**
	 * @dataProvider providerTestData
	 */
	public function testShouldDisplayCPCSSSActions( $config, $expected ) {
		$this->setUpTest( $config );

		$this->beacon->shouldReceive( 'get_suggest' )
		             ->once()
		             ->andReturn( $expected['data']['beacon'] );

		$this->setUpGenerate( 'metabox/generate', $expected['data'] );

		ob_start();
		$this->subscriber->cpcss_actions();
		ob_get_clean();
	}
}
