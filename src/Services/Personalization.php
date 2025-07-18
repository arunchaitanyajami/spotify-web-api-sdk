<?php

namespace SpotifyWebApiSdk\Services;

use SpotifyWebApiSdk\SpotifyPagination;

/**
 * Spotify Personalization Service
 */
class Personalization {
	const GET_TOP = '/v1/me/top/{type}';

	/**
	 * Get a User's Top Artists and Tracks
	 * Authorization - Required
	 *
	 * @param string $type The type of entity to return. Valid values: artists or tracks.
	 */
	public static function getTop( string $type ): array {
		SpotifyPagination::setHasPagination( true );

		return [
			'requestType' => 'GET',
			'uri'         => str_replace( '{type}', $type, self::GET_TOP ),
		];
	}
}
