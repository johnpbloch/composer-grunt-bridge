<?php

/*
 * This file is part of the Composer Grunt bridge package.
 *
 * Copyright (c) 2015 John Bloch
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JPB\Composer\GruntBridge\Exception;

use Exception;

/**
 * The grunt executable could not be found.
 */
final class GruntNotFoundException extends Exception {
	/**
	 * Construct a new grunt not found exception.
	 *
	 * @param Exception|null $cause The cause, if available.
	 */
	public function __construct( Exception $cause = null ) {
		parent::__construct(
			'The grunt executable could not be found.',
			0,
			$cause
		);
	}
}
