<?php

class NGL_Sendy_API {

    const HTTP_METHOD_GET 		= 'GET';
    const HTTP_METHOD_POST 		= 'POST';
	const HTTP_METHOD_DELETE 	= 'DELETE';

    private $apiKey;
	private $apiUrl;

    /**
     * Constructor.
     */
    public function __construct( $api_url, $api_key ) {
		$this->apiUrl = $api_url;
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

		$body[ 'api_key' ] = $this->apiKey;

		$postdata 	= http_build_query( $body );

		$opts 		= array( 'http' => array( 'method'  => $method, 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata ) );
		$context  	= stream_context_create( $opts );
		$result 	= file_get_contents( $this->apiUrl . $endpoint, false, $context );

		return $result;
    }

}