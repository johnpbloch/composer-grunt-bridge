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

use Composer\IO\IOInterface;

/**
 * Creates Grunt bridges.
 */
class GruntBridgeFactory implements GruntBridgeFactoryInterface {
	/**
	 * Construct a new Composer Grunt bridge plugin.
	 *
	 * @param IOInterface|null                $io           The i/o interface to use.
	 * @param GruntVendorFinderInterface|null $vendorFinder The vendor finder to use.
	 * @param GruntClientInterface|null       $client       The Grunt client to use.
	 *
	 * @return GruntBridgeInterface
	 */
	public function create(
		IOInterface $io = null,
		GruntVendorFinderInterface $vendorFinder = null,
		GruntClientInterface $client = null
	) {
		return new GruntBridge( $io, $vendorFinder, $client );
	}
}
