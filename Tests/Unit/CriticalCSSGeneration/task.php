<?php

namespace WP_Rocket\Tests\Unit\inc\Engine\CriticalPath\CriticalCSSGeneration;

use Brain\Monkey\Functions;
use Mockery;
use WP_Error;
use WPMedia\CriticalCSSGeneration;
use WPMedia\ProcessorService;
use WP_Rocket\Tests\Unit\TestCase;

/**
 * @covers \WPMedia\CriticalCSSGeneration::task
 *
 * @group  CriticalPath
 */
class test_Task extends TestCase {
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		require_once WP_ROCKET_TESTS_FIXTURES_DIR . '/WP_Error.php';
	}

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldDoExpected( $item, $result, $transient, $expected ) {
		$processor  = Mockery::mock( ProcessorService::class );
		$generation = new CriticalCSSGeneration( $processor );

		$task = $this->get_reflective_method( 'task', CriticalCSSGeneration::class );

		Functions\when( 'get_transient' )->justReturn( [
			'generated' => 0,
			'total'     => 1,
			'items'     => [],
		] );

		if ( false === $result['success'] ) {
			$processor->shouldReceive( 'process_generate' )
			->once()
			->andReturnUsing( function() use ( $result ) {
				return new WP_Error( $result['code'], $result['message'] );
			} );
		} else {
			$processor->shouldReceive( 'process_generate' )
			->once()
			->andReturn( $result );
		}

		Functions\when( 'is_wp_error' )->alias( function( $thing ) {
			return ( $thing instanceof WP_Error );
		} );

		if ( ! isset( $transient ) ) {
			Functions\expect( 'set_transient' )->never();
		} else {
			Functions\expect( 'set_transient' )
				->once()
				->with(
					'rocket_critical_css_generation_process_running',
					$transient,
					HOUR_IN_SECONDS
				);
		}

		if ( false === $expected ) {
			$this->assertFalse( $task->invoke( $generation, $item ) );
		} else {
			$this->assertSame(
				$expected,
				$task->invoke( $generation, $item )
			);
		}
	}
}