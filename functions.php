<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'spinupwp_purge_site' ) ) {
    /**
     * Purge the entire SpinupWP page cache.
     *
     * @return bool
     */
    function spinupwp_purge_site() {
        return spinupwp()->cache->purge_page_cache();
    }
}

if ( ! function_exists( 'spinupwp_purge_post' ) ) {
    /**
     * Purge a single post from the SpinupWP page cache.
     *
     * @param \WP_Post $post
     * @return bool
     */
    function spinupwp_purge_post( $post ) {
        return spinupwp()->cache->purge_post( $post );
    }
}

if ( ! function_exists( 'spinupwp_purge_url' ) ) {
    /**
     * Purge a single URL from the SpinupWP page cache.
     *
     * @param string $url
     * @return bool
     */
    function spinupwp_purge_url( $url ) {
        return spinupwp()->cache->purge_url( $url );
    }
}