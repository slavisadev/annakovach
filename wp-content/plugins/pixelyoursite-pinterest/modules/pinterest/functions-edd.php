<?php

namespace PixelYourSite\Pinterest;

use PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function getEddCustomAudiencesOptimizationParams( $post_id ) {
	
	$post = get_post( $post_id );
	
	$params = array(
		'content_name'  => '',
		'category_name' => '',
	);
	
	if ( ! $post ) {
		return $params;
	}
	
	$params['content_name']  = $post->post_title;
	$params['category_name'] = implode( ', ', PixelYourSite\getObjectTerms( 'download_category', $post_id ) );
	
	return $params;
	
}

function getEddDownloadContentId( $download_id ) {

    if ( PixelYourSite\Pinterest()->getOption( 'edd_content_id' ) == 'download_sku' ) {
        $content_id = get_post_meta( $download_id, 'edd_sku', true );
    } else {
        $content_id = $download_id;
    }

    $prefix = PixelYourSite\Pinterest()->getOption( 'edd_content_id_prefix' );
    $suffix = PixelYourSite\Pinterest()->getOption( 'edd_content_id_suffix' );

    return $prefix . $content_id . $suffix;

}