<?php

namespace SpotifyWebApiSdk\Services;

use SpotifyWebApiSdk\SpotifyPagination;

/**
 * Spotify Browse Service
 */
class Browse {
	const GET_CATEGORY = '/v1/browse/categories/{category_id}';
	const GET_CATEGORY_PLAYLISTS = '/v1/browse/categories/{category_id}/playlists';
	const GET_CATEGORIES_LIST = '/v1/browse/categories';
	const GET_FEATURED_PLAYLISTS = '/v1/browse/featured-playlists';
	const GET_NEW_RELEASES = '/v1/browse/new-releases';
	const GET_RECOMMENDATIONS_SEEDS = '/v1/recommendations';


	/**
	 * Get a Category
	 * Authorization - Required
	 *
	 * @param string $category_id I'd of category.
	 */
	public static function getCategory( string $category_id ): array {
		SpotifyPagination::setHasPagination( false );

		return [
			'requestType' => 'GET',
			'uri'         => str_replace( '{category_id}', $category_id, self::GET_CATEGORY ),
		];
	}

	/**
	 * Get a Category's Playlists
	 * Authorization - Required
	 *
	 * @param string $category_id I'd of category.
	 */
	public static function getCategoryPlaylists( string $category_id ): array {
		SpotifyPagination::setHasPagination( true );

		return [
			'requestType' => 'GET',
			'uri'         => str_replace( '{category_id}', $category_id, self::GET_CATEGORY_PLAYLISTS ),
		];
	}

	/**
	 * Get a List of Categories
	 * Authorization - Required
	 */
	public static function getCategoriesList(): array {
		SpotifyPagination::setHasPagination( true );

		return [
			'requestType' => 'GET',
			'uri'         => self::GET_CATEGORIES_LIST,
		];
	}

	/**
	 * Get a List of Featured Playlists
	 * Authorization - Required
	 */
	public static function getFeaturedPlaylists(): array {
		SpotifyPagination::setHasPagination( true );

		return [
			'requestType' => 'GET',
			'uri'         => self::GET_FEATURED_PLAYLISTS,
		];
	}

	/**
	 * Get a List of New Releases
	 * Authorization - Required
	 */
	public static function getNewReleases(): array {
		SpotifyPagination::setHasPagination( true );

		return [
			'requestType' => 'GET',
			'uri'         => self::GET_NEW_RELEASES,
		];
	}

	/**
	 * Get Recommendations Based on Seeds
	 * Authorization - Required
	 *
	 * @param array $seed_artists - Artists ids
	 * @param array $seed_genres - Genres
	 * @param array $seed_tracks - Tracks ids
	 * @param array $optional - Optional parameters. Eg. ['limit' => 20]
	 */
	public static function getRecommendationsSeeds(
		array $seed_artists = [], array $seed_genres = [],
		array $seed_tracks = [], array $optional = []
	): array {
		$query_params                 = [];
		$query_params['seed_artists'] = implode( ',', array_slice( $seed_artists, 0, 5 ) );
		$query_params['seed_genres']  = implode( ',', array_slice( $seed_genres, 0, 5 ) );
		$query_params['seed_tracks']  = implode( ',', array_slice( $seed_tracks, 0, 5 ) );
		if ( count( $optional ) > 0 ) {
			$query_params = array_merge( $query_params, $optional );
		}

		SpotifyPagination::setHasPagination( false );

		return [
			'requestType'    => 'GET',
			'setQueryParams' => $query_params,
			'uri'            => self::GET_RECOMMENDATIONS_SEEDS,
		];
	}
}
