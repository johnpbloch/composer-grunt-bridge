<?php

/*
 * This file is part of the Composer Grunt bridge package.
 *
 * Copyright (c) 2015 John Bloch
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JPB\Composer\GruntBridge;

use Composer\Composer;
use Composer\Package\PackageInterface;

/**
 * The interface implemented by Grunt bridges.
 */
interface GruntBridgeInterface {
	/**
	 * Run Grunt tasks for a Composer project and its dependencies.
	 *
	 * @param Composer     $composer  The main Composer object.
	 * @param boolean|null $isDevMode True if dev mode is enabled.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	public function runGruntTasks( Composer $composer, $isDevMode = null );


	/**
	 * Returns true if the supplied package requires the Composer Grunt bridge.
	 *
	 * @param PackageInterface $package                The package to inspect.
	 * @param boolean|null     $includeDevDependencies True if the dev dependencies should also be inspected.
	 *
	 * @return boolean True if the package requires the bridge.
	 */
	public function isDependantPackage(
		PackageInterface $package,
		$includeDevDependencies = null
	);
}
