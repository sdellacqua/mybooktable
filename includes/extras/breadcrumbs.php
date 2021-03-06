<?php

function mbt_breadcrumbs_init() {
	if(mbt_get_setting('enable_breadcrumbs')) {
		add_action('mbt_before_single_book', 'mbt_the_breadcrumbs');
		add_action('mbt_before_book_archive', 'mbt_the_breadcrumbs');
	}
	add_filter('woo_breadcrumbs_trail', 'mbt_integrate_woo_breadcrumbs');
	add_filter('genesis_archive_crumb', 'mbt_integrate_genesis_breadcrumb_archive', 20, 2);
}
add_action('mbt_init', 'mbt_breadcrumbs_init');

function mbt_integrate_woo_breadcrumbs($trail) {
	global $mbt_taxonomy_query;
	if(is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') || (mbt_is_taxonomy_query() && ($mbt_taxonomy_query->is_tax('mbt_author') or $mbt_taxonomy_query->is_tax('mbt_genre') or $mbt_taxonomy_query->is_tax('mbt_series')))) {
		$page_link = '<a href="'.mbt_get_booktable_url().'">Books</a>';
		array_splice($trail, 1, count($trail) - 1, array($page_link, $trail['trail_end']));
	}
	return $trail;
}

function mbt_integrate_genesis_breadcrumb_archive($crumb, $args) {
	if(is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') || (mbt_is_taxonomy_query() && ($mbt_taxonomy_query->is_tax('mbt_author') or $mbt_taxonomy_query->is_tax('mbt_genre') or $mbt_taxonomy_query->is_tax('mbt_series')))) {
		$crumb = '<a href="'.mbt_get_booktable_url().'">Books</a>'.$args['sep'].$crumb;
	}
	return $crumb;
}

function mbt_get_breadcrumbs($delimiter = '') {
	global $wp_query;
	$delimiter = empty($delimiter) ? ' &gt; ' : $delimiter;
	$output = '';

	if(is_singular('mbt_book')) {
		global $post;
		$output .= '<a href="'.mbt_get_booktable_url().'">Books</a>'.$delimiter.'<a href="'.get_permalink().'">'.$post->post_title.'</a>';
	} else if(is_tax('mbt_author') or is_tax('mbt_series') or is_tax('mbt_genre')) {
		$output .= '<a href="'.mbt_get_booktable_url().'">Books</a>'.$delimiter.'<a href="'.get_term_link(get_queried_object()).'">'.get_queried_object()->name.'</a>';
	}

	return apply_filters('mbt_get_breadcrumbs', empty($output) ? '' : '<div class="mbt-breadcrumbs">'.$output.'</div>');
}
function mbt_the_breadcrumbs($delimiter = '') {
	echo(mbt_get_breadcrumbs($delimiter));
}