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
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use Composer\Util\ProcessExecutor;

/**
 * Runs Grunt tasks for Composer projects.
 */
class GruntBridge implements GruntBridgeInterface {
	/**
	 * Construct a new Composer Grunt bridge plugin.
	 *
	 * @param IOInterface|null                $io           The i/o interface to use.
	 * @param GruntVendorFinderInterface|null $vendorFinder The vendor finder to use.
	 * @param GruntClientInterface|null       $client       The Grunt client to use.
	 */
	public function __construct(
		IOInterface $io = null,
		GruntVendorFinderInterface $vendorFinder = null,
		GruntClientInterface $client = null
	) {
		if ( null === $io ) {
			$io = new NullIO;
		}
		if ( null === $vendorFinder ) {
			$vendorFinder = new GruntVendorFinder;
		}
		if ( null === $client ) {
			$client = new GruntClient( new ProcessExecutor( $io ) );
		}

		$this->io           = $io;
		$this->vendorFinder = $vendorFinder;
		$this->client       = $client;
	}

	/**
	 * Get the i/o interface.
	 *
	 * @return IOInterface The i/o interface.
	 */
	public function io() {
		return $this->io;
	}

	/**
	 * Get the vendor finder.
	 *
	 * @return GruntVendorFinderInterface The vendor finder.
	 */
	public function vendorFinder() {
		return $this->vendorFinder;
	}

	/**
	 * Get the Grunt client.
	 *
	 * @return GruntClientInterface The Grunt client.
	 */
	public function client() {
		return $this->client;
	}

	public function runGruntTasks( Composer $composer, $isDevMode = null ) {
		$isDevMode = (bool) $isDevMode;
		$this->io()->write(
			'<info>Running Grunt tasks for root project</info>'
		);

		if ( $this->isDependantPackage( $composer->getPackage(), $isDevMode ) ) {
			$this->client()->runTask( $this->getTask( $composer->getPackage() ) );
		} else {
			$this->io()->write( 'Nothing to grunt' );
		}

		$this->installForVendors( $composer );
	}

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
	) {
		if ( null === $includeDevDependencies ) {
			$includeDevDependencies = false;
		}

		foreach ( $package->getRequires() as $link ) {
			if ( 'johnpbloch/composer-grunt-bridge' === $link->getTarget() ) {
				return true;
			}
		}

		if ( $includeDevDependencies ) {
			foreach ( $package->getDevRequires() as $link ) {
				if ( 'johnpbloch/composer-grunt-bridge' === $link->getTarget() ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param PackageInterface $package
	 *
	 * @return string|array|null
	 */
	public function getTask( PackageInterface $package ) {
		$extra = $package->getExtra();
		if ( ! empty( $extra ) && ! empty( $extra['grunt-task'] ) ) {
			return $extra['grunt-task'];
		}

		return null;
	}

	/**
	 * Run Grunt tasks for all Composer dependencies that use the bridge.
	 *
	 * @param Composer $composer The main Composer object.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	protected function installForVendors( Composer $composer ) {
		$this->io()->write(
			'<info>Running Grunt tasks for Composer dependencies</info>'
		);

		$packages = $this->vendorFinder()->find( $composer, $this );
		if ( count( $packages ) > 0 ) {
			foreach ( $packages as $package ) {
				$this->io()->write(
					sprintf(
						'<info>Running Grunt tasks for %s</info>',
						$package->getPrettyName()
					)
				);

				$this->client()->runTask(
					$this->getTask($package),
					$composer->getInstallationManager()
					         ->getInstallPath( $package )
				);
			}
		} else {
			$this->io()->write( 'Nothing to grunt' );
		}
	}

	private $io;
	private $vendorFinder;
	private $client;
}
