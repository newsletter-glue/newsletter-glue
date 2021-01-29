<?php

class NGL_GetResponse_API {

    const API_BASE_URL 		= 'https://api.getresponse.com/v3';
    const HTTP_METHOD_GET 	= 'GET';
    const HTTP_METHOD_POST 	= 'POST';

    private $apiKey;
    private $lastResponseCode;

    /**
     * NGL_SendinblueApiClient constructor.
     */
    public function __construct( $api_key ) {
        $this->apiKey = $api_key;
    }

    /**
     * @param $endpoint
     * @param array $parameters
     * @return mixed
     */
    public function get( $endpoint, $parameters = [] ) {
        if ( $parameters ) {
            foreach ( $parameters as $key => $parameter ) {
                if ( is_bool( $parameter ) ) {
                    // http_build_query converts bool to int
                    $parameters[ $key ] = $parameter ? 'true' : 'false';
                }
            }
            $endpoint .= '?' . http_build_query( $parameters );
        }
        return $this->makeHttpRequest( self::HTTP_METHOD_GET, $endpoint );
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function post($endpoint, $data = []) {
        return $this->makeHttpRequest( self::HTTP_METHOD_POST, $endpoint, $data );
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $body
     * @return mixed
     */
    private function makeHttpRequest( $method, $endpoint, $body = [] ) {
        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'timeout' => 10000,
            'method' => $method,
            'headers' => [
				'X-Auth-Token' 	=> "api-key {$this->apiKey}",
				'Content-Type'	=> "application/json",
            ],
        ];

        if ( $method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE ) {
            $args[ 'body' ] = wp_json_encode( $body );
        }

        $response = wp_remote_request($url, $args);
        $this->lastResponseCode = wp_remote_retrieve_response_code($response);

        if ( is_wp_error( $response ) ) {
            $data = [
                'code' => $response->get_error_code(),
                'message' => $response->get_error_message()
            ];
        } else {
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getLastResponseCode() {
        return $this->lastResponseCode;
    }

}