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
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * A Composer plugin to facilitate Grunt integration.
 */
class GruntBridgePlugin implements PluginInterface, EventSubscriberInterface {
	/**
	 * Construct a new Composer Grunt bridge plugin.
	 *
	 * @param GruntBridgeFactoryInterface|null $bridgeFactory The bridge factory to use.
	 */
	public function __construct( GruntBridgeFactoryInterface $bridgeFactory = null ) {
		if ( null === $bridgeFactory ) {
			$bridgeFactory = new GruntBridgeFactory;
		}

		$this->bridgeFactory = $bridgeFactory;
	}

	/**
	 * Get the bridge factory.
	 *
	 * @return GruntBridgeFactoryInterface The bridge factory.
	 */
	public function bridgeFactory() {
		return $this->bridgeFactory;
	}

	/**
	 * Activate the plugin.
	 *
	 * @param Composer    $composer The main Composer object.
	 * @param IOInterface $io       The i/o interface to use.
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		// no action required
	}

	/**
	 * Get the event subscriber configuration for this plugin.
	 *
	 * @return array<string,string> The events to listen to, and their associated handlers.
	 */
	public static function getSubscribedEvents() {
		return array(
			ScriptEvents::POST_INSTALL_CMD => 'onPostInstallCmd',
			ScriptEvents::POST_UPDATE_CMD  => 'onPostUpdateCmd',
		);
	}

	/**
	 * Handle post install command events.
	 *
	 * @param Event $event The event to handle.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	public function onPostInstallCmd( Event $event ) {
		$this->bridgeFactory()
		     ->create( $event->getIO() )
		     ->runGruntTasks( $event->getComposer(), $event->isDevMode() );
	}

	/**
	 * Handle post update command events.
	 *
	 * @param Event $event The event to handle.
	 *
	 * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
	 * @throws Exception\GruntCommandFailedException If the operation fails.
	 */
	public function onPostUpdateCmd( Event $event ) {
		$this->bridgeFactory()
		     ->create( $event->getIO() )
		     ->runGruntTasks( $event->getComposer(), true );
	}

	private $bridgeFactory;
}
