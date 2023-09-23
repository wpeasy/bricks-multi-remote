<?php

namespace BricksMultiRemote;

use Bricks\Helpers;

class App
{
    const TEXT_DOMAIN = 'bricks-multi-remote';

    private static $instance = null;

    public $config;

    public static function getInstance($config = null)
    {
        if (null === self::$instance) {
            self::$instance = new self();
            if($config){
                self::$instance->config = $config;
            }
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->config = get_option('bmr_settings', []);
        add_filter('bricks/get_remote_templates_data', [$this, 'get_more_remote_templates']);

        Settings::getInstance();
    }

    public function get_more_remote_templates($remote_templates_data)
    {
        $conf = $this->config['remote_templates'];
        $lines = explode("\n", $conf);

        foreach ($lines as $line) {
            list($url, $password) = explode('|', $line);
            $data = $this->_get_remote_templates(trim($url), trim($password));
            !empty($data['templates']) && $remote_templates_data['templates'] = array_merge($remote_templates_data['templates'], $data['templates']);
            !empty($data['authors']) && $remote_templates_data['authors'] = array_merge($remote_templates_data['authors'], $data['authors']);
            !empty($data['bundles']) && $remote_templates_data['bundles'] = array_merge($remote_templates_data['bundles'], $data['bundles']);
            !empty($data['tags']) && $remote_templates_data['tags'] = array_merge($remote_templates_data['tags'], $data['tags']);
        }
        return $remote_templates_data;
    }

    private function _get_remote_templates($url, $password)
    {
        // Remote templates data

        $remote_templates_url = rtrim($url, '/') . '/wp-json/bricks/v1/get-templates-data';
        $remote_templates_url = add_query_arg( [ 'site' => get_site_url() ], $remote_templates_url );
        $remote_templates_url = add_query_arg( [ 'password' => urlencode( $password ) ], $remote_templates_url );
        $remote_templates_url = add_query_arg( [ 'time' => time() ], $remote_templates_url );
        $remote_templates_response = Helpers::remote_get( $remote_templates_url );

        // Error handling
        if ( is_wp_error( $remote_templates_response ) ) {
            wp_send_json_error(['error' => wp_strip_all_tags( $remote_templates_response->get_error_message() )]);
        }

        $response_body = json_decode( wp_remote_retrieve_body( $remote_templates_response ), true );

        if( isset($response_body['error'])){
            wp_send_json_error(['error' => $url . ': ' . $response_body['error']['message']]);
        }

        return $response_body;
    }
}