<?php
require "wp-content/plugins/test/includes/api.php";

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (!function_exists('wp_remote_post')) {
            function wp_remote_post($url, $args)
            {
                global $mocked_wp_remote_post_response;
                return $mocked_wp_remote_post_response;
            }
        }

        if (!function_exists('is_wp_error')) {
            function is_wp_error($thing)
            {
                return $thing instanceof WP_Error;
            }
        }

        if (!function_exists('wp_remote_retrieve_body')) {
            function wp_remote_retrieve_body($response)
            {
                return $response['body'];
            }
        }
    }

    public function testPostSuccess()
    {
        global $mocked_wp_remote_post_response;
        $mocked_wp_remote_post_response = [
            'body' => json_encode(['success' => true])
        ];

        $api = new Api();
        $fields = ['field1' => 'value1'];

        $result = $api->post($fields);

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    public function testPostFailure()
    {
        global $mocked_wp_remote_post_response;
        $mocked_wp_remote_post_response = new WP_Error('error', 'Something went wrong');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error on request API :(');

        $api = new Api();
        $fields = ['field1' => 'value1'];

        $api->post($fields);
    }
}

class WP_Error
{
    private $code;
    private $message;

    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function get_error_code()
    {
        return $this->code;
    }

    public function get_error_message()
    {
        return $this->message;
    }
}