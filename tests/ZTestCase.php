<?php

namespace Tacone\Coffee\Test;

class ZTestCase extends \Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';
//
		return require __DIR__.'/../../../../bootstrap/start.php';
	}

    protected function field()
    {
        
    }
}
