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

/**
 * The interface implemented by Grunt clients.
 */
interface GruntClientInterface {
	/**
	 * Run a grunt task.
	 *
	 * @param string|null $task The task to run, or null for the default task.
	 * @param string|null $path The path to the directory containing the Gruntfile, or null to use the current working directory.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	public function runTask( $task = null, $path = null );
}
