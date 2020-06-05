<?php

namespace WP_Rocket\Tests\Integration\Admin\Subscriber;

use ReflectionObject;

trait ProviderTrait {

	public function providerTestData() {
		$obj      = new ReflectionObject( $this );
		$filename = $obj->getFileName();

		$dir  = WP_ROCKET_TESTS_FIXTURES_DIR . '/Admin/' . self::$provider_class . '/';
		$data = $this->getTestData( $dir, basename( $filename, '.php' ) );

		return isset( $data['test_data'] )
			? $data['test_data']
			: $data;
	}
}
