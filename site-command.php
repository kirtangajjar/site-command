<?php

use EE\Dispatcher\CommandFactory;

if ( ! defined( 'SITE_TEMPLATE_ROOT' ) ) {
	define( 'SITE_TEMPLATE_ROOT', __DIR__ . '/templates' );
}

if ( ! class_exists( 'EE' ) ) {
	return;
}

$autoload = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

// Load utility functions
require_once 'src/site-utils.php';

function Before_Help_Command( $args, $assoc_args ) {

	if ( isset( $args[0] ) && 'site' === $args[0] ) {
		$site_types = Site_Command::get_site_types();
		if ( isset( $assoc_args['type'] ) ) {
			$type = $assoc_args['type'];	
		} else {
			//TODO: get from config.
			$type = 'html';
		}
		
		if ( isset( $site_types[ $type ] ) ) {
			$callback = $site_types[ $type ];

			$command      = EE::get_root_command();
			$leaf_command = CommandFactory::create( 'site', $callback, $command );
			$command->add_subcommand( 'site', $leaf_command );
		} else {
			$error = sprintf(
				"'%s' is not a registered site type of 'ee site --type=%s'. See 'ee help site --type=%s' for available subcommands.",
				$type,
				$type,
				$type
			);
			EE::error( $error );
		}
	}
}

EE::add_command( 'site', 'Site_Command' );
EE::add_hook( 'before_invoke:help', 'Before_Help_Command' );
Site_Command::add_site_type( 'html', 'EE\Site\Type\HTML' );
