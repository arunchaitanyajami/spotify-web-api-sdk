<?php

namespace SpotifyWebApiSdk\Services;

use \SpotifyWebApiSdk\SpotifyPagination;

/**
 * Spotify Albums Service.
 */
class Albums {
	const GET_ALBUMS = '/v1/albums';
	const GET_ALBUM_TRACKS = '/v1/albums/{id}/tracks';
	const GET_ALBUM = '/v1/albums/{id}';

	/**
	 * Get Several Albums
	 * Authorization - Required
	 *
	 * @param array $ids Array with ids of albums.
	 */
	public static function getAlbums( array $ids ): array {
		SpotifyPagination::setHasPagination( false );
		$ids_string = implode( ',', $ids );

		return [
			'queryString' => [ 'ids' => $ids_string ],
			'requestType' => 'GET',
			'uri'         => self::GET_ALBUMS,
		];
	}

	/**
	 * Get an Album's Tracks
	 * Authorization - Required
	 *
	 * @param string $id I'd of album.
	 */
	public static function getTracks( string $id ): array {
		SpotifyPagination::setHasPagination( true );

		return [
			'requestType' => 'GET',
			'uri'         => str_replace( '{id}', $id, self::GET_ALBUM_TRACKS ),
		];
	}

	/**
	 * Get an Album
	 * Authorization - Required
	 *
	 * @param string $id I'd of album.
	 */
	public static function getAlbum( string $id ): array {
		SpotifyPagination::setHasPagination( false );

		return [
			'requestType' => 'GET',
			'uri'         => str_replace( '{id}', $id, self::GET_ALBUM ),
		];
	}
}
