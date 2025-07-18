<?php

namespace SpotifyWebApiSdk;

/**
 * @author Kiril Kirkov
 * Spotify Service Api Prepared Requests
 */
class SpotifyServices {
	const AUTHORIZE = '/authorize';
	const TOKEN = '/api/token';

	public static function albums(): Services\Albums {
		return new \SpotifyWebApiSdk\Services\Albums();
	}

	public static function artists(): Services\Artists {
		return new \SpotifyWebApiSdk\Services\Artists();
	}

	public static function browse(): Services\Browse {
		return new \SpotifyWebApiSdk\Services\Browse();
	}

	public static function follow(): Services\Follow {
		return new \SpotifyWebApiSdk\Services\Follow();
	}

	public static function library(): Services\Library {
		return new \SpotifyWebApiSdk\Services\Library();
	}

	public static function personalization(): Services\Personalization {
		return new \SpotifyWebApiSdk\Services\Personalization();
	}

	public static function player(): Services\Player {
		return new \SpotifyWebApiSdk\Services\Player();
	}

	public static function playlists(): Services\Playlists {
		return new \SpotifyWebApiSdk\Services\Playlists();
	}

	public static function search(): Services\Search {
		return new \SpotifyWebApiSdk\Services\Search();
	}

	public static function tracks(): Services\Tracks {
		return new \SpotifyWebApiSdk\Services\Tracks();
	}

	public static function users(): Services\UsersProfile {
		return new \SpotifyWebApiSdk\Services\UsersProfile();
	}

	public static function authorize(): array {
		return [
			'requestType' => 'GET',
			'uri'         => self::AUTHORIZE,
		];
	}

	public static function token(): array {
		return [
			'requestType' => 'POST',
			'uri'         => self::TOKEN,
		];
	}
}
