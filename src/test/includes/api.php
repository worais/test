<?php
class Api{
    public $url = 'https://httpbin.org/post';

	public function post( $fields ) {
        //wp_remote_post we can mock in phpunit
        $response = wp_remote_post( $this->url, array(
            'body'    => $fields,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( '?:?'),
            ),
        ) );  

        if ( ! is_wp_error( $response ) ) {
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            return $body;
        } else {
            throw new Exception( 'Error on request API :(' );
        }
	}    
}