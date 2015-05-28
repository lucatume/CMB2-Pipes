<?php
// Here you can initialize variables that will be available to your tests
function load_p2p() {
	require_once PLUGIN_FOLDER . '/posts-to-posts/posts-to-posts.php';

	$p2p_core = PLUGIN_FOLDER . '/posts-to-posts/core';

	require_once $p2p_core . '/util.php';
	require_once $p2p_core . '/api.php';
	require_once $p2p_core . '/autoload.php';

	P2P_Autoload::register( 'P2P_', $p2p_core );

	P2P_Storage::install();
	P2P_Storage::init();

	P2P_Query_Post::init();
	P2P_Query_User::init();

	P2P_URL_Query::init();
}
