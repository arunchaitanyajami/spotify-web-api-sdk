<?php

namespace SpotifyWebApiSdk\Services;

use SpotifyWebApiSdk\SpotifyPagination;

/**
 * Spotify Search Service
 */
class Search {
	const SEARCH = '/v1/search';

	/**
	 * Search for an Item
	 * Authorization - Required
	 *
	 * @param string $q - Search query keywords and optional field filters and operators.
	 * @param string|array $type - A comma-separated list of item types to search across. Valid types are: album , artist, playlist, and track.
	 */
	public static function get( string $q, $type ): array {
		if ( is_array( $type ) ) {
			$type = implode( ',', $type );
		}

		SpotifyPagination::setHasPagination( true );

		return [
			'setQueryParams' => [ 'q' => $q, 'type' => $type ],
			'requestType'    => 'GET',
			'uri'            => self::SEARCH,
		];
	}
}
