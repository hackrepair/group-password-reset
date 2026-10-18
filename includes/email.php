<?php
/**
 * Shared site identification for plugin email subjects.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gpr_email_domain() {
	$host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$port = wp_parse_url( home_url( '/' ), PHP_URL_PORT );
	if ( ! $host ) {
		$host = wp_parse_url( site_url( '/' ), PHP_URL_HOST );
		$port = wp_parse_url( site_url( '/' ), PHP_URL_PORT );
	}
	return strtolower( (string) $host ) . ( $port ? ':' . (int) $port : '' );
}

function gpr_email_subject( $label ) {
	return '[' . gpr_email_domain() . '] [Group Password Reset] ' . $label;
}
