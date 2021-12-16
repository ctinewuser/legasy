<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set("Asia/Kolkata");
error_reporting(0);
class Api extends Base_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
        $this->data = json_decode(file_get_contents("php://input"));
    }
 ////////////////////////  //////category/////
    public function category_list() {
         $category = $this->common->category($_POST['search']);
        if (!empty($category)) {
            $this->response(true, "Success", array('category' => $category));
        } else {
            $this->response(false, "Category Not found");
        }
    }
    public function legacy_response()
    {
        $user_id  = $_POST['user_id'];
        if($_POST['type'] == '1'){
             $legacy_questions = $this->common->legacy_questions($user_id,1);
        }
       
        if($_POST['type'] == '2'){
             $legacy_questions = $this->common->legacy_questions($user_id,2);
        }
        if(!empty($legacy_questions)){
             $this->response(true, "legacy Fetch Successfully.", array("legacy_response"=>$legacy_questions));
         }else{
             $this->response(true, "legacy Fetch Successfully.", array("legacy_response"=>array()));
         } 
     
    }

/////////////////////////// legacy by category/////////////////////////////
 public function legacy_by_category()
    {
        $user_id  = $_POST['user_id'];
        $cat_id  = $_POST['cat_id'];
        if(!empty($cat_id)){
            $cat_name = category_name($cat_id);
        }else{
             $cat_name = "";
        }
        $result = $this->common->legacy_by_category($cat_id,$user_id);
        if (!empty($result)) {
            $this->response(true, "legacy Fetch Successfully.", array('cat_name'=>$cat_name,"legacy" => $result));
        } else {
            $this->response(false, "legacy Not Found", array('cat_name'=>$cat_name,"legacy" => array()));
        }
    }
//////////////////////// daily Question/////
    public function daily_question() {
        $daily_question = $this->common->getData('daily_question', array(''), array('single'));
        if (!empty($daily_question)) {
            $this->response(true, "Success", array('daily_question' => $daily_question));
        } else {
            $this->response(false, "daily question Not found");
        }
    }
/////////////////////////////////user module////////////////////////////////    
    public function signup() {
        if (!empty($_POST['email'])) {
            $_POST['status'] = 1;
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $_POST['password'] = md5($_POST['password']);
           
            $exist2 = $this->common->getData('user', array('email' => $_POST['email']), array('single'));
            if ($exist2) {
                $this->response(false, "email already exist");
                die;
            } else {
                $post = $this->common->getField('user', $_POST);
                $result = $this->common->insertData('user', $post);
                $user_id = $this->db->insert_id();
                if ($user_id) {
                    $user = $this->common->getData('user', array('id' => $user_id), array('single'));
                    $this->response(true, "Signup Succesfully!", array('userinfo' => $user));
                } else {
                    $this->response(false, " Invalid detail, please try again.");
                }
            }
        } else {
            $this->response(false, " Invalid detail, please try again.");
        }
    }
//////////////////////////////// login //////////////////////////////
    public function login() {
        $_POST['password'] = md5($_POST['password']);
        $result = array();
        $where = "email = '" . $_POST['email'] . "' AND password = '" . $_POST['password'] . "'";
        $result = $this->common->getData('user', $where, array('single'));
        if ($result) {
            if (isset($_REQUEST['android_token'])) {
                $old_device = $this->common->getData('user', array('android_token' => $_REQUEST['android_token']), array('single', 'field' => 'id'));
            }
            if (isset($_REQUEST['ios_token'])) {
                $old_device = $this->common->getData('user', array('ios_token' => $_REQUEST['ios_token']), array('single', 'field' => 'id'));
            }
            if ($old_device) {
                $this->common->updateData('user', array('android_token' => "", "ios_token" => ""), array('id' => $old_device['id']));
            }
            
            $this->common->updateData('user', array('ios_token' => $_REQUEST['ios_token'], 'android_token' => $_REQUEST['android_token'],'device_type'=>$_REQUEST['device_type']),array('id' => $result['id']));

            $result['android_token'] = $_REQUEST['android_token'];

            $this->response(true, 'Successfully Login', array("userinfo" => $result));
        } else {
            $message = "Wrong mobile number or password";
            $this->response(false, $message, array("userinfo" => (object) array()));
        }
    }
////////////////////terms_condition//////////////////////////////////////
    public function terms_condition() {
        $terms_services = $this->common->getData('site_detail', array('id' => 1), array('single'));
        if (!empty($terms_services)) {
            $this->response(true, "Success", array('site_detail' => $terms_services));
        } else {
            $this->response(false, "Services Not found");
        }
    }
    public function privacy_policy() {
        $privacy_policy = $this->common->getData('site_detail', array('id' => 2), array('single'));
        if (!empty($privacy_policy)) {
            $this->response(true, "Success", array('site_detail' => $privacy_policy));
        } else {
            $this->response(false, "Privacy Policy Not found");
        }
    }
 /////////////////////////userProfile///////////////////////////////   
 public function user_profile(){
        $user = $this->common->getData('user', array('id' => $_POST['user_id']), array('single'));
        if ($user) {
            $this->response(true, "Profile Fetch Successful", array("userinfo" => $user));
        } else {
            $this->response(false, "There Is a Problem, Please Try Again.", array("userinfo" => array()));
        }
    }
//////////////////////////////forgot_passowrd//////////////////////
    public function forgot_passowrd()
        {
        if(!empty($_REQUEST['email']))
            {
            $email=$_REQUEST['email'];
            $result = $this->common->getData('user',array('email'=>$_REQUEST['email'],'status'=>'1'),array('single'));
              
                if(empty($result))
                {
                    $this->response(false,"Invalid Email. Please try again.");
                }else{

                    $email=$_POST['email'];
                    $result['token'] = $this->generateToken();

                    $this->common->updateData('user',array('token'=>$result['token']),array('id' => $result['id']));

                    $message = $this->load->view('template/reset-mail', $result, true);
                    $this->common->sendMail($email,"Reset Email",$message);
                    $this->response(true, "Verified Email", array("userinfo" => $result));
                }
            }
            else
            {
                $this->response(false,"Missing parameter");
            }
        }
  public function change_passowrd()
       {
            if(!empty($_POST['email']))
            {
            $email = $_POST['email'];
            //$otp = $_POST['otp'];'otp'=>$otp,
            $newpassword = $_POST['password'];
            $result = $this->common->getData('user',array('email'=>$_REQUEST['email'],'status'=>'1'),array('single'));
                if(!empty($result))
                {
                    $this->common->updateData('user',array('password'=>md5($newpassword)),array('id' => $result['id']));
                    $this->response(true,"Password changed successfully",array('email' => $email,'password' => $newpassword));
                }
                else
                {
                    $this->response(false,"Invalid Detail. Please try again.");
                }
            }
            else
            {
                $this->response(false,"Missing parameter");
            }
        }
 //////////Update Profile
    public function update_profile() {
        $result = $this->common->getData('user', array('id' => $_POST['user_id']), array('single'));
        if ($result) {
             
                if (isset($_FILES['profile_image'])) {
                    $image = $this->common->do_upload('profile_image', './assets/userfile/profile/');
                    if (isset($image['upload_data'])) {
                        $_POST['profile_image'] = $image['upload_data']['file_name'];
                        $image_name = $_POST['profile_image'];
                    }
                }
            $post = $this->common->getField('user', $_POST);
            $info = $this->common->updateData('user', $post, array('id' => $_POST['user_id']));

            $user = $this->common->getData('user', array('id' => $_POST['user_id']), array('single'));
            $this->response(true, "Your Profile Is Updated Sucessfully.", array('userinfo' => $user));
        } else {
            $this->response(false, "There Is a Problem, Please Try Again.", array('userinfo' => (object)array()));
        }
    }
//////////////////////add_post///////////////////////////////
    public function add_post() {
        if (!empty($_POST['user_id'])) {
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $post = $this->common->getField('post', $_POST);
            $result = $this->common->insertData('post', $post);
            $postid = $this->db->insert_id();
      
                $_POST['images'] = '';
                if (isset($_FILES['images'])) {
                    $image = $this->common->do_upload('images', './assets/post/');
                    if (isset($image['upload_data'])) {
                        $_POST['images'] = $image['upload_data']['file_name'];

                        $res = $this->common->insertData('post_image', array('user_id' => $_POST['user_id'], 'post_id' => $postid, 'image' => $_POST['images'], 'post_type' => $_POST['post_type'],'created_at'=>$_POST['created_at']));
                    }
                }

            $this->response(true, "Post successfully added");

    }else {
            $this->response(false, "User Id field is mandatory");
        }
    }
/////////////add_genre////////////
    public function add_album() {
        if (!empty($_POST['user_id'])) {
            $post = $this->common->getField('albums', $_POST);
            $result = $this->common->insertData('albums', $post);
            $postid = $this->db->insert_id();
            $album = $this->common->getData('albums', array('user_id' => $_POST['user_id']), array());
        } else {
            $this->response(false, "User Id field is mandatory",array('albums'=>array()));
            exit();
        }
        $this->response(true, "album Added Successfully",array('albums'=>$album));
    }
///////////////////////////////album_by_user////////////////////////////////////
    public function  album_by_user() {
        if (!empty($_POST['user_id'])) {
            
            $album = $this->common->getData('albums', array('user_id' => $_POST['user_id']), array());
        } else {
            $this->response(false, "User Id field is mandatory",array('albums'=>array()));
            exit();
        }
        $this->response(true, "album Fetch Successfully",array('albums'=>$album));
    }

  public  function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
////////////////////////////////post_list///////////////////////////////////////
    public function all_post() {
        $arr = array();
        $key0 = "";   

        $limit = $offset= '';
        $page_count = $_POST['page_count'];

        if($_POST['start'] == 0){
             $offset  = ($_POST['start']+$page_count);
             $limit = 0;
        }

        if($_POST['start'] != 0){
             $limit  = $_POST['start'];
             $offset = ($_POST['start']+ $page_count);
        }


        $post_list = $this->common->post_list($_POST['user_id'],$limit,$offset,$page_count);
        if (!empty($post_list)) {
            foreach ($post_list as $key => $value) {
                $r = $this->common->getData('post_image', array('post_id' => $value['id']), array('', 'field' => 'image'));
                if (!empty($r)) {
                    $value['post_images'] = $r;
                } else {
                    $value['post_images'] = array();
                }
                $value['datetime'] =  $this->time_elapsed_string($value['created_at']);
                // if($this->common->getcomment($value['id'])){
                //      $post_list[$key]['post_comments'] = $this->common->getcomment($value['id']);
                // }else{
                //    $post_list[$key]['post_comments'] = array();
                // }
                if($_POST['user_id'] != $value['user_id']){
                     $report1 = user_members($value['user_id'],$_POST['user_id']);
                     if ($report1 == 0) {
                         unset($value);
                    }
                }
                
                if (!empty($value)) {
                    $arr[] = $value;
                }
            }
            
            $this->response(true, "Post List", array('all_feedpost' => $arr));
        } else {
            $this->response(true, "Post List.", array('all_feedpost' => array()));
        }
    }
/////////////////////delete_post//////////////////////
    public function delete_post() {
        $postdata = $this->common->getData('post_image', array('post_id' => $_POST['post_id']), array());

        $post = $this->common->deleteData('post', array('id' => $_POST['post_id']));

        $post_like = $this->common->deleteData('post_like', array('post_id' => $_POST['post_id']));

        $post_comment = $this->common->deleteData('post_comment', array('post_id' => $_POST['post_id']));

        $post_image = $this->common->deleteData('post_image', array('post_id' => $_POST['post_id']));

        if ($postdata) {
            foreach ($postdata as $key => $value) {
                $image_path = getcwd();
                unlink($image_path . "/assets/post/".$value['image']);
            }
            $this->response(true, "Post successfully deleted");
        } else {
            $this->response(false, "Post not deleted");
        }
    }
////////////////////post_like///////////
    public function post_like() {
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $_POST['created_date'] = date('Y-m-d');
        $_POST['created_time'] = date('H:i:s');
        $count = 0;
        $message = "";
        $likedata = $this->common->getData('post_like', array('user_id' => $user_id, 'post_id' => $post_id), array('single'));

        $postdata = $this->common->getData('post', array('id' => $post_id), array('single'));
        if (!empty($likedata)) {
            $result = $this->common->deleteData('post_like', array('like_id' => $likedata['like_id']));
            if ($postdata['total_like'] > 0) {
                $count = $postdata['total_like'] - 1;
            } else {
                $count = $postdata['total_like'];
            }
            $message = "Dislike Post";
            $test = 2;
        } else {
            $post = $this->common->getField('post_like', $_POST);
            $result = $this->common->insertData('post_like', $post);
            $count = $postdata['total_like'] + 1;
            $message = "Like Post";
            $test = 1;
           
        }
        $update = $this->common->updateData('post', array('total_like' => $count), array('id' => $post_id));

        if (!empty($update)) {

            if($test == 1){
                $user_detail = user_detail($_POST['user_id']);
             $sendnoti =  $this->common->send_all_notification($postdata['user_id'],ucwords($user_detail['full_name'])." liked your post","Like",'1',array('refer_id'=>$_POST['post_id']));
            }
            $this->response(true, $message, array('count' => $count));
        } else {
            $this->response(false, "There is a problem, please try again.", array('count' => $count));
        }
    }
    
///////////////post_comment//////////////
    public function add_post_comment() {
        $_POST['created_date'] = date('Y-m-d');
        $_POST['created_time'] = date('H:i:s');
        $post_list = $this->common->add_post_comment($_POST['post_id']);
        if ($_POST['comment_id']) {
            $_POST['refer_id'] = $_POST['comment_id'];
        } else {
            $_POST['refer_id'] = 0;
        }
        unset($_POST['comment_id']);
        $_POST['reciever_id'] = $post_list['user_id'];
        $post = $this->common->getField('post_comment', $_POST);
        $result = $this->common->insertData('post_comment', $post);
        if (!empty($result)) {
            $id = $this->db->insert_id();
            $comment = $this->common->getData('post_comment', array('post_id' => $_POST['post_id'], 'refer_id' => 0), array());
            $count = count($comment);
            $upcomment = $this->common->updateData('post', array('total_comment' => $count), array('id' => $_POST['post_id']));
            $this->response(true, "Success Comment Added", array('comment' => $comment, 'count' => $count));
        } else {
            $this->response(false, "No Comments Yet!");
        }
    }
 
    ///////edit_comment/////
    public function edit_comment() {
        $update = $this->common->updateData('post_comment', array('message' => $_POST['message']), array('comment_id' => $_POST['comment_id']));
        if ($update) {
            $this->response(true, "comment updated Successfully.");
        } else {
            $this->response(false, "There is a problem, please try again.");
        }
    }
    //////delete_comment/////
    public function delete_comment() {
        
        $results = $this->common->deleteData('post_comment_like', array('comment_id' => $_POST['comment_id'])); 

        $results = $this->common->deleteData('post_comment', array('comment_id' => $_POST['comment_id']));

        if ($this->db->affected_rows() > 0) {
            $post = $this->common->getData('post', array('id' => $_POST['post_id']), array('single'));
            $count = isset($post['total_comment']) > 0 ? ($post['total_comment'] - 1) : 0;
            $upcomment = $this->common->updateData('post', array('total_comment' => $count), array('id' => $_POST['post_id']));
            $this->response(true, "Comment Successfully Deleted");
        } else {
            $this->response(false, "comment does not exits");
        }
    }
  
    ////////////All comments////////
    public function post_all_comments() {
        $user_id = $_POST['user_id'];
        $post_comments = $this->common->getcomment($_POST['post_id'], $user_id);
        // echo $this->db->last_query();
        if ($post_comments) {
            $this->response(true, "Post List", array('post_comments' => $post_comments, 'total_comment' => count($post_comments)));
        } else {
            $this->response(false, "There is a problem, please try again.", array('post_comments' => array()));
        }
    }
    ////////post_comment_like/////////////////
    public function post_comment_like() {
        $user_id = $_POST['user_id'];
        $comment_id = $_POST['comment_id'];
        $_POST['created_date'] = date('Y-m-d');
        $_POST['created_time'] = date('H:i:s');
        $count = 0;
        $message = "";
        $likedata = $this->common->getData('post_comment_like', array('user_id' => $user_id, 'comment_id' => $comment_id), array('single'));
        $postdata = $this->common->getData('post_comment', array('comment_id' => $comment_id), array('single'));
        if (!empty($likedata)) {
            $result = $this->common->deleteData('post_comment_like', array('like_id' => $likedata['like_id']));
            if ($postdata['total_like'] > 0) {
                $count = $postdata['total_like'] - 1;
            } else {
                $count = $postdata['total_like'];
            }
            $message = "Dislike Comment";
        } else {
            $_POST['post_id'] = $postdata['post_id'];
            $post = $this->common->getField('post_comment_like', $_POST);
            $result = $this->common->insertData('post_comment_like', $post);
            $count = $postdata['total_like'] + 1;
            $message = "Like Comment";
        }
        $update = $this->common->updateData('post_comment', array('total_like' => $count), array('comment_id' => $comment_id));
        if (!empty($update)) {
            $postdata = $this->common->getData('post_comment', array('comment_id' => $comment_id), array('single'));
            $like = $this->common->getData('post_comment_like', array("comment_id" => $comment_id, "user_id" => $user_id), array('single'));
            if (!empty($like)) {
                $is_comment_like = 1;
            } else {
                $is_comment_like = 0;
            }
            $this->response(true, $message, array('count' => (string)$count, 'is_comment_like' => $is_comment_like));
        } else {
            $this->response(false, "There is a problem, please try again.", array('count' => $count));
        }
    }
  
////////////////////user_members ////////////////////////////
    public function add_members() {
        $user_id = $_POST['user_id'];
        $user_members = $this->common->getData('user_members', array('user_id' => $user_id, 'member_id' => $_POST['member_id']), array('single'));
        if (!empty($user_id)) {
            if (!empty($user_members)) {
                $this->response(false, "You have already Added this user");
                exit();
            } else {
                $insert = $this->common->insertData('user_members', $_POST);
                $insertid = $this->db->insert_id();
                if ($insertid > 0) {
                $this->response(true, "Memeber added successfully!");
                }
            }
        } else {
            $this->response(false, "user Id field is mandatory");
        }
    }
     public function user_member_by_id() {
          $user_id = $_POST['user_id'];
        if (!empty($user_id)) {
            $user_members = $this->common->getmemberprofile($user_id);
            $this->response(true, "Memebers Fetch successfully!",array('userinfo'=>$user_members));
        } else {
            $this->response(false, "user Id field is mandatory",array('userinfo'=>array()));
        }
    }
    public function all_users(){
        $user_members = $this->common->getData('user', array());
        if (!empty($user_members)) {
            $this->response(true, "Memebers Fetch successfully!",array('userinfo'=>$user_members));
            
        } else {
            $this->response(false, "user Id field is mandatory",array('userinfo'=>array()));
        }
    }
/////////////////////////////add_group_events/////////////////////
    public function add_group_events()
    {
       if (!empty($_POST['user_id'])) {
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $event = $this->common->getField('group_events', $_POST);
            $result = $this->common->insertData('group_events', $event);
            $eventid = $this->db->insert_id();

            if(!empty($_POST['members']) && $eventid > 0){
               $exp = explode(',', $_POST['members']);
               foreach ($exp as  $value) {
                    $result = $this->common->insertData('group_events_member', array('event_id'=>$eventid,'user_id'=>$value));
               }
            }
        } else {
            $this->response(false, "User Id field is mandatory");
            exit();
        }
        $this->response(true, "Event successfully added");
    }
/////////////////////////////get_events/////////////////////
    public function get_events()
    {
        $user_id =  $_POST['user_id'];
        $group_events = $this->common->get_events($user_id);
        if (!empty($group_events)) {
            foreach ($group_events as $key => $value) {
                $value['members'] = group_events_member($value['id']);
                $array[] = $value;
            }
            $this->response(true, "Event fetch Successfully", array('group_events' => $array));
        } else {
            $this->response(false, "No Events found",array('group_events' => array()));
        }
    }
///////////////////////////invoice/////////////////////////////
 public function create_invoice()
    {
       if (!empty($_POST['user_id'])) {
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $invoice = $this->common->getField('invoice', $_POST);
            $result = $this->common->insertData('invoice', $invoice);
            $invoiceid = $this->db->insert_id();

             if(!empty($_POST['members']) && $invoiceid > 0){
               $exp = explode(',', $_POST['members']);
               foreach ($exp as  $value) {
                    $result = $this->common->insertData('invoice_member', array('invoice_id'=>$invoiceid,'user_id'=>$value));
               }
            }
        } else {
            $this->response(false, "User Id field is mandatory");
            exit();
        }
        $this->response(true, "Invoice Created successfully added");
    }
  public function get_invoice_byid()
    {
       $user_id  = $_POST['user_id'];
       
        $result = $this->common->get_invoice($user_id);
        if (!empty($result)) {
            $this->response(true, "Invoice Fetch Successfully.", array("Invoicedata" => $result));
        } else {
            $this->response(false, "Invoice Not Found", array("Invoicedata" => array()));
        }
    }

/////////////////////////// legacy  //video/////////////////////////////
 public function  create_legacy()
    {
       if (!empty($_POST['user_id'])) {
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $_POST['video'] = '';
            $_POST['audio'] = '';
             if (isset($_FILES['file_upload'])) {
    
                $exp = explode('/',$_FILES['file_upload']['type']);

                $image = $this->common->do_upload('file_upload', './assets/legacy/');
                if (isset($image['upload_data'])) {
                    $_POST[$exp[0]] = $image['upload_data']['file_name'];
                }
            }


            $legacy = $this->common->getField('legacy', $_POST);
            $result = $this->common->insertData('legacy', $legacy);
            $legacy_id = $this->db->insert_id();

             if(!empty($_POST['questions'])){
               $exp = json_decode($_POST['questions'],true);
               foreach ($exp as  $value) {

                    $result = $this->common->insertData('legacy_questions', array('legacy_id'=>$legacy_id,'user_id'=>$_POST['user_id'],'question'=>$value['question'],'answer'=>$value['answer'],'date'=>date('Y-m-d'),'time'=>date('H:i:s')));
               }
            }
        } else {
            $this->response(false, "User Id field is mandatory");
            exit();
        }
        $this->response(true, "legacy Created successfully added");
    }
/////////////////////////// legacy_response /////////////////////////////
public function legacy_response()
    {
        $user_id  = $_POST['user_id'];
        if($_POST['type'] == '1'){
             $legacy_questions = $this->common->legacy_questions($user_id,1);
        }
        if($_POST['type'] == '2'){
             $legacy_questions = $this->common->legacy_questions($user_id,2);
        }
        if(!empty($legacy_questions)){
             $this->response(true, "legacy Fetch Successfully.", array("legacy_response"=>$legacy_questions));
         }else{
             $this->response(true, "legacy Fetch Successfully.", array("legacy_response"=>array()));
         }    
       
    }

/////////////////////////// legacy by category/////////////////////////////
 public function legacy_by_category()
    {
        $user_id  = $_POST['user_id'];
        $cat_id  = $_POST['cat_id'];
        if(!empty($cat_id)){
            $cat_name = category_name($cat_id);
        }else{
             $cat_name = "";
        }
        $result = $this->common->legacy_by_category($cat_id,$user_id);
        if (!empty($result)) {
            $this->response(true, "legacy Fetch Successfully.", array('cat_name'=>$cat_name,"legacy" => $result));
        } else {
            $this->response(false, "legacy Not Found", array('cat_name'=>$cat_name,"legacy" => array()));
        }
    }

/////////////////////////// legacy by category/////////////////////////////
 public function legacy_detail()
    {
        $user_id  = $_POST['user_id'];
        $cat_id  = $_POST['cat_id'];
        $legacy_id  = $_POST['legacy_id'];
        $legacy_type  = $_POST['legacy_type'];

        if(!empty($cat_id)){
            $cat_name = category_name($cat_id);
        }else{
             $cat_name = "";
        }

        if(!empty($legacy_type) && $legacy_type == "2" ){
            $legacy_by_category = $this->common->legacy_by_category($cat_id,$user_id,$legacy_id);
            $legacy_questions = array();
        }else if(!empty($legacy_type) && $legacy_type == "1" ){
            $legacy_by_category = $this->common->legacy_by_category($cat_id,$user_id,$legacy_id);
            $legacy_questions = $this->common->legacy_questions($user_id,1,$cat_id,$legacy_id);
             
         }
          $this->response(true, "legacy Fetch Successfully.", array('cat_name'=>$cat_name,"legacy_detail" => $legacy_by_category,"legacy_questions"=>$legacy_questions));
    }

/////////////// add legacy response//////////////
    public function add_legacy_response() {
        $_POST['date'] = date('Y-m-d');
        $_POST['time'] = date('Y-m-d H:i:s');
        $_POST['created_at'] = date('Y-m-d H:i:s');
        if ($_POST['response_id']) {
            $_POST['refer_id'] = $_POST['response_id'];
        } else {
            $_POST['refer_id'] = 0;
        }
        unset($_POST['response_id']);
        $_POST['response_video'] = "";
          $_POST['response_audio'] = "";

        if($_POST['response_type'] == '1'){
            if (isset($_FILES['file_upload'])) {
                $exp = explode('/',$_FILES['file_upload']['type']);
                $image = $this->common->do_upload('file_upload', './assets/legacy/');
                if (isset($image['upload_data'])) {
                    $_POST['response_'.$exp[0]] = $image['upload_data']['file_name'];
                }
            }
            //   if (isset($_FILES['response_video'])) {
            //     $image = $this->common->do_upload('response_video', './assets/legacy/');
            //     if (isset($image['upload_data'])) {
            //         $_POST['response_video'] = $image['upload_data']['file_name'];
            //     }
            // }
        }

        $post = $this->common->getField('legacy_questions_response', $_POST);
        $result = $this->common->insertData('legacy_questions_response', $post);
        $id = $this->db->insert_id();

        if (!empty($id)) {

            $legacy_response = $this->common->getData('legacy_questions_response', array('question_id' => $_POST['question_id'],'legacy_id'=>$_POST['legacy_id'], 'refer_id' => $_POST['refer_id']), array());
            $count = count($legacy_response);
        
            $this->response(true, "response Added successfully", array('legacy_response' => $legacy_response, 'count' => $count));        
        } else {
            $this->response(false, "No response Yet!");
        }
    }
 /////////////////////////////////////////////
    public function create_group() {
            $_POST['created_at'] = date('d-m-Y H:i:s');
            $post = $this->common->getField('create_group', $_POST);
            $result = $this->common->insertData('create_group', $post);
            $group_id = $this->db->insert_id();

            if(!empty($_POST['members']) && $group_id > 0){
               $exp = explode(',', $_POST['members']);
               foreach ($exp as  $value) {
                    $result = $this->common->insertData('create_group_member', array('group_id'=>$group_id,'user_id'=>$_POST['user_id'],'member_id'=>$value));
               }
                $this->response(true, "Group created successfully");
            }else{
                 $this->response(false, "There is a problem, please try again.");
            }
    }
///////////////////////////////////////////////////////////////
    public function  delete_group() {
        $group = $this->common->deleteData('create_group', array('id' => $_POST['group_id']));
        $groupdata = $this->common->deleteData('create_group_member', array('group_id' => $_POST['group_id']));
        if($group){
            $this->response(true, "Successfully Deleted");
        
        } else {
            $this->response(false, "There is a problem, please try again.");
        }
    }
///////////////////////////////////////////////////////////////
public function update_group() {
            
        $user_id = $_POST['user_id'];
        $group_id = $_POST['group_id'];
        $create_group = $this->common->getData('create_group', array('id' => $group_id,'user_id'=>$user_id), array('single'));

        if (!empty($create_group)) {

            $update = $this->common->updateData('create_group',array('group_name'=>$_POST['group_name'],'user_id'=>$user_id), array('id' => $group_id));

             if(!empty($_POST['members'])){
                $groupdata = $this->common->deleteData('create_group_member', array('group_id' => $_POST['group_id']));

               $exp = explode(',', $_POST['members']);
               foreach ($exp as  $value) {
                    $result = $this->common->insertData('create_group_member', array('group_id'=>$group_id,'user_id'=>$_POST['user_id'],'member_id'=>$value));
               }
                $this->response(true, "Group Updated successfully");
            }else{
                 $this->response(false, "There is a problem, please try again.");
            }
        }
    }
//////////////////////////////////group list bu id///////////////////
    public function all_groups() {

        $arr = array();    
        $limit = $offset= '';
        $page_count = $_POST['page_count'];

        if($_POST['start'] == 0){
             $offset  = ($_POST['start']+$page_count);
             $limit = 0;
        }

        if($_POST['start'] != 0){
             $limit  = $_POST['start'];
             $offset = ($_POST['start']+ $page_count);
        }

        $user_id = $_POST['user_id'];

        if(!empty($user_id)){
            $groupdata = $this->common->all_groups_by_member($user_id);
            foreach ($groupdata as  $value) {

                    $value['members'] = create_group_member($value['id']);
                   
                    $value['total_members'] = count($value['members']);
                    $arr[] = $value;
               }
                $this->response(true, "Group Fetch successfully",array('groups'=>$arr));
            }else{
                 $this->response(false, "There is a problem, please try again.");
            }
     
    }


































     ///////////// get_library /////////////////////////////////////////////
    public function get_library() {
        $type = $_POST['type'];
        $audio =array("mp3", "ogg", "flac","wav","mpeg");
        $video = array("wmv","flv","wma","avi", "3gp","webm", "mkv","avchd","mp4","mov");
        $library = array();
        $get_library = $this->common->get_library($_POST['user_id']);
        if (!empty($get_library)) {
            foreach ($get_library as $key => $value) {
                $exp = explode('.', $value['video']);
                if($type == "1"){
                    if(in_array($exp['1'],$audio) && !empty($value)){
                            $library[] = $value;
                        }
                    }
                else if($type == "2"){
                 if(in_array($exp['1'],$video) && !empty($value)){
                        $library[] = $value;
                    }
                }
            }
            $this->response(true, "library", array('library' => $library));
        } else {
            $this->response(false, "There is a problem, please try again.", array('library' => array()));
        }
    }

    ///////////// delete_library /////////////////////////////////////////////
    public function  delete_library() {
     
        $library = $this->common->deleteData('library', array('id' => $_POST['id']));
        if($library){
            $this->response(true, "Successfully Deleted");
        
        } else {
            $this->response(false, "There is a problem, please try again.");
        }
    }
    /////////////Save to library/////////////////////////////////////////
    public function save_to_library() {

        if (!empty($_POST['user_id'])) {
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $savedata = $this->common->getData('library', array('user_id' => $_POST['user_id'], 'legacy_id' => $_POST['legacy_id']), array('single'));
            if (!empty($savedata)) {
                $result = $this->common->deleteData('library', array('id' => $savedata['id']));
                $message = "Unsaved to library";
            } else {
                $pin = $this->common->getField('library', $_POST);
                $result = $this->common->insertData('library', $pin);
                $pinid = $this->db->insert_id();
                $message = "save to library";
            }
            $this->response(true, $message);
        } else {
            $this->response(false, "There is a problem, please try again.");
            exit();
        }
    }
    
    ///////////Media////////////////////////
    public function get_media() {
        $new_array = array();
        $base_url = base_url('assets/userfile/profile/');
        $base_url2 = base_url('assets/post/');
        $base_url3 = base_url('assets/concerts/');
        if (!empty($_POST['user_id'])) {
            $tables = array("user", "post_image", "concerts");
            foreach ($tables as $table) {
                $col = "user_id";
                if ($table == "user") {
                    $col = "id";
                    $user_id = $_POST['user_id'];
                }
                // $sql = "select * from $table where $col = '" . $user_id . "'";
                // $query = $this->db->query($sql);
                // $imagedata = $query->result_array();

                $imagedata =  $this->common->getData($table, array($col=>$user_id),array(''));

                foreach ($imagedata as $key => $value) {
                    if (!empty($value)) {
                        $new = array();
                        if (!empty($value['profile_image'])) {
                            $new['image'] = $base_url . $value['profile_image'];
                        }
                        if (!empty($value['cover_image'])) {
                            $new['image'] = $base_url . $value['cover_image'];
                        }
                        if (!empty($value['image'])) {
                            $new['image'] = $base_url2 . $value['image'];
                        }
                        if (!empty($value['concert_image'])) {
                            $new['image'] = $base_url3 . $value['concert_image'];
                        }
                        if (!empty($new)) {
                            $new_array[] = $new;
                        }
                    }
                }
            }
            $this->response(true, "All images", array('media' => $new_array));
        } else {
            $this->response(false, "There is a problem, please try again.", array('media' => array()));
        }
    }


       ////////////////////////get_bitpak //////////////
    public function get_bitpack() {
        $user_id  = $_POST['user_id'];
        if (!empty($user_id)) {
            $subquery = "(exists (select 1
                from bitpack_users Bu
                where Bu.bit_pack_id = B.id and Bu.user_id = '".$user_id."')) as is_selected ,";
        } else {
            $subquery = "";
        }

        $result = $this->common->getData('bit_pack as B', array(), array('field'=>$subquery.' B.id,B.bit_name,B.type,B.bit_amount_d,B.amount_in_bit,B.euro_amount_d,B.amount_in_euro,B.image,B.created_at'));
        if (!empty($result)) {
            $this->response(true, "Bit Packs Fetch Successfully.", array("BitPackList" => $result));
        } else {
            $this->response(false, "Bit Packs  Not Found", array("BitPackList" => array()));
        }
    }

    ///////////////////////////////////notification/////////////////////////
    public function get_notification() {
        $sender_id = $_POST['sender_id'];
        $data = $this->common->getData('chat_messages', array('sender_id' => $sender_id), array('sort_by' => 'date', 'sort_direction' => 'desc'));
        if (!empty($data)) {
            $this->response(true, "Notification fetch Successfully", array('notification' => $data));
        } else {
            $this->response(false, "No notification found");
        }
    }
     public function get_notification_by_date() {
        $user_id = $_POST['user_id'];
        $array = array();

         $limit = $offset= '';
         $page_count = $_POST['page_count'];

        if($_POST['start'] == 0){
             $offset  = ($_POST['start']+$page_count);
             $limit = 0;
        }
        if($_POST['start'] != 0){
             $limit  = $_POST['start'];
             $offset = ($_POST['start']+ $page_count);
        }


   
        //$notification = $this->common->getData('notification', array('user_id' => $user_id),array('group_by'=>'created_at','sort_by'=>'created_at','sort_direction'=>'desc','limit'=>$offset,'offset'=>$limit));

         $notification = $this->common->get_notification_by_date($user_id,$limit,$offset,$page_count);

        if (!empty($notification)) {
            foreach ($notification as $key => $value){ 
                 $data1 = $this->common->getData('notification', array('user_id' => $user_id,'created_at'=>$value['created_at']), array('sort_by' => 'notification_id','sort_direction' => 'desc'));
                    
                       if(!empty($data1)){
                            $arr['date'] = $value['created_at'];
                            $arr['data'] = $data1;
                            $array[] = $arr;
                       }
            }
         
            $this->response(true, "Notification fetch Successfully", array('notification' => $array));
        } else {
            $this->response(true,"Notification fetch Successfully", array('notification' => array()));
        }
    }
    ///////////////////////////Leave Group ////////////////////////
    public function leave_group() {
        $user_id = $_POST['user_id'];
        $group_id = $_POST['group_id'];
        $create_group = $this->common->getData('create_group', array('id' => $group_id, 'find_in_set("' . $user_id . '",members) <>' => 0), array('single'));
        if (!empty($create_group)) {
            $members = explode(',', $create_group['members']);
            $index = array_search($user_id, $members);
            if ($index !== false) {
                unset($members[$index]);
            }
            $members = implode(',', $members);
            $update = $this->common->updateData('create_group', array('members' => $members), array('id' => $group_id));
        }
        if (!empty($update)) {
            $this->response(true, "Successfully Leave group", array());
        } else {
            $this->response(false, "There is a problem, please try again.", array());
        }
    }
    
    ///////////////////////////artist_page_share /////////
    public function artist_page_share() {

          $share_link = 'deeplinking/artist_army.php?artist_id='.$_POST['artist_id'].'&user_id='.$_POST['user_id'].'&type=3';
          $_POST['share_link'] =  $share_link;

          $artist_detail = user_detail($_POST['artist_id']);
        if (!empty($_POST['user_id'])) {
            if(!empty($_POST['share_to'])){
                $export = explode(',', $_POST['share_to']);
                foreach ($export as $key => $value) {
                     $_POST['share_to'] = $value;
                     $share = $this->common->getField('artist_page_share', $_POST);
                     $result = $this->common->insertData('artist_page_share', $share);
                     $shareid = $this->db->insert_id();

                     if (!empty($shareid)) {
                            $reciver = user_detail($value);
                            $sender = user_detail($_POST['user_id']);
                            $result = $this->common->insertData('chat_messages', array('sender_id' => $_POST['user_id'], 'sender_name' => isset($sender['full_name']) ? $sender['full_name'] : "", 'sender_image' => $sender['profile_image'], 'receiver_id' => $_POST['share_to'], 'receiver_name' => $reciver['full_name'], 'receiver_image' => $reciver['profile_image'], 'messages' => $share_link, 'image' => "", "type" => "3", 'refer_id'=>$_POST['artist_id'],'datetime' => strtotime(date('Y-m-d'))));

                            $notifi = $this->common->send_all_notification($value,$sender['full_name'].' sent you a artist profile:'.$artist_detail['artist_name'],"Profile",'3',array('refer_id'=>$_POST['user_id'],'ref_artist_id'=>$_POST['artist_id']));

                        }
                }
            }
          
            $this->response(true, "Successfully Shared!", array('not'=>$notifi));
        } else {
            $this->response(false, "Not Shared!");
            exit();
        }
    }
     ///////////////////////////post_share /////////
    public function post_share() {

          $share_link = '?post_id='.$_POST['post_id'];

           $postdata = $this->common->getData('post', array('id' => $_POST['post_id']), array('single'));
           $post_user_fullname = user_detail($postdata['user_id']);

          $_POST['share_link'] =  $share_link;
            $noti = "";
        if (!empty($_POST['user_id'])) {
            if(!empty($_POST['share_to'])){
                $export = explode(',', $_POST['share_to']);
                foreach ($export as $key => $value) {
                     $_POST['share_to'] = $value;
                     $share = $this->common->getField('post_share', $_POST);
                     $result = $this->common->insertData('post_share', $share);
                     $shareid = $this->db->insert_id();

                     if (!empty($shareid)) {
                            $reciver = user_detail($value);
                            $sender = user_detail($_POST['user_id']);
                            $result = $this->common->insertData('chat_messages', array('sender_id' => $_POST['user_id'], 'sender_name' => isset($sender['full_name']) ? $sender['full_name'] : "", 'sender_image' => $sender['profile_image'], 'receiver_id' => $_POST['share_to'], 'receiver_name' => $reciver['full_name'], 'receiver_image' => $reciver['profile_image'], 'messages' => $share_link, 'image' => "", "type" => "1",'refer_id'=>$_POST['post_id'], 'datetime' => strtotime(date('Y-m-d'))));

                            $noti  = $this->common->send_all_notification($value,ucwords($sender['full_name']).' shared you a post by '.ucwords($post_user_fullname['full_name']),"Share",'1',array('refer_id'=>$_POST['post_id']));
                        }

                }
            }
            $this->response(true, "Successfully Shared!", array('sendnoti'=>$noti));
        } else {
            $this->response(false, "Not Shared!");
            exit();
        }
    }
    
    /////////////////////////////////Chat messages///////////////////////////
    public function send_message() {

        $result = $this->common->insertData('chat_messages', array('sender_id' => $_GET['sender_id'], 'sender_name' => $_GET['sender_name'], 'sender_image' => $_GET['sender_image'], 'receiver_id' => $_GET['receiver_id'], 'receiver_name' => $_GET['receiver_name'], 'receiver_image' => $_GET['receiver_image'], 'messages' => $_GET['messages'], 'image' => $_GET['image'], 'datetime' => $_GET['datetime']));

        $insert_id = $this->db->insert_id();
        if ($insert_id > 0) {
            $this->response(true, "Successfully Sent!", array());
        } else {
            $this->response(false, "Not Sent!");
        }
    }
    public function chat_image() {
        $image_name = "";
        if (isset($_FILES['image'])) {
            $image = $this->common->do_upload('image', './assets/chat/');
            if (isset($image['upload_data'])) {
                $image_name = $image['upload_data']['file_name'];
                $url = base_url('assets/chat/') . $image_name;
            }
        }
        if ($image_name != "") {
            $this->response(true, "file Upload Successfully!", array('image_url' => $url));
        } else {
            $this->response(false, "Not Sent!");
        }
    }
    public function user_chat_messages() {
        $user_id = $_POST['sender_id'];
        $friend_id = $_POST['receiver_id'];
        $arr = array();
        if (!empty($user_id)) {
            $where = " (sender_id = '" . $user_id . "' and receiver_id = '" . $friend_id . "') OR  (receiver_id = '" . $user_id . "' and sender_id = '" . $friend_id . "') ";
        
             $chat_messages = $this->common->getData('chat_messages', $where, array('sort_by'=>"messages_id",'sort_direction'=>"ASC"));

             if(!empty($chat_messages)){
                foreach ($chat_messages as $key => $value) {
                      $arr[] = $value;
                }
             }
            $this->response(true, "Fetch messages Successfully!", array("chat_messages" => $arr));
        } else {
            $this->response(false, "No message found", array("chat_messages" => array()));
        }
    }
    public function chat_users() {

        $arr = array();
        $key0 = "";   

        $limit = $offset= '';
        $page_count = $_POST['page_count'];

        if($_POST['start'] == 0){
             $offset  = ($_POST['start']+$page_count);
             $limit = 0;
        }

        if($_POST['start'] != 0){
             $limit  = $_POST['start'];
             $offset = ($_POST['start']+ $page_count);
        }

        $user_id = $_POST['user_id'];
        $chats = array();
        $cmessages = array();
        if (!empty($user_id)) {
            $cwhere = " receiver_id = '" . $user_id . "'";
            $cmessages = $this->common->getData('chat_messages', $cwhere, array('field'=>'sender_id as id, sender_name as full_name, sender_image as profile_image','group_by'=>'id','sort_by'=>'messages_id','sort_direction'=>'desc'));

            $user = $this->common->getData('user', array('id' => $user_id), array('single'));

            $chat_users =  $this->common->chat_users($user_id,$user['user_type']);

            if (!empty($chat_users)) {
                $array1 = array_merge($cmessages, $chat_users);
                $ids = array_column($array1, 'id');
                $ids = array_unique($ids);
                $array = array_filter($array1, function ($key, $value) use ($ids) {
                    return in_array($value, array_keys($ids));
                }, ARRAY_FILTER_USE_BOTH);

            if(!empty($page_count)){
                 $array =  array_splice($array,$limit,$offset);
            
              }
                foreach ($array as $key => $value) {

                     $lwhere = "(sender_id = '" . $value['id'] . "') OR  (receiver_id = '" . $value['id'] . "')";  

                     $chat_messages = $this->common->getData('chat_messages',$lwhere, array('sort_by'=>'messages_id','sort_direction'=>'desc','limit'=>1,'single'));

                    if (!empty($chat_messages['messages'])) {

                        if($chat_messages['type'] > 0){

                              $value['messages'] = base_url().$chat_messages['messages'];
                        }else{
                              $value['messages'] = $chat_messages['messages'];
                        }
                      
                    } else if (!empty($chat_messages['image'])) {
                        $value['messages'] = "Photo";
                    } else {
                        $value['messages'] = "";
                    }
                    $value['datetime'] = $value['datetime'] = !empty($chat_messages['datetime']) ? $chat_messages['datetime'] : "";
                    $value['count'] = 0;
                    $chats[] = $value;
                }
                $this->response(true, "Fetch messages Successfully!", array("chat_users" => $chats));
            } else {
                $this->response(false, "No chat user found", array("chat_users" => array()));
            }
        } else {
            $this->response(false, "user Id field is mandatory", array("chat_users" => array()));
        }
    }
    public function read_chat() {
        $user_id = $_POST['user_id'];
        $friend_id = $_POST['friend_id'];
        if (!empty($user_id)) {

            $where = "(sender_id = '" . $user_id . "' and receiver_id = '" . $friend_id . "') OR  (receiver_id = '" . $user_id . "' and sender_id = '" . $friend_id . "') and status = '1' ";  
            $chat_messages = $this->common->getData('chat_messages',$where, array('sort_by'=>'messages_id','sort_direction'=>'desc'));
            if ($chat_messages) {
                foreach ($chat_messages as $key => $value) {
                    $update = $this->common->updateData('chat_messages', array('status' => "0"), array('messages_id' => $value['messages_id']));
                }
                $this->response(true, "read messages Successfully!");
            } else {
                $this->response(false, "No message found");
            }
        } else {
            $this->response(false, "Sender Id field is mandatory");
        }
    }
    public function delete_chat() {
        $sender_id = $_POST['user_id'];
        $receiver_id = $_POST['friend_id'];
        if (!empty($sender_id)) {
            $where = "(sender_id = '" . $sender_id . "' and receiver_id  = '" . $receiver_id . "' ) OR  (sender_id = '" . $receiver_id . "' and receiver_id = '" . $sender_id . "')";  
            $chat_messages = $this->common->getData('chat_messages',$where, array('sort_by'=>'messages_id','sort_direction'=>'desc'));
            if ($chat_messages) {
                foreach ($chat_messages as $key => $value) {
                    $result = $this->common->deleteData('chat_messages', array('messages_id' => $value['messages_id']));
                }
                $this->response(true, "message deleted!");
            } else {
                $this->response(false, "No message found");
            }
        } else {
            $this->response(false, "User Id field is mandatory");
        }
    }
 ////////////////////home_search///////////////////////////////////////////   
    public function home_search() {
        $user_id = $_POST['user_id'];
        $user = $this->common->getData('user', array('id' => $user_id), array('single'));
          if (!empty($user)) {
            $search = $_POST['search'];
            $userinfo = $this->common->home_search($user_id,$search);
            if (!empty($userinfo)) {
                $this->response(true, "Fetch user Successfully!", array("userinfo" => $userinfo));
            } else {
                $this->response(false, "No user found", array("userinfo" => array()));
            }
        } else {
            $this->response(false, "user Id field is mandatory", array("userinfo" => array()));
        }
    }

     public function report_artist() {
        $user_id = $_POST['user_id'];
        $report_artist = $this->common->getData('report_artist', array('block_id' => $_POST['block_id']), array('single'));
        if (!empty($user_id)) {
            $count = count($report_artist);
            if (!empty($report_artist) && $count == 5) {

                if(report_user($user_id,$_POST['block_id'],'1') != 1){

                     $report_user = $this->common->insertData('report_user', array('user_id' => $user_id, 'block_id' => $_POST['block_id'],'type'=>'1'));
                        $this->response(false, "You have Blocked this artist");
                }else{
                       $this->response(false, "You have already Blocked this artist");
                }
             
                exit();
            } else {
                $insert = $this->common->insertData('report_artist', $_POST);
                $insertid = $this->db->insert_id();
                if ($insertid > 0) {
                    $this->response(true, "Artist Reported successfully!");
                }
            }
        } else {
            $this->response(false, "Artist Id field is mandatory");
        }
    }

    public function select_bitpack()
    {
        $user_id = $_POST['user_id'];
        $bit_pack_id = $_POST['bit_pack_id'];
        $total_donate_bit = $_POST['bit_amount'];
        $total_donatebit ="";
            if(!empty($user_id) && !empty($bit_pack_id)){

                  $select =   $this->common->getData('bitpack_users', array('user_id' => $_POST['user_id']), array('sort_by'=>'id','sort_direction'=>'desc','single'));

                  if(!empty($select)){
                    $total_donatebit = ($select['total_donate_bit']+$_POST['bit_amount']);
                  }else{
                    $total_donatebit = $_POST['bit_amount'];
                  }
                  
                $array = array("bit_pack_id"=>$bit_pack_id, "bit_pack_amount"=>$total_donate_bit, "total_donate_bit"=>$total_donatebit, "user_id"=> $user_id);

                $insert = $this->common->insertData('bitpack_users', $array);
                $insertid = $this->db->insert_id();
            

            if ($insertid >0) {

             
               $this->response(true, "Bitpack Added on your account Successfully.",array('url'=>base_url('api/paypal/').$user_id.'/'.$bit_pack_id));
            }
        }else{
            $this->response(false, "Please select your bit pack");
            exit();
        }
    }
 ///////////////post image //////////////////////
public function get_post_image()
    {
         $post_image = $this->common->getData('post_image', array('post_id' => $_POST['post_id']), array('field' => 'image'));

         if (!empty($post_image)) {
                $this->response(true, "Post image", array('post_images' => $post_image));
        } else {
            $this->response(false, "There is a problem, please try again.", array('post_images' => array()));
        }
    }

public function paypal($user_id,$bit_pack_id){

      $bit_pack = $this->common->getData('bit_pack', array('id'=>$bit_pack_id),array('single'));

      if($bit_pack){
         // Set variables for paypal form
        $returnURL = base_url().'home/success';
        $cancelURL = base_url().'home/cancel';
        $notifyURL = 'https://www.creativethoughtsinfo.com/CTCC/artist_army/home/ipn'; 
        // Add fields to paypal form
        $this->paypal_lib->add_field('return',$returnURL);
        //print_r($this->paypal_lib);
        $this->paypal_lib->add_field('cancel_return',$cancelURL);
        $this->paypal_lib->add_field('notify_url',$notifyURL);
        $this->paypal_lib->add_field('custom',$user_id);
        $this->paypal_lib->add_field('bit_pack_id',$bit_pack_id);
        $this->paypal_lib->add_field('item_number',$bit_pack_id);
        $this->paypal_lib->add_field('amount',$bit_pack['amount_in_bit']);
        $this->paypal_lib->paypal_auto_form();

      }
       
    }
     public function send_all_notification($user_id='',$message='',$type='',$data=array()){
        $user_id= $_POST['user_id'];
        $userdetail = user_detail($user_id);

        $title =   $type;


        $body = $message;

        $msg_notification = array("body" =>$body,"title"=>$title,'type'=>$type); 

        if($userdetail['device_type'] == "1" && !empty($userdetail['android_token']))
        {
          $res = $this->common->sendNotification_android(array($userdetail['android_token']), $msg_notification, $msg_notification);
        
        }   
        /*if($userdetail['device_type'] == "2" && !empty($userdetail['ios_token']))
        {
            $messages_push =  array("notification" => $msg_notification);
            $res = $this->common->sendNotification_ios($userdetail['ios_token'],$body,$title,$type,$messages_push); 
              
        }*/

        if($res){
            $json_array=json_decode($res ,true);
            $created_at = date('Y-m-d H:i:s');
            if($json_array["success"]>0){

          /*    
                $noti  =  array('message'=>$body,'user_id'=>$user_id,'created_at'=>$created_at,
                  'type_name'=>$type,'type'=>$ref_type,'refer_id'=>$data['refer_id'],'ref_artist_id'=>$ref_artist_id,'user_type'=>$user_type);
                $result = $this->common->insertData('notification', $noti);*/
            }
        }

     return $res;
    }    
    public function testfile(){
         $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt = $ipnCheck;
        fwrite($myfile,$_REQUEST);
        fclose($myfile);
    }
}