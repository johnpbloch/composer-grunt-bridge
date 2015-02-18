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
 * The Grunt command failed.
 */
final class GruntCommandFailedException extends Exception {
	/**
	 * Construct a new Grunt command failed exception.
	 *
	 * @param string         $command The executed command.
	 * @param Exception|null $cause   The cause, if available.
	 */
	public function __construct( $command, Exception $cause = null ) {
		$this->command = $command;

		parent::__construct(
			sprintf( 'Execution of %s failed.', var_export( $command, true ) ),
			0,
			$cause
		);
	}

	/**
	 * Get the executed command.
	 *
	 * @return string The command.
	 */
	public function command() {
		return $this->command;
	}

	private $command;
}
