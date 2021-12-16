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
    public function create_category()
    {
    	  if (!empty($_POST['user_id'])) {
            if (isset($_FILES['cat_image'])) {
                $cat_image = $this->common->do_upload('cat_image', './assets/category');
                if (isset($cat_image['upload_data'])) {
                    $_POST['cat_image'] = $cat_image['upload_data']['file_name'];
                      $image_name = $_POST['cat_image'];

                }
            }
            $cat = $this->common->getField('category', $_POST);
            $result = $this->common->insertData('category', $cat);
            $catid = $this->db->insert_id();
        } else {
            $this->response(false, "Id field is mandatory");
            exit();
        }
        $this->response(true, "Category added");
    } 


    public function delete_category()
    {  
    	
       $catdel = $this->common->deleteData('category', array('id' => $_POST['id']));
        if ($catdel) {
            $this->response(true, "Category successfully deleted");
        } else {
            $this->response(false, "Category not deleted");
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
            // if (isset($_REQUEST['android_token'])) {
            //     $old_device = $this->common->getData('user', array('android_token' => $_REQUEST['android_token']), array('single', 'field' => 'id'));
            // }
            // if (isset($_REQUEST['ios_token'])) {
            //     $old_device = $this->common->getData('user', array('ios_token' => $_REQUEST['ios_token']), array('single', 'field' => 'id'));
            // }
            // if ($old_device) {
            //     $this->common->updateData('user', array('android_token' => "", "ios_token" => ""), array('id' => $old_device['id']));
            // }
            
            $this->common->updateData('user', array('fcm_token' => $_REQUEST['fcm_token']),array('id' => $result['id']));

            $result['fcm_token'] = $_REQUEST['fcm_token'];

            $this->response(true, 'Successfully Login', array("userinfo" => $result));
        } else {
            $message = "Invalid Email or password";

           /* $this->response(false, $message, array("userinfo" => (object) array($message)));*/
            $this->response(false, " Invalid Email or Password.");
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
//////////Update Accept and reject member 
    public function request_status() {
        $result = $this->common->getData('user_members', array('user_id' => $_POST['user_id'],'member_id'=>$_POST['member_id']), array('single'));
        if ($result) {
            $post = $this->common->getField('user_members', $_POST);
            $info = $this->common->updateData('user_members', $post, array('id' => $result['id']));

             $info1 = $this->common->updateData('notification', array('type'=>"4"), array('notification_id' => $_POST['notification_id']));

        if (!empty($info)) {
           $message = "";
            if($_POST['request_status'] == "0"){
                $user_detail = user_detail($_POST['user_id']);
                $message = ucwords($user_detail['full_name'])." has accepted your request";
            }

            if($_POST['request_status'] == "2"){
               $user_detail = user_detail($_POST['user_id']);
               $message = ucwords($user_detail['full_name'])." has rejected your request";
            }

             $sendnoti =  $this->common->send_all_notification($_POST['member_id'],$message,"request",'4',array('refer_id'=>$result['id']));

            $this->response(true, "Your Request Is Updated Sucessfully.");
        }
}
         else {
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
         $video = array("wmv","flv","wma","avi", "3gp","webm", "mkv","avchd","mp4","mov");

        $post_list = $this->common->post_list($_POST['user_id'],$limit,$offset,$page_count);
        if (!empty($post_list)) {
            foreach ($post_list as $key => $value) {
                $r = $this->common->getData('post_image', array('post_id' => $value['id']), array('', 'field' => 'image'));
                if (!empty($r)) {

                $exp = explode('.', $r[0]['image']);
                    if(in_array($exp['1'],$video)){
                             $value['media'] = "video";
                        }else{
                              $value['media'] = "image";
                        }
                    $value['post_images'] = $r;
                } else {
                    $value['post_images'] = array();
                }


                if(!empty($value['refer_user_id'])){
                    $user_detail = user_detail($value['refer_user_id']);
                    $value['refer_full_name'] = $user_detail['full_name'];
                    $value['refer_profile_image'] = $user_detail['profile_image'];
                    $value['refer_created_at'] = $this->time_elapsed_string($value['created_at']);
                    $value['datetime'] =  $this->time_elapsed_string($value['refer_created_at']);
                }else{
                    $value['refer_full_name'] = "";
                    $value['refer_profile_image'] = "";
                    $value['refer_created_at'] ="";
                    $value['datetime'] =  $this->time_elapsed_string($value['created_at']);
                }

               

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
            $post_comment_like = $this->common->deleteData('post_comment_like', array('post_id' => $_POST['post_id']));

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
    //////Send Notification Question APi ////
    
   /*  $sendnoti =  $this->common->send_all_notification($postdata['user_id'],ucwords($user_detail['full_name'])." liked your post","Like",'1',array('refer_id'=>$_POST['post_id']));*/
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

                $user_detail = user_detail($_POST['user_id']);
                $message = ucwords($user_detail['full_name'])."  wants to add you as a member";

                 $sendnoti =  $this->common->send_all_notification($_POST['member_id'],$message,"request",'3',array('refer_id'=>$user_id));

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
        if(!empty($_POST['user_id'])){
            $user_id = $_POST['user_id'];
        }
        $user_members = $this->common->getData('user', array('id!='=>$user_id));
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

                    $result = $this->common->insertData('legacy_questions', array('legacy_id'=>$legacy_id,'user_id'=>$_POST['user_id'],'cat_id'=>$_POST['cat_id'],'question'=>$value['question'],'answer'=>$value['answer'],'date'=>date('Y-m-d'),'time'=>date('H:i:s')));
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
        $array = array();
        $user_id  = $_POST['user_id'];
        $cat_id  = $_POST['cat_id'];
      
      
        $legacy_questions = $this->common->legacy_questions($user_id,1,$cat_id);
       
        if(!empty($legacy_questions)){
             foreach ($legacy_questions as $key => $value) {

                if(!empty($value['response'])){
                    foreach ($value['response'] as $val) {
                        if($_POST['type'] == '2' && $val['user_id'] ==  $user_id ){
                          //  $legacy_questions = $this->common->legacy_questions($user_id,1);
                            $val['title'] = legacy_title_name($val['legacy_id']);
                            $array[] = $val;
                           
                        }
                        if($_POST['type'] == '1' && $val['user_id'] !=  $user_id){
                            $val['title'] = legacy_title_name($val['legacy_id']);
                            $array[] = $val;
                        }
                      
                    }
                   
                }
                
             }



             $this->response(true, "legacy Fetch Successfully.", array("legacy_response"=>$array));
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
            foreach ($get_library as $key => $value) 
            {

           	$sql = $this->common->getData('legacy',array('id'=>$value['legacy_id']), array('single'));

               $value['cat_id'] = $sql['cat_id'] ;
               $value['legacy_id'] = $sql['id'];
               $value['legacy_type'] = $sql['legacy_type'];
              

                if($type == "1"){
                      $exp = explode('.', $value['audio']);
                    if(in_array($exp['1'],$audio) && !empty($value)){
                            $library[] = $value;
                        }
                    }
                else if($type == "2"){
                     $exp = explode('.', $value['video']);
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
    

    ///////////////////////////////////notification/////////////////////////
    public function get_notification() {
        $user_id = $_POST['user_id'];
        $array = array();
        $data = $this->common->getData('notification', array('user_id' => $user_id), array('sort_by' => 'created_at', 'sort_direction' => 'desc'));
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $user_detail = user_detail($value['user_id']);
                $value['profile_image'] = $user_detail['profile_image'];
                $value['email'] = $user_detail['email'];
                $array[] = $value;
            }
            $this->response(true, "Notification fetch Successfully", array('notification' => $array));
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
   
     ///////////////////////////post_share /////////
    public function post_share() {

          $share_link = '?post_id='.$_POST['post_id'];

           $postdata = $this->common->getData('post', array('id' => $_POST['post_id']), array('single'));

           $post_image = $this->common->getData('post_image', array('post_id' => $_POST['post_id']), array(''));

           $post_user_fullname = user_detail($postdata['user_id']);

          $_POST['share_link'] =  $share_link;
            $noti = "";
        if (!empty($_POST['user_id'])) {
            if(!empty($_POST['share_to'])){

                     $share = $this->common->getField('post_share', $_POST);
                     $result = $this->common->insertData('post_share', $share);
                     $shareid = $this->db->insert_id();

                     if (!empty($shareid)) {
                            $reciver = user_detail($value);
                            $sender = user_detail($_POST['user_id']);

                            $post  = array('user_id'=>$_POST['share_to'],
                                'album_name'=>$postdata['album_name'],
                                'post_type'=>$postdata['post_type'],
                                'type'=>$postdata['type'],
                                'description'=>$postdata['description'],
                                'question'=>$postdata['question'],
                                "refer_user_id"=>$postdata['user_id'],
                                "refer_post_id"=>$postdata['id'],
                                "refer_created_at"=> $postdata['created_at'], 
                                "created_at"=>date('Y-m-d H:i:s'));
                                   
                            $result = $this->common->insertData('post',$post);

                            $post_arr_id = $this->db->insert_id();

                            if($post_image){
                                foreach ($post_image as $image) {
                                        $img_arr  = array('user_id'=>$_POST['share_to'],
                                                'post_id'=>$post_arr_id,
                                                'image'=>$image['image'],
                                                'post_type'=>$image['post_type'],
                                                "created_at"=>date('Y-m-d H:i:s'));
                                        $img = $this->common->insertData('post_image',$img_arr);
                                    }   
                              }
                                
                            $noti  = $this->common->send_all_notification($postdata['user_id'],ucwords($sender['full_name']).' shared you a post by '.ucwords($post_user_fullname['full_name']),"Shared",'2',array('refer_id'=>$_POST['post_id']));
                        }

               // }
            }
            $this->response(true, "Successfully Shared!");
        } else {
            $this->response(false, "Not Shared!");
            exit();
        }
    }
  
    /////////////////////////////////Chat messages///////////////////////////
    public function send_message() {

        $date =  date('Y-m-d');

        if(!empty($_GET['group_id'])){
            $group = $_GET['group_id'];
        }else{
            $group = '0';
        }
        $result = $this->common->insertData('chat_messages', array('sender_id' => $_GET['sender_id'], 'sender_name' => $_GET['sender_name'],'receiver_id' => $_GET['receiver_id'], 'receiver_name' => $_GET['receiver_name'], 'messages' => $_GET['messages'],'image' => $_GET['image'],'type' => $_GET['type'],'group_id'=>$group,'date'=>$date, 'created_at' => $_GET['created_at']));

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
                $url =  $image_name;
            }
        }
        if ($image_name != "") {
            $this->response(true, "file Upload Successfully!", array('image_url' => $url));
        } else {
            $this->response(false, "Not Sent!");
        }
    }

    public function user_chat_messages() {
        $user_id = $_POST['user_id'];
        $sender_id = $_POST['sender_id'];
        $array = array();
        if (!empty($user_id)) {
            $where = " (sender_id = '" . $user_id . "' and receiver_id = '" . $sender_id . "') OR  (receiver_id = '" . $user_id . "' and sender_id = '" . $sender_id . "') ";

        $chat_messages = $this->common->getData('chat_messages', $where, array('group_by'=>'date','sort_by'=>"messages_id",'sort_direction'=>"ASC"));
          

        if (!empty($chat_messages)) {
            foreach ($chat_messages as $key => $value){ 

                 $where1 = " (sender_id = '" . $value['sender_id'] . "' and receiver_id = '" . $value['receiver_id'] . "' OR  receiver_id = '" . $value['sender_id'] . "' and sender_id = '" . $value['receiver_id']."') AND (date = '".$value['date']."')";

                 $chat_msg = $this->common->getData('chat_messages', $where1, array('sort_by'=>"messages_id",'sort_direction'=>"ASC"));


                       if(!empty($chat_msg)){

                        foreach ($chat_msg as $valuemsg) {

                               if (!empty($valuemsg['messages'])) {

                                    if($valuemsg['type'] > 1){

                                          $valuemsg['messages'] = base_url().$valuemsg['messages'];
                                          
                                    }
                                  
                                }
                                $chat[] = $valuemsg;
                        }
                            
                            $arr['data'] = $chat;
                            $arr['title'] = (integer) $value['created_at'];
                            $array[] = $arr;
                       }

                }

                //array_unshift($array,array("data"=>array(),'title'=>0));
               // $array1 = array_unshift($array,);
            }
            $this->response(true, "Fetch messages Successfully!", array("chat_messages" => $array));
        } else {
            $this->response(false, "No message found", array("chat_messages" => array()));
        }
    }


    public function group_messages() {
        $user_id = $_POST['user_id'];
        $group_id = $_POST['group_id'];
        $array = array();
        if (!empty($group_id)) {
        $chat_messages = $this->common->getData('chat_messages', array('group_id'=> $group_id), array('group_by'=>'date','sort_by'=>"messages_id",'sort_direction'=>"ASC"));

        if (!empty($chat_messages)) {
            foreach ($chat_messages as $key => $value){ 

                 $chat_msg = $this->common->getData('chat_messages', array('group_id'=> $value['group_id'],'date'=>$value['date']), array('sort_by'=>"messages_id",'sort_direction'=>"ASC"));
                  //  echo $this->db->last_query();
                       if(!empty($chat_msg)){

                        //   foreach ($chat_msg as $valuemsg) {

                        //        if (!empty($valuemsg['messages'])) {

                        //             if($valuemsg['type'] > 1){

                        //                   $valuemsg['messages'] = base_url().$valuemsg['messages'];
                        //             }
                        //         }
                        //         $chat[] = $valuemsg;
                        // }
                            $arr['title'] = (integer) $value['created_at'];
                            $arr['data'] = $chat_msg;
                            $array[] = $arr;
                       }
                }
            }
            $this->response(true, "Fetch messages Successfully!", array("chat_messages" => $array));
        } else {
            $this->response(false, "No message found", array("chat_messages" => array()));
        }
    }


     public function delete_chat() {
        $sender_id = $_POST['user_id'];
        $receiver_id = $_POST['sender_id'];
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
   //////////////////////// daily Question/////
		public function get_daily_question() 
		{
		 $ios=array();
         $this->db->select('D.*');
          $this->db->from('daily_question as D');
         $this->db->order_by('rand()');
         $this->db->limit(1);
         $query = $this->db->get('daily_question'); 
        $daily_question =  $query->result_array();
	     $message = $daily_question[0]['question'];
		 if(!empty($message))
		{
		$all_users = $this->common->getData('user', array(), array());
		foreach ($all_users as $key => $value) 
		{
	     	if(!empty($value['fcm_token']) && $value['fcm_token']!='null')
	     	{
		    $ios = $value['fcm_token'];

		   $que = $this->common->daily_que_notification($ios,$message,$daily_question[0]['que_type'],$daily_question[0]['id']);
		   }
	    }
	     }
		if($que)
		 {
		$this->response(true, "Fetch messages Successfully!", array($que));
		 } else {
		$this->response(false, "No message found", array());
		}
		}
     ///End//

      public function testnoti(){
        $user_id = $_POST['user_id'];
         $send_all = $this->common->send_all_notification($user_id,"hello ",$type,$data=array());

         print_r($send_all);
      }
       

   public function daily_question_response() {
        $user_id = $_POST['user_id'];
        $daily_question_id = $_POST['question_id'];
        $response  = $_POST['response'];
        $created_at  = date('Y-m-d H:i:s');
        $array = array();
        if (!empty($user_id)) {

            $noti  =  array('daily_question_id'=>$daily_question_id,'response'=>$response,'created_at'=>$created_at);

            $result = $this->common->insertData('daily_question_response', $noti);
            
            $this->response(true, "Response Added Successfully!");
        } else {
            $this->response(false, "No message found");
        }
    }




}