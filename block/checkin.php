<?php
    /**
     *  CheckIn BLock
     *
     *
     * @package SDEV
     * @subpackage SDEV WP
     * @since 1.0
     */

    namespace SDEV\Block;

    class CheckIn extends \SDEV\Block{

        public function __construct(){
            parent::__construct();    
            $this->taxonomy = 'ct_sessions';
        }

        public function record($title, $data = array()){

            $checkin = new \SDEV\Model\CheckIn();
            $checkin->setPostData('post_title', wp_strip_all_tags( $title ))->setPostData('post_status', 'private');

            foreach($data as $key => $value){
                $checkin->setData($key, $value);
            }

            return $checkin->save();

        }

        public function isCheckedIn($email, $key, $return_id = false){
            $checkin = new \SDEV\Model\CheckIn(null);
            $query = new \WP_Query( array(
                'post_type' =>  $checkin->post_type,
                'post_status' => array('publish', 'private'),
                'posts_per_page' => 1,
                'meta_query' => array(
                    'relation' => 'and',
                    array(
                        'key' => 'email',
                        'value' => $email,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => 'key',
                        'value' => $key,
                        'compare' => 'LIKE'
                    )
                )
            ));
            if($query){
                if(!empty($query->posts)){
                    if($return_id){
                        return $query->posts[0]->ID;
                    } else{
                        return true;
                    }
                }
            }
            return false;
        }

    }

?>