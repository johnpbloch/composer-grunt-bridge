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

use Composer\Util\ProcessExecutor;
use Icecave\Isolator\Isolator;
use Symfony\Component\Process\ExecutableFinder;

/**
 * A simple client for performing Grunt operations.
 */
class GruntClient implements GruntClientInterface {
	/**
	 * Construct a new Grunt client.
	 *
	 * @param ProcessExecutor|null  $processExecutor  The process executor to use.
	 * @param ExecutableFinder|null $executableFinder The executable finder to use.
	 * @param Isolator|null         $isolator         The isolator to use.
	 */
	public function __construct(
		ProcessExecutor $processExecutor = null,
		ExecutableFinder $executableFinder = null,
		Isolator $isolator = null
	) {
		if ( null === $processExecutor ) {
			$processExecutor = new ProcessExecutor;
		}
		if ( null === $executableFinder ) {
			$executableFinder = new ExecutableFinder;
		}

		$this->processExecutor  = $processExecutor;
		$this->executableFinder = $executableFinder;
		$this->isolator         = Isolator::get( $isolator );
	}

	/**
	 * Get the process executor.
	 *
	 * @return ProcessExecutor The process executor.
	 */
	public function processExecutor() {
		return $this->processExecutor;
	}

	/**
	 * Get the executable finder.
	 *
	 * @return ExecutableFinder The executable finder.
	 */
	public function executableFinder() {
		return $this->executableFinder;
	}

	/**
	 * Run a grunt task.
	 *
	 * @param string|null $task The task to run, or null for the default task.
	 * @param string|null $path The path to the directory containing the Gruntfile, or null to use the current working directory.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	public function runTask( $task = null, $path = null ) {
		if ( $task && ! is_array( $task ) ) {
			$task = [ $task ];
		}
		$this->executeGrunt( $task ?: [ ], $path );
	}

	/**
	 * Execute an Grunt command.
	 *
	 * @param             array                 [integer,string] $arguments            The arguments to pass to the grunt executable.
	 * @param string|null $workingDirectoryPath The path to the working directory, or null to use the current working directory.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	protected function executeGrunt(
		array $arguments,
		$workingDirectoryPath = null
	) {
		array_unshift( $arguments, $this->gruntPath() );
		$command = implode( ' ', array_map( 'escapeshellarg', $arguments ) );

		if ( null !== $workingDirectoryPath ) {
			$previousWorkingDirectoryPath = $this->isolator()->getcwd();
			$this->isolator()->chdir( $workingDirectoryPath );
		}

		$exitCode = $this->processExecutor()->execute( $command );

		if ( null !== $workingDirectoryPath ) {
			$this->isolator()->chdir( $previousWorkingDirectoryPath );
		}

		if ( 0 !== $exitCode ) {
			throw new Exception\GruntCommandFailedException( $command );
		}
	}

	/**
	 * Get the grunt exectable path.
	 *
	 * @return string                           The path to the grunt executable.
	 * @throws Exception\GruntNotFoundException If the grunt executable cannot be located.
	 */
	protected function gruntPath() {
		if ( null === $this->gruntPath ) {
			$this->gruntPath = $this->executableFinder()->find( 'grunt' );
			if ( null === $this->gruntPath ) {
				throw new Exception\GruntNotFoundException;
			}
		}

		return $this->gruntPath;
	}

	/**
	 * Get the isolator.
	 *
	 * @return Isolator The isolator.
	 */
	protected function isolator() {
		return $this->isolator;
	}

	private $processExecutor;
	private $executableFinder;
	private $isolator;
	private $gruntPath;
}
