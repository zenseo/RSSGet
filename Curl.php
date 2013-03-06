<?php

/**
 * A simple curl wrapper
 * @package Curl
 * @author  Aleksandr Zelenin <aleksandr@zelenin.me>
 * @link    https://github.com/zelenin/Curl
 * @version 0.3
 * @license http://opensource.org/licenses/gpl-3.0.html GPL-3.0
 */

namespace Zelenin;

class Curl
{
	const VERSION = '0.3';
	protected $request;
	private $user_agent;

	public function __construct()
	{
		if ( !function_exists( 'curl_init' ) ) return false;
		return $this->set_user_agent( 'Curl ' . self::VERSION . ' (https://github.com/zelenin/curl)' );
	}

	private function request( $url, $data = null, $method = 'get', $headers = null, $cookie = null )
	{
		if ( !$url ) return false;

		if ( $method == 'get' && $data ) {
			$url = is_array( $data ) ? trim( $url, '/' ) . '/?' . http_build_query( $data ) : trim( $url, '/' ) . '/?' . $data;
		}
		$this->request = curl_init( $url );

		$options = array(
			CURLOPT_HEADER => true,
			CURLOPT_NOBODY => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => $this->user_agent,
			CURLOPT_SSL_VERIFYPEER => false
		);

		if ( $method == 'post' && $data ) {
			$options[CURLOPT_POSTFIELDS] = is_array( $data ) ? http_build_query( $data ) : $data;
		}

		if ( $headers ) {
			$options[CURLOPT_HTTPHEADER] = $headers;
		}

		if ( $cookie ) {
			$options[CURLOPT_COOKIEFILE] = $cookie;
			$options[CURLOPT_COOKIEJAR] = $cookie;
		}

		curl_setopt_array( $this->request, $options );
		$result = curl_exec( $this->request );

		if ( $result ) {
			$info = curl_getinfo( $this->request );
			$response = $this->parse_response( $result );
			$response['info'] = $info;
		} else {
			$response = array(
				'number' => curl_errno( $this->request ),
				'error' => curl_error( $this->request )
			);
		}
		curl_close( $this->request );
		return $response;
	}

	public function get( $url, $data = null, $headers = null, $cookie = null )
	{
		return $this->request( $url, $data, $method = 'get', $headers, $cookie );
	}

	public function post( $url, $data = null, $headers = null, $cookie = null )
	{
		return $this->request( $url, $data, $method = 'post', $headers, $cookie );
	}

	private function parse_response( $response )
	{
		$response_parts = explode( "\r\n\r\n", $response, 2 );
		$response = array();
		$cookie = array();

		$response['header'] = explode( "\r\n", $response_parts[0] );

		if ( preg_match_all( '/Set-Cookie: (.*?)=(.*?)(\n|;)/i', $response_parts[0], $matches ) ) {
			if ( !empty( $matches ) ) {
				foreach ( $matches[1] as $key => $value ) {
					$cookie[] = $value . '=' . $matches[2][$key] . ';';
				}
				$response['cookie'] = $cookie;
			}
		}
		$response['body'] = $response_parts[1];
		return $response;
	}

	public function set_user_agent( $user_agent )
	{
		$this->user_agent = $user_agent;
		return $this;
	}
}