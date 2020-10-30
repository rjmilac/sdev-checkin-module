<?php
    /**
     *  CheckIn Model
     *
     *
     * @package SDEV
     * @subpackage SDEV WP
     * @since 1.0
     */

    namespace SDEV\Model;

    class CheckIn extends \SDEV\Model implements \SDEV\Interfaces\ModelDataObject{

        public $post_type, $post_data = array();

        public function __construct($wp_post = null){
            parent::__construct();
            $this->post_type = 'cpt_checkin';
            if($wp_post && $wp_post instanceof \WP_Post){
                $this->createFromWPPostObject($wp_post);
            } else {
                $this->setID($wp_post);
            }
        }

        public function setPostData($key, $value){
            $this->post_data[$key] = $value;
            return $this;
        }

        public function save(){
            if(!$this->ID){

                $this->ID = wp_insert_post( 
                    array(
                        'post_type' => $this->post_type,
                        'post_title'    => wp_strip_all_tags( $this->post_data['post_title'] ),
                        'post_status' => $this->post_data['post_status']
                    )
                );

            }

            if($this->ID){
                foreach($this->data as $k => $d):
                    update_field($k, $d, $this->ID);
                endforeach;
            }

            return $this;
        }

    }

?>