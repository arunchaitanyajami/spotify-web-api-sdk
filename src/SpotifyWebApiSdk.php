<?php

namespace SpotifyWebApiSdk;

use \GuzzleHttp\Client as GuzzleClient;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * Spotify Web Api Sdk
 */
class SpotifyWebApiSdk {
	const ACCOUNT_URL = 'https://accounts.spotify.com';
	const API_URL = 'https://api.spotify.com';

	// User Credentials
	private $accessToken;
	private $refreshToken;
	private $clientId;
	private $clientSecret;
	private $redirectUrl;

	// Request params
	private $baseUri;
	private $requestType = 'GET';
	private $requestParams = [];
	private $uri;
	private $headers = [
		'Accept' => 'application/json',
	];

	// Request result params
	private $rawResponseBody;
	private $response;
	private $responseHeaders;

	// Refresh token
	private $lastRequest = [];
	private $returnNewTokenIfIsExpired = false;

	/**
	 * @param array $credentials User credentials
	 * - Client I'd.
	 * - Client Secret.
	 * - (Optional) Refresh Token.
	 * - (Optional) Access Token.
	 */
	public function __construct( array $credentials = [] ) {
		if ( ! empty( $credentials ) ) {
			$this->setCredentials( $credentials );
		}
	}

	private function setCredentials( $credentials ) {
		if ( isset( $credentials['accessToken'] ) ) {
			$this->setAccessToken( $credentials['accessToken'] );
		}
		if ( isset( $credentials['refreshToken'] ) ) {
			$this->setRefreshToken( $credentials['refreshToken'] );
		}
		if ( isset( $credentials['clientId'] ) ) {
			$this->setClientId( $credentials['clientId'] );
		}
		if ( isset( $credentials['clientSecret'] ) ) {
			$this->setClientSecret( $credentials['clientSecret'] );
		}
	}

	public function setPaginationLimit( int $limit ) {
		SpotifyPagination::setLimit( $limit );
	}

	public function setPaginationOffset( int $offset ) {
		SpotifyPagination::setOffset( $offset );
	}

	public function getPaginationTotal() {
		return SpotifyPagination::getTotal();
	}

	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * Set Generated Refresh Token
	 *
	 * @param string $refreshToken Valid refresh token.
	 */
	public function setRefreshToken( string $refreshToken ) {
		$this->refreshToken = $refreshToken;
	}

	public function getRefreshToken() {
		return $this->refreshToken;
	}

	/**
	 * Set Client I'd
	 *
	 * @param string $clientId Valid client id.
	 */
	public function setClientId( string $clientId ) {
		$this->clientId = $clientId;
	}

	public function getClientId() {
		return $this->clientId;
	}

	public function setRequestType( string $method ) {
		$this->requestType = strtoupper( $method );
	}

	public function getRequestType(): string {
		return $this->requestType;
	}

	public function setQueryString( array $params ): SpotifyWebApiSdk {
		$this->requestParams['query'] = $params;

		return $this;
	}

	public function getQueryString() {
		return $this->requestParams['query'] ?? null;
	}

	/**
	 * @param array $params Set params for auth header
	 */
	private function setAuthParams( array $params ): SpotifyWebApiSdk {
		$this->requestParams['auth'] = $params;

		return $this;
	}

	private function getHeaders(): array {
		return $this->headers;
	}

	private function getBaseUri() {
		return $this->baseUri;
	}

	private function setBaseUri( string $base_uri ) {
		$this->baseUri = $base_uri;
	}

	/**
	 * @param array $value Value of guzzle query full array
	 */
	private function setQueryParams( array $value ) {
		$this->requestParams['query'] = $value;
	}

	/**
	 * @return array All params for the guzzle query
	 */
	private function getRequestParams(): array {
		return $this->requestParams;
	}

	/**
	 * @param array $arrays Set full array
	 */
	private function setRequestParams( array $arrays ): SpotifyWebApiSdk {
		$this->requestParams = $arrays;

		return $this;
	}

	/**
	 * @param string $key Name of guzzle form_params parameter
	 * @param string $value Value of guzzle form_params parameter
	 */
	private function setFormParam( string $key, string $value ): SpotifyWebApiSdk {
		$this->requestParams['form_params'][ $key ] = $value;

		return $this;
	}

	/**
	 * @param array $params
	 *
	 * @return SpotifyWebApiSdk
	 */
	private function setFormParams( array $params ): SpotifyWebApiSdk {
		$this->requestParams['form_params'] = $params;

		return $this;
	}

	private function setResponseHeaders( $headers ): SpotifyWebApiSdk {
		$this->responseHeaders = $headers;

		return $this;
	}

	private function getResponseHeaders() {
		return $this->responseHeaders;
	}

	/**
	 * Set Client Secret.
	 *
	 * @param string $clientSecret Valid client secret.
	 */
	public function setClientSecret( string $clientSecret ) {
		$this->clientSecret = $clientSecret;
	}

	public function getClientSecret() {
		return $this->clientSecret;
	}

	/**
	 * @param string $uri Api uri
	 */
	public function setUri( string $uri ): SpotifyWebApiSdk {
		$this->uri = '/' . ltrim( $uri, '/' );

		return $this;
	}

	public function getUri() {
		return $this->uri;
	}

	public function provider( array $service ): SpotifyWebApiSdk {
		array_walk( $service, function ( &$value, &$key ) {
			if ( method_exists( $this, $key ) ) {
				$this->{$key}( $value );
			} else if ( property_exists( $this, $key ) ) {
				$this->{$key} = $value;
			}
		} );

		return $this;
	}

	/**
	 * Generate token with code - Step 1/2
	 * Send user to login after that redirect back with code for access token
	 *
	 * @param string|null $redirectUri Callback url with returned $_GET['code'].
	 * @param string|null $clientId Optional Client Id if is not set in instance constructor.
	 * @param array $options Optional. Parameters - scope, show_dialog or state.
	 *
	 * @return string Authorization url to open in browser
	 */
	public function getUrlForCodeToken( string $redirectUri = null, string $clientId = null, array $options = [] ): string {
		$account = $this->account()->provider( SpotifyServices::authorize() );
		$qString = http_build_query( [
			'client_id'     => $clientId ?? $this->getClientId(),
			'redirect_uri'  => $redirectUri,
			'response_type' => 'code',
			'scope'         => $options['scope'] ?? null,
			'show_dialog'   => $options['show_dialog'] ?? null,
			'state'         => $options['state'] ?? null,
		] );

		return (string) $this->getBaseUri() . $this->getUri() . '?' . $qString;
	}

	/**
	 * Generate token with code - Step 2/2
	 * Get the access token with the returned code
	 *
	 * @param string $code Code for token.
	 * @param string $redirectUri Callback url with returned access token.
	 *
	 * @return array Access Token and Refresh Token
	 * @throws SpotifyWebAPIException
	 */
	public function getAccessTokenWithCode( string $code, string $redirectUri ): array {
		$this->setAuthParams( [ $this->getClientId(), $this->getClientSecret() ] );

		$response = $this->account()->provider( SpotifyServices::token() )->setFormParams( [
			'code'         => $code,
			'grant_type'   => 'authorization_code',
			'redirect_uri' => $redirectUri,
		] )->getResult();

		if ( ! isset( $response->access_token ) ) {
			throw new SpotifyWebAPIException( 'Access token missing in response' );
		}

		return [ 'accessToken' => $response->access_token, 'refreshToken' => $response->refresh_token ];
	}

	/**
	 * Get access token with client credentials
	 * Access token expires in 24 hours
	 *
	 * @param string|null $clientId Client id.
	 * @param string|null $clientSecret Client secret.
	 *
	 * @return string Access Token
	 * @throws SpotifyWebAPIException
	 */
	public function getAccessTokenWithCredentials( string $clientId = null, string $clientSecret = null ): string {
		if ( $clientId != null ) {
			$this->setClientId( $clientId );
		}
		if ( $clientSecret != null ) {
			$this->setClientSecret( $clientSecret );
		}

		$this->setAuthParams( [ $this->getClientId(), $this->getClientSecret() ] );

		$response = $this->account()->provider( SpotifyServices::token() )->setFormParams( [
			'grant_type' => 'client_credentials',
		] )->getResult();

		if ( ! isset( $response->access_token ) ) {
			throw new SpotifyWebAPIException( 'Access token missing in response' );
		}

		return $response->access_token;
	}

	/**
	 * Set Generated Access Token
	 *
	 * @param string $acccessToken Valid access token.
	 */
	public function setAccessToken( string $accessToken ) {
		$this->accessToken = $accessToken;
		$this->setHeaders( [ 'Authorization' => 'Bearer ' . $this->accessToken ] );

		return $this;
	}

	public function account() {
		$this->setBaseUri( rtrim( self::ACCOUNT_URL, '/' ) );

		return $this;
	}

	public function api() {
		$this->setBaseUri( rtrim( self::API_URL, '/' ) );

		return $this;
	}

	/**
	 * @param string|array $headers Headers to send
	 */
	public function setHeaders( $headers ) {
		if ( $headers === null ) {
			$this->headers = [];

			return $this;
		}
		if ( is_array( $headers ) ) {
			foreach ( $headers as $key => $value ) {
				$this->headers[ $key ] = $value;
			}
		}

		return $this;
	}

	private function clearAccessToken() {
		$this->accessToken = null;
		unset( $this->headers['Authorization'] );
	}

	public function sendRequest() {
		$this->paginationCheck();
		try {
			$client   = new GuzzleClient( [ 'base_uri' => $this->getBaseUri(), 'headers' => $this->getHeaders() ] );
			$response = $client->request( $this->getRequestType(), $this->getUri(), $this->getRequestParams() );
			$body     = $response->getBody();
			$this->setResponseHeaders( $response->getHeaders() );
			$this->response = $this->parseRawResponse( (string) $body );
		} catch ( \GuzzleHttp\Exception\ClientException $e ) {
			$responseBody = json_decode( $e->getResponse()->getBody()->getContents() );
			if ( isset( $responseBody->error ) ) {
				$error = $responseBody->error->message ?? $responseBody->error;
			} else {
				$error = $e->getMessage();
			}
			$this->setResponseHeaders( $e->getResponse()->getHeaders() );
			$this->errorHandler( new SpotifyWebAPIException( $error, $e->getCode() ) );
		} catch ( SpotifyWebAPIException $e ) {
			throw new SpotifyWebAPIException( $e->getMessage() );
		}

		return $this;
	}

	/**
	 * Set pagination if has
	 */
	private function paginationCheck() {
		$currentQueryString = $this->getQueryString();
		if ( SpotifyPagination::getHasPagination() ) {
			$pagination = [
				'limit'  => SpotifyPagination::getLimit(),
				'offset' => SpotifyPagination::getOffset()
			];
			if ( ! is_null( $currentQueryString ) ) {
				$currentQueryString = array_merge( $currentQueryString, $pagination );
				$this->setQueryString( $currentQueryString );

				return $this;
			}
			$this->setQueryString( $pagination );

			return $this;
		} else {
			if ( ! is_null( $currentQueryString ) ) {
				unset( $currentQueryString['limit'], $currentQueryString['offset'] );
				$this->setQueryString( $currentQueryString );
			}
		}
	}

	private function errorHandler( SpotifyWebAPIException $e ) {
		if ( $e->hasExpiredToken() ) {
			$this->clearAccessToken();
			if ( $this->returnNewTokenIfIsExpired === false ) {
				$this->refreshTokenAndReCallLastRequest();
			} else {
				$this->refreshTokenAndReturnBack();
			}
		} elseif ( $e->invalidClient() ) {
			throw new SpotifyWebAPIException( 'Probably missing header Content-Type: application/x-www-form-urlencoded' );
		} elseif ( $e->isRateLimited() ) {
			$responseHeaders  = $this->getResponseHeaders();
			$retryAfter       = $responseHeaders['Retry-After'] ?? 1;
			$retryRequestTime = (int) $retryAfter;
			sleep( $retryRequestTime );
			$this->getResult();
		} else {
			throw new SpotifyWebAPIException( $e->getMessage() );
		}
	}

	private function refreshTokenAndReturnBack() {
		$result = $this->refreshAccessToken();
		if ( ! isset( $result->access_token ) ) {
			throw new SpotifyWebAPIException( 'Cant find access token in refresh token response' );
		}

		return $result;
	}

	private function refreshTokenAndReCallLastRequest() {
		$this->setLastRequest();
		$result = $this->refreshAccessToken();
		if ( isset( $result->access_token ) ) {
			$this->setAccessToken( $result->access_token )->returnLastRequest()->getResult();
		} else {
			throw new SpotifyWebAPIException( 'Cant find access token in refresh token response' );
		}
	}

	/**
	 * @param boolean $status Return as result new token if is expired
	 */
	public function returnNewTokenIfIsExpired( $status = true ) {
		$this->returnNewTokenIfIsExpired = $status;
	}

	private function setLastRequest() {
		$this->lastRequest = [
			'requestParams' => $this->getRequestParams(),
			'requestType'   => $this->getRequestType(),
			'baseUri'       => $this->getBaseUri(),
			'uri'           => $this->getUri(),
		];
	}

	private function returnLastRequest() {
		if ( isset( $this->lastRequest['requestParams'] ) ) {
			$this->setRequestParams( $this->lastRequest['requestParams'] );
		}
		if ( isset( $this->lastRequest['requestType'] ) ) {
			$this->setRequestType( $this->lastRequest['requestType'] );
		}
		if ( isset( $this->lastRequest['baseUri'] ) ) {
			$this->setBaseUri( $this->lastRequest['baseUri'] );
		}
		if ( isset( $this->lastRequest['uri'] ) ) {
			$this->setUri( $this->lastRequest['uri'] );
		}

		return $this;
	}

	public function getResponse() {
		return $this->response;
	}

	/**
	 * Send prepared request and return parsed response
	 */
	public function getResult() {
		return $this->sendRequest()->getResponse();
	}

	private function parseRawResponse( $rawResponseBody ) {
		$decodedResponse = json_decode( $rawResponseBody );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new SpotifyWebAPIException( 'The response from Spotify is not valid json' );
		}
		SpotifyPagination::parsePagination( $decodedResponse );

		return $decodedResponse;
	}

	/**
	 * Auto refresh expired token
	 *
	 * @return string Access Token
	 */
	public function refreshAccessToken() {
		$this->setAuthParams( [ $this->getClientId(), $this->getClientSecret() ] );
		try {
			return $this->account()->provider( SpotifyServices::token() )->setFormParams( [
				'grant_type'    => 'refresh_token',
				'refresh_token' => $this->getRefreshToken(),
			] )->getResult();
		} catch ( SpotifyWebAPIException $e ) {
			throw new SpotifyWebAPIException( 'Cant Refresh Access Token - ' . $e->getMessage() );
		}
	}
}
