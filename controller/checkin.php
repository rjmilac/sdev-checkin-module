<?php
    /**
     *  Checkin Controller
     *
     *
     * @package SDEV
     * @subpackage SDEV WP
     * @since 1.0
     */

    namespace SDEV\Controller\Event;

    class Checkin extends \SDEV\Controller implements \SDEV\Interfaces\WPXHRActionControllerInterface {

        protected $_block;

        public function __construct(){
            parent::__construct();
        }

        public function registerActions(){
            add_action( 'wp_ajax_event_checkin', array($this, 'checkIn') );
            add_action( 'wp_ajax_nopriv_event_checkin', array($this, 'checkIn') );
        }

        public function checkIn(){

            $response = [
                'success' => true,
                'code' => 'SUCCESS',
            ];

            $_login = new \SDEV\Block\Event\Login();
            $_checkIn = new \SDEV\Block\CheckIn();

            if(!empty($_login->getUserEmail()) && !$_checkIn->isCheckedIn($_login->getUserEmail(), $this->getPostData('key'))){

                $tag = preg_replace('%,%', '', $this->getPostData('session'));
                $user_id = $_login->getUserID();

                $record = $_checkIn->record($_login->getUserEmail(), [
                    'name' => get_field('registrant_name', $user_id),
                    'last_name' => get_field('registrant_last_name', $user_id),
                    'email' => $_login->getUserEmail(),
                    'company' => get_field('company', $user_id),
                    'job_title' => get_field('job_title', $user_id),
                    'state' => get_field('state', $user_id),
                    'session' => $this->getPostData('session'),
                    'metadata' => $this->getPostData('metadata'),
                    'key' => $this->getPostData('key'),
                    'event_name' => $this->getPostData('event'),
                    'timestamp' => time()+((get_field('time_offset', 'option')) ? get_field('time_offset', 'option') : 0),
                    'timezone' => get_field('timezone_label', 'option')
                ]);

                if(!empty($tag) && $record->getID()){
                    wp_set_post_terms( $record->getID(), $tag, $_checkIn->taxonomy );
                }

            }

            echo json_encode($response);
            exit;

        }

    }

?>