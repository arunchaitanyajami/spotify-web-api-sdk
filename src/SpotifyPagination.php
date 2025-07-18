<?php

namespace SpotifyWebApiSdk;

class SpotifyPagination {

	private static $hasPagination = false;
	private static $total;
	private static $limit = 20;
	private static $offset = 0;

	public static function setHasPagination( $status ) {
		self::$hasPagination = (boolean) $status;
	}

	public static function getHasPagination(): bool {
		return self::$hasPagination;
	}

	public static function setTotal( $total ) {
		self::$total = (int) $total;
	}

	public static function getTotal() {
		return self::$total;
	}

	public static function setLimit( $limit ) {
		if ( (int) $limit > 0 ) {
			self::$limit = (int) $limit;
		}
	}

	public static function getLimit(): int {
		return self::$limit;
	}

	public static function setOffset( $offset ) {
		self::$offset = (int) $offset;
	}

	public static function getOffset(): int {
		return self::$offset;
	}

	public static function parsePagination( $response ) {
		if ( isset( $response->limit ) ) {
			self::setLimit( $response->limit );
		}
		if ( isset( $response->offset ) ) {
			self::setOffset( $response->offset );
		}
		if ( isset( $response->total ) ) {
			self::setTotal( $response->total );
		}
		unset( $response );
	}
}
