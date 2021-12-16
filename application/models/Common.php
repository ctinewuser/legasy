<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends CI_Model
{
  function __construct()
  {
    parent::__construct();
  }
  public function Update_login_data()
  {
    $data = array(
      'last_activity' => date('Y-m-d H:i:s')
    );
    $this->db->where('login_data_id', $this->session->userdata('id'));
    $this->db->update('login_data', $data);
  }
  public function User_last_activity($user_id)
  {
    $this->db->where('user_id', $user_id);
    $this->db->order_by('login_data_id', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get('login_data');
    foreach ($query->result() as $row)
    {
      return $row->last_activity;
    }
  }
  public function getData($table, $where = "", $options = array())
  {
    if (isset($options['field']))
    {
      $this->db->select($options['field']);
    }
    if ($where != "")
    {
      $this->db->where($where);
    }
    if (isset($options['where_in']) && isset($options['where_in']))
    {
      $this->db->where_in($options['colname'], $options['where_in']);
    }
    if (isset($options['sort_by']) && isset($options['sort_direction']))
    {
      $this->db->order_by($options['sort_by'], $options['sort_direction']);
    }
    if (isset($options['group_by']))
    {
      $this->db->group_by($options['group_by']);
    }
    if (isset($options['limit']) && isset($options['offset']))
    {
      $this->db->limit($options['limit'], $options['offset']);
    }
    else
    {
      if (isset($options['limit']))
      {
        $this->db->limit($options['limit']);
      }
    }
    $query = $this->db->get($table);
    $result = $query->result_array();
    if (!empty($options) && in_array('count', $options))
    {

      return count($result);
    }
    if ($result)
    {
      if (isset($options) && in_array('single', $options))
      {
        return $result[0];
      }
      else
      {
        return $result;
      }
    }
    else
    {
      if (isset($options) && in_array('api', $options))
      {
        return array();
      }
      return false;
    }
  }
  public function getField($table, $data)
  {
    $post = array();
    $fields = $this->db->list_fields($table);
    foreach ($data as $key => $value)
    {
      if (in_array($key, $fields))
      {
        $post[$key] = $value;
      }
    }
    return $post;
  }
  public function getFieldKey($table)
  {
    return $this->db->list_fields($table);
  }
  public function insertData($table, $data)
  {
    return $this->db->insert($table, $data);
  }
  public function updateData($table, $data, $where)
  {
    return $this->db->update($table, $data, $where);
  }
  public function checkTrue()
  {
    if ($this->db->affected_rows())
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  public function deleteData($table, $where)
  {
    return $this->db->delete($table, $where);
  }
  function query($sql)
  {
    $query = $this->db->query($sql);
    if ($query->num_rows() > 0)
    {
      return $query->result_array();
    }
    else
    {
      return FALSE;
    }
  }
  public function whereIn($table, $colname, $in, $where = array())
  {
    $this->db->where($where);
    $search = "FIND_IN_SET('" . $in . "', $colname)";
    $this->db->where($search);
    $query = $this->db->get($table);
    $result = $query->result_array();
    if ($result)
    {
      return $result[0];
    }
    else
    {
      return false;
    }
  }
  public function arrayToName($table, $field, $array)
  {
    foreach ($array as $value)
    {
      $name[] = $this->getData($table, array(
        'id' => $value
      ) , array(
        'field' => $field,
        'single'
      ));
    }
    if (!empty($name))
    {
      foreach ($name as $key => $value)
      {
        $name1[] = $value[$field];
      }
      return implode(',', $name1);
    }
    else
    {
      return false;
    }
  }


  public function sendMail($to, $subject, $message, $options = array())
  {
    $msg = "";
    include (APPPATH . 'third_party/phpmailer/class.phpmailer.php');
    $account = "ctichandni@ctinfotech.com";
    $password = "cticti@12345";
    $msg .= $message;
    $from = "ctichandni@ctinfotech.com";
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = "mail.ctinfotech.com";
    $mail->SMTPAuth = true;
    $mail->Port = 587; // Or 587
    $mail->Username = $account;
    $mail->Password = $password;
    $mail->SMTPSecure = 'tls';
    $mail->From = $from;
    $mail->Body = $msg;
    $mail->FromName = "GoInflu";
    $mail->isHTML(true);
    $mail->Subject = $subject;
    if (!empty($options))
    {
      while (list($key, $val) = each($options))
      {
        $mail->addAddress($val);
      }
    }
    else
    {
      $mail->addAddress($to);
    }
    $send = $mail->send();
    if ($send)
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  public function do_upload($file, $path)
  {
    $this->load->library('image_lib');
    $config['upload_path'] = $path;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = '*';
    $this->load->library('upload', $config);
    if (!$this->upload->do_upload($file))
    {
      $error = array(
        'error' => $this->upload->display_errors()
      );
      $data['error_msg'] = $this->upload->display_errors();
      return $error;
    }
    else
    {
      $image_data = $this->upload->data();
      $configer = array(
        'image_library' => 'gd2',
        'source_image' => $image_data['full_path'],
        'maintain_ratio' => TRUE,
        'width' => 500,
        'height' => 500,
      );
      $this->image_lib->clear();
      $this->image_lib->initialize($configer);
      $this->image_lib->resize();
      $data = array(
        'upload_data' => $this->upload->data()
      );
      $data['success_msg'] = 'File has been uploaded successfully.';
      return $data;
    }
  }
  public function file_compress($userfileName, $path)
  {
    $this->load->library('image_lib');
    $config['upload_path'] = $path;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'gif|jpg|png|jpeg|JPD|PMG|jpd|pmg';
    $this->load->library('upload', $config);
    if (!$this->upload->do_upload($userfileName))
    {
      $error = array(
        'error' => $this->upload->display_errors()
      );
      $data['error_msg'] = $this->upload->display_errors();
      return $error;
    }
    else
    {
      $image_data = $this->upload->data();
      $configer = array(
        'image_library' => 'gd2',
        'source_image' => $image_data['full_path'],
        'maintain_ratio' => TRUE,
        'width' => 500,
        'height' => 500,
      );
      $this->image_lib->clear();
      $this->image_lib->initialize($configer);
      $this->image_lib->resize();
      $data = array(
        'upload_data' => $this->upload->data()
      );
      $data['success_msg'] = 'File has been uploaded successfully.';
    }
  }
  
  public function multi_upload($file, $path)
  {
    $config = array();
    $config['upload_path'] = $path; // upload path eg. - './resources/images/products/';
    $config['allowed_types'] = '*';
    $config['encrypt_name'] = true;
    //$config['max_size']      = '0';
    $config['overwrite'] = FALSE;
    $this->load->library('upload', $config);
    $dataInfo = array();
    $files = $_FILES;
    foreach ($files[$file]['name'] as $key => $image)
    {
      $_FILES[$file]['name'] = $files[$file]['name'][$key];
      $_FILES[$file]['type'] = $files[$file]['type'][$key];
      $_FILES[$file]['tmp_name'] = $files[$file]['tmp_name'][$key];
      $_FILES[$file]['error'] = $files[$file]['error'][$key];
      $_FILES[$file]['size'] = $files[$file]['size'][$key];
      $this->upload->initialize($config);
      if ($this->upload->do_upload($file))
      {
        $dataInfo[] = $this->upload->data();
      }
      else
      {
        return $this->upload->display_errors();
      }
    }
    if (!empty($dataInfo))
    {
      return $dataInfo;
    }
    else
    {
      return false;
    }
  }
  function get_record_join_two_table($table1, $table2, $id1, $id2, $column = '', $where = '', $orderby = '', $options = array())
  {
    if ($column != '')
    {
      $this->db->select($column);
    }
    else
    {
      $this->db->select('*');
    }
    $this->db->from($table1);
    $this->db->join($table2, $table2 . '.' . $id2 . '=' . $table1 . '.' . $id1);
    if ($where != '')
    {
      $this->db->where($where);
    }
    if ($orderby != '')
    {
      $this->db->order_by($orderby, 'desc');
    }
    $query = $this->db->get();
    $result = $query->result_array();
    if ($result)
    {
      if (isset($options) && in_array('single', $options))
      {
        return $result[0];
      }
      else
      {
        return $result;
      }
    }
    else
    {
      return false;
    }
  }
  function get_data_join_four_tabel_where($table1, $table2, $table3, $table4, $id1, $id2, $id3, $id4, $id5, $id6, $column = '', $where, $orderby = '', $options = array())
  {
    if ($column != '')
    {
      $this->db->select($column);
    }
    else
    {
      $this->db->select('*');
    }
    $this->db->from($table1);
    $this->db->join($table2, $table2 . '.' . $id1 . '=' . $table1 . '.' . $id2);
    $this->db->join($table3, $table3 . '.' . $id3 . '=' . $table1 . '.' . $id4);
    $this->db->join($table4, $table4 . '.' . $id5 . '=' . $table1 . '.' . $id6);
    $this->db->where($where);
    if ($orderby != '')
    {
      $this->db->order_by($orderby, 'desc');
    }
    $query = $this->db->get();
    $result = $query->result_array();
    if ($result)
    {
      if (isset($options) && in_array('single', $options))
      {
        return $result[0];
      }
      else
      {
        return $result;
      }
    }
    else
    {
      return false;
    }
  }
  //////////////////////////////Notification///////////////////
    public function sendNotification_android($tokens, $message, $data = array())
    {   
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
          'registration_ids' => $tokens,
          "notification" => $message,
          "data" => $data
        );

         $headers = array(
        'Authorization:key = AAAAbflq5RM:APA91bGg4IcaaVsMBbo4gJNZLuzD_K5aRXxXVpEsTQR_Jj7kax85TdcqmT5bhhk3vwW36y6TIv0orC5y-ogMZaRzRHMDPsAT3eW74lk1Z-JZVFTevk-JdxYKZBYNA0Nn_MLBqxg0vUoy',
        'Content-Type: application/json'
        );
      
        return $this->curl($url,$headers,$fields);

    }
  
    public function sendNotification_ios($tokens,$message,$title,$type,$data,$datarray=array())
    {   
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAAbflq5RM:APA91bGg4IcaaVsMBbo4gJNZLuzD_K5aRXxXVpEsTQR_Jj7kax85TdcqmT5bhhk3vwW36y6TIv0orC5y-ogMZaRzRHMDPsAT3eW74lk1Z-JZVFTevk-JdxYKZBYNA0Nn_MLBqxg0vUoy';
       
        $notification = array('title'=>$title, "body"=>$message,'type'=>$type,
       'text'=>$data,'sound'=>'default','badge'=>'0');

       
        $fields = array('to' => $tokens,'notification'=>$notification,'data'=>$datarray,'priority'=>'high');

        // echo "<pre>";
        // print_r($fields);
        // die();

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        return $this->curl($url,$headers,$fields);
    
    }

    public function sendNotification_fcm($tokens,$message,$title,$type,$data,$datarray=array())
    {   
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAAbflq5RM:APA91bGg4IcaaVsMBbo4gJNZLuzD_K5aRXxXVpEsTQR_Jj7kax85TdcqmT5bhhk3vwW36y6TIv0orC5y-ogMZaRzRHMDPsAT3eW74lk1Z-JZVFTevk-JdxYKZBYNA0Nn_MLBqxg0vUoy';
        $notification = array('title'=>$title, "body"=>$message,'type'=>$type,
       'text'=>$data,'sound'=>'default','badge'=>'0');
        $fields = array('to' => $tokens,'notification'=>$notification,'data'=>$datarray,'priority'=>'high');
        // echo "<pre>";
        // print_r($fields);
        // die();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        return $this->curl($url,$headers,$fields);
    
    }

     public function curl($url, $headers, $fields)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE)
        {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

public function post_comments($post_id=""){
  $this->db->select('*'); 
  $this->db->where('post_id',$post_id);
  $this->db->where('refer_id',0);
  $this->db->from('post_comment');
  $this->db->order_by('comment_id','desc');
  $query = $this->db->get();
  if($query->num_rows()>0)
  {
      $row = $query->result_array();
      return $row;
  }
  else
      {
      return "";
      }
}


public function fetch_post_reply_comments($article_id,$parent_id,$user_id) { 
    $sql = "select  comment_id,post_id,reciever_id,sender_id,message,created_date,created_time,created_at,refer_id,total_like 
    from    (select * from post_comment
             order by refer_id,comment_id) products_sorted,
            (select @pv := '".$parent_id."') initialisation
    where   find_in_set(refer_id, @pv)
    and     length(@pv := concat(@pv, ',', comment_id))
    and post_id = '".$article_id."'";// order by refer_id asc,created_at desc
    $query = $this->db->query($sql);
    //echo $this->db->last_query();
    $comments =  $query->result_array();
    $comm = array();
    if(!empty($comments)){
      foreach ($comments as $key => $value) {
         $user =$this->common->getData('user',array('id'=>$value['sender_id']),array('single'));

         $like = $this->common->getData('post_comment_like',array("comment_id"=>$value['comment_id'],"user_id"=>$user_id),array('single'));              
          if(!empty($like)){
              $value['is_comment_like'] = 1;
          }else{
              $value['is_comment_like'] = 0;
          }    
          $value['full_name'] = $user['full_name'];
          $value['profile_image'] = $user['profile_image'];
          $value['email'] = $user['email'];
          $comm[] = $value;
      }
    }
     return $comm;   
}

public function getcomment($post_id,$user_id){
    $this->db->select('P.*,U.full_name,U.profile_image,U.email');
    $this->db->from('post_comment as P');
    $this->db->join('user as U', 'U.id = P.sender_id');
    $this->db->where('P.post_id',$post_id);
    $this->db->where('P.refer_id',0);
    $query = $this->db->get();
    $comments =  $query->result_array();
     if(!empty($comments)){
        foreach ($comments as $key => $value) {

          $value['post_reply'] =  $this->common->fetch_post_reply_comments($value['post_id'], $value['comment_id'],$user_id); 

          $value['total_reply'] = count($value['post_reply']);

         $like = $this->common->getData('post_comment_like',array("comment_id"=>$value['comment_id'],"user_id"=>$user_id),array('single'));              
        if(!empty($like)){
              $value['is_comment_like'] = 1;
          }else{
              $value['is_comment_like'] = 0;
          }  
          $data[] = $value;

        }
        return $data;
    }
}


public function send_all_notification($user_id,$message,$title,$type,$data=array()){
       $userdetail = user_detail($user_id);
     
       $body = $message;
       $title = "Legacy";


       $msg_notification = array("body" =>$body,"title"=>$title,'type'=>$type,$data); 

        // if(!empty($userdetail['fcm_token']))
        // {
        //   $res = $this->common->sendNotification_android(array($userdetail['fcm_token']), $msg_notification, $msg_notification);
        
        // }   
        if(!empty($userdetail['fcm_token']))
        {
            $messages_push =  array("notification" => $msg_notification);
            $res = $this->common->sendNotification_ios($userdetail['fcm_token'],$body,$title,$type,$messages_push); 
              
        }

        if($res){
            $json_array=json_decode($res ,true);
            $created_at = date('Y-m-d H:i:s');
            if($json_array["success"]>0){

              
                 $noti  =  array('message'=>$body,'user_id'=>$user_id,'created_at'=>$created_at,
                   'type_name'=>$title,'type'=>$type,'refer_id'=>$data['refer_id']);
                 $result = $this->common->insertData('notification', $noti);
            }
        }

     return $res;
    }    

  public function artist_refer_detail($ref_artist_id,$ref_user_id,$fans_id)
  {

      $insertid = 0;
       $credit_amount = $this->common->getData('credit_amount',array(),array());
      $amount = $credit_amount[0]['amount'];
    
      $share_link = 'deeplinking/artist_army.php?artist_id='.$ref_artist_id.'&user_id='.$ref_user_id.'&type=3';

      $credits = array("refer_id" => $ref_user_id, "fans_id" =>$fans_id, "artist_id" => $ref_artist_id, "amount" => $amount,"song_no" => '0');

      $artist_arr = array("artist_id" => $ref_artist_id, "user_id" =>$ref_user_id, "share_to" => $fans_id,'share_link'=>$share_link);
      $getcredits = $this->common->getData('credits',$credits,array('single'));


      $getartistshare = $this->common->getData('artist_page_share',$artist_arr,array('single'));

      if(empty($getcredits))
      {
        $update = $this->common->updateData('user', array('ref_user_id'=>$ref_user_id,'ref_artist_id'=>$ref_artist_id),array('id' => $fans_id));

        $insertid = $this->common->insertData('credits',$credits);

        $amounthalf = floatval($amount/2);
        $credits1 = array("refer_id" => $fans_id, "fans_id" => $ref_user_id, "artist_id" => $ref_artist_id, "amount" =>$amounthalf,"song_no" => "0",'ref_fans_id'=>$fans_id);

        $result = $this->common->insertData('credits', $credits1);
                  
      }
      if(empty($getartistshare))
      {
         $addshare = $this->common->insertData('artist_page_share',$artist_arr);
         $shareid = $this->db->insert_id();
          if (!empty($shareid)) {
                
                $reciver = user_detail($fans_id);
                $sender = user_detail($ref_user_id);

                $result = $this->common->insertData('chat_messages', array('sender_id' => $ref_user_id, 'sender_name' => isset($sender['full_name']) ? $sender['full_name'] : "", 'sender_image' => $sender['profile_image'], 'receiver_id' => $fans_id, 'receiver_name' => $reciver['full_name'], 'receiver_image' => $reciver['profile_image'], 'messages' => $share_link, 'image' => "", "type" => "3", 'refer_id'=>$ref_artist_id,'datetime' => strtotime(date('Y-m-d'))));

                $notifi = $this->send_all_notification($ref_user_id,'You have earned '.$amount.' credit points ',"Credits",'4',array('refer_id'=>$ref_user_id));
              }
      }
        return $getcredits;
 }


public function credit_amount($fans_id,$user_id){
        $this->db->select('SUM(C.amount) as amount ,C.refer_id,C.artist_id,C.ref_fans_id,C.created_at,U.full_name,U.email,U.total_like,U.profile_image,U.id as fans_id');
        $this->db->from('credits as C');
        $this->db->join('user as U', 'U.id = C.refer_id');
        $this->db->where('C.refer_id', $fans_id);
        $this->db->where('C.artist_id', $user_id);
        $this->db->group_by('C.refer_id');
        $query = $this->db->get();
        $result = $query->result_array()[0];

        if ($result)
        {
           return $result;
        } else {
          return  array();
        }
      }

public function add_post_comment($post_id){
      $this->db->select('P.*,U.android_token,U.ios_token,U.full_name');
      $this->db->from('user as U');
      $this->db->join('post as P', 'P.user_id = U.id');
      $this->db->where('P.id', $post_id);
      $query = $this->db->get();
      $post_list = $query->result_array()[0];
     if($post_list)
        {
          return $post_list;
        } else {
          return  array();
        }
}
public function getmemberprofile($user_id,$offset="",$limit="",$page_count=""){
        $this->db->select('M.*,U.profile_image,U.full_name');
        $this->db->from('user_members as M');
        $this->db->join('user as U', 'M.member_id = U.id');
        $this->db->where('M.user_id', $user_id);

        $this->db->where('M.member_id !=', $user_id);

        $this->db->order_by('M.id', 'desc');

          if(!empty($page_count)){
               $this->db->limit($offset,$limit);
          }
        $query = $this->db->get();
        $memberprofile = $query->result_array();
      if($memberprofile)
          {
            return $memberprofile;
          } else {
            return  array();
          }
}
public function category($search=""){

        $this->db->select('C.*,(SELECT COUNT(*) FROM legacy WHERE cat_id = C.id) AS total_legacy');
        $this->db->from('category C');
        if (!empty($search)) {
            $this->db->where("C.cat_name LIKE '%" . $search . "%'");
        }
        $this->db->where('C.status', '0');
        $query = $this->db->get();
        $category = $query->result_array();
        if($category)
          {
            return $category;
          } else {
            return  array();
          }
}
public function post_list($user_id="",$limit, $offset,$page_count){
        if(!empty($user_id)) {

        $subquery = "(exists (select 1  from post_like L where L.post_id = P.id and L.user_id = '".$user_id."') ) as is_like ,";
        } else {
            $subquery = "";
        }
        $this->db->select('P.*,'.$subquery .'U.profile_image as user_profile_image,U.full_name as user_full_name');
        $this->db->from('post as P');
        $this->db->join('user as U', 'P.user_id = U.id');
        $this->db->order_by('P.id', 'desc');
          if(!empty($page_count)){
               $this->db->limit($offset,$limit);
          }
         $query = $this->db->get();
      // echo $this->db->last_query();
        $post_list = $query->result_array();
      if($post_list)
          {
            return $post_list;
          } else {
            return  array();
          }
}


  public function  all_likes($id,$type){
         if ($type == 1) {
              $sql = "Select U.*,P.* from post_like as P JOIN user as U ON P.user_id = U.id where post_id = '".$id."'";
          } else if ($type == 2) {
              $sql = "Select U.*,P.* from post_comment_like as P  JOIN user as U ON P.user_id = U.id where comment_id = '".$id."'";
          }
          $query = $this->db->query($sql);
          $like_list = $query->result_array();
          if($like_list)
            {
              return $like_list;
            } else {
              return  array();
            }
  }
 public function  get_events($user_id){
        if (!empty($user_id)) {
          $subquery = "(exists (select 1 from group_events_member EM where EM.event_id = E.id and EM.user_id = '".$user_id."' OR E.user_id = '".$user_id."') ) as is_event";
        } 
        $this->db->select('E.*,U.full_name,U.profile_image,'.$subquery);
        $this->db->from('group_events as E');
        $this->db->join('user as U', 'U.id = E.user_id');
        $this->db->having('is_event','1');
        $query = $this->db->get();
        //echo $this->db->last_query();
        $get_events = $query->result_array();
        if($get_events)
        {
          return $get_events;
        } else {
          return  array();
        }
}

public function  get_invoice($user_id){
        if (!empty($user_id)) {
          $subquery = "(exists (select 1 from invoice_member IM where IM.invoice_id = I.id and IM.user_id = '".$user_id."' OR I.user_id = '".$user_id."') ) as is_invoice";
        } 
        $this->db->select('I.*,U.full_name,U.profile_image,'.$subquery);
        $this->db->from('invoice as I');
        $this->db->join('user as U', 'U.id = I.user_id');
        $this->db->having('is_invoice','1');
        $query = $this->db->get();
        //echo $this->db->last_query();
        $get_events = $query->result_array();
        if($get_events)
        {
          return $get_events;
        } else {
          return  array();
        }
}

public function  legacy_by_category($cat_id,$user_id,$legacy_id=""){
        $this->db->select('L.*,U.full_name,U.profile_image');
        $this->db->from('legacy as L');
        $this->db->join('user as U', 'U.id = L.user_id');
        $this->db->where('L.cat_id',$cat_id);
        if($legacy_id){
               $this->db->where('L.id',$legacy_id);
          }
        $query = $this->db->get();
        $legacy = $query->result_array();
        if($legacy)
        {
          return $legacy;
        } else {
          return  array();
        }
}


public function legacy_questions($user_id="",$type="",$cat_id="",$legacy_id=""){
    $this->db->select('L.*,U.full_name,U.profile_image,U.email');
    $this->db->from('legacy_questions as L');
    $this->db->join('user as U', 'U.id = L.user_id');

    if($type == 1 && !empty($cat_id) && !empty($legacy_id)){
       $this->db->where('L.legacy_id',$legacy_id);
       $this->db->where('L.cat_id',$cat_id);
    }

    if(!empty($cat_id)){
       $this->db->where('L.cat_id',$cat_id);
    }

    $query = $this->db->get();
    $questions =  $query->result_array();
    //echo $this->db->last_query();
     if(!empty($questions)){
        foreach ($questions as $key => $value) {
          $value['response'] =  $this->common->fetch_legacy_response($value['legacy_id'], $value['id'],$user_id); 
          $value['total_response'] = count($value['response']);
          $data[] = $value;

        }
        return $data;
    }
}

public function fetch_legacy_response($legacy_id,$parent_id,$user_id) { 
    $sql = "select  id,user_id,legacy_id,question_id,response_type,response_video,response_audio,response,date,time,created_at,refer_id
    from    (select * from legacy_questions_response
             order by question_id) products_sorted,
            (select @pv := '".$parent_id."') initialisation
    where   find_in_set(question_id, @pv)
    and     length(@pv := concat(@pv, ',', question_id))
    and legacy_id = '".$legacy_id."'";
    // order by refer_id asc,created_at desc
    $query = $this->db->query($sql);
    //echo $this->db->last_query();
    $legacy =  $query->result_array();
    $leg = array();
    if(!empty($legacy)){
      foreach ($legacy as $key => $value) {
         $user =$this->common->getData('user',array('id'=>$value['user_id']),array('single'));
          $value['full_name'] = $user['full_name'];
          $value['profile_image'] = $user['profile_image'];
          $value['email'] = $user['email'];
          $leg[] = $value;
      }
    }
     return $leg;   
}

// public function  all_groups($user_id){
//       $subquery = "(SELECT COUNT(*) FROM create_group_member WHERE group_id=C.id) AS total_member ,";
//       $this->db->select('U.full_name,U.email,U.profile_image',$subquery);
//       $this->db->from('create_group as C');
//       $this->db->join('user as U', 'U.id = C.user_id');
//       $this->db->where('C.user_id', $user_id);
//       $query = $this->db->get();
//       $all_groups = $query->result_array();
            
     
//         if($all_groups)
//         {
//           return $all_groups;
//         } else {
//           return  array();
//         }
// }

public function  all_groups_by_member($user_id){
        if (!empty($user_id)) {
          $subquery = "(exists (select 1 from create_group_member CM where CM.group_id = C.id and CM.member_id = '".$user_id."' OR C.user_id = '".$user_id."') ) as is_member";
        } 
        $this->db->select('C.*,U.full_name,U.profile_image,'.$subquery);
        $this->db->from('create_group as C');
        $this->db->join('user as U', 'U.id = C.user_id');
        $this->db->having('is_member','1');
        $query = $this->db->get();
        $member = $query->result_array();
        if($member)
        {
          return $member;
        } else {
          return  array();
        }
}

// public function  fanProfile($user_id){
//       $subquery = "
//         (SELECT COUNT(*) FROM friends WHERE user_id=U.id) AS total_friend ,
//         (SELECT COUNT(*) FROM artist_page_share WHERE user_id=U.id) AS total_shared ,
//         (SELECT Count(*) from redeem_checkout where user_id = U.id) AS total_redeem,
//         (SELECT SUM(amount) from credits where refer_id = U.id) AS total_credits";
//         // (SELECT Count(*) from goals where find_in_set(redeem_members,U.id)<>0 ) AS total_redeem
//         //(SELECT COUNT(*) FROM goals WHERE user_id=U.id) AS total_credits ,
//         $this->db->select('U.*,G.genre_name,G.genre_image,' . $subquery);
//         $this->db->from('user as U');
//         $this->db->join('genre_category as G', 'G.genre_id = U.genre_cat', 'LEFT');
//         $this->db->where('U.id', $user_id);
//         $query = $this->db->get();
//         $fandata = $query->result_array()[0];
//         if($fandata)
//         {
//           return $fandata;
//         } else {
//           return  array();
//         }
// }

public function  get_library($user_id){
        $this->db->select('L.*,leg.video,leg.audio,leg.legacy_type');
        $this->db->from('library as L');
        $this->db->join('legacy as leg', 'leg.id = L.legacy_id');
        $this->db->where('L.user_id', $user_id);
        $this->db->order_by('L.id', 'desc');
        $query = $this->db->get();
        $get_library = $query->result_array();
        if($get_library)
        {
          return $get_library;
        } else {
          return  array();
        }
}

public function  chat_users($user_id="",$user_type){
          if ($user_type == '0') {
                $this->db->select('U.id,U.full_name,U.profile_image');
                $this->db->from('friends as F');
                $this->db->join('user as U', 'U.id = F.friends_id');
                $this->db->where('F.user_id', $user_id);
                $query = $this->db->get();
                $chat_users = $query->result_array();
            }
            if ($user_type == '1') {
                $this->db->select('U.id,U.full_name,U.email,U.profile_image');
                $this->db->from('follower as F');
                $this->db->join('user as U', 'U.id = F.fans_id');
                $this->db->where('F.artist_id', $user_id);
                $query = $this->db->get();
                $chat_users = $query->result_array();
            }
     
        if($chat_users)
        {
          return $chat_users;
        } else {
          return  array();
        }
}
public function  home_search($user_id="",$search=""){
     
      $this->db->select('U.*');
      $this->db->from('user as U');
      if (!empty($search)) {
        $this->db->like('U.full_name', $search);
        $this->db->or_like('U.artist_name', $search);
      }
      $this->db->where('id !=', $user_id);
      $query = $this->db->get();
      $userinfo = $query->result_array();
        if($userinfo)
        {
          return $userinfo;
        } else {
          return  array();
        }
}
  ////Question////
public function daily_que_notification($data,$message,$title,$id){
      
     
       $body = $message;
       $msg_notification = array("body" =>$body,"title"=>$title,'type'=>"4"); 

        $datarray =array('type'=>'Question','id'=>$id,"body" =>$body,"title"=>$title);
        if(!empty($data))
        {
         /*   $data = json_encode($data,true);
      */
            $messages_push =  array("notification" => $msg_notification);

            $res = $this->common->sendNotification_ios($data,$body,$title,'4',$messages_push,$datarray); 
              
        }
       
        if($res){
            $json_array=json_decode($res ,true);
            $created_at = date('Y-m-d H:i:s');

            if($json_array["success"]>0){

              
                 $noti  =  array('message'=>$body,'user_id'=>"",'created_at'=>$created_at,
                   'type_name'=>$title,'type'=>"4",'refer_id'=>'');
                 $result = $this->common->insertData('notification', $noti);
            }
        }

     return $res;
    }    

    /////End///
public function  get_notification_by_date($user_id="",$limit,$offset,$page_count){
     
      $this->db->select('*');
      $this->db->from('notification');
      $this->db->where('user_id', $user_id);
      $this->db->group_by('created_at');
      $this->db->order_by('created_at', 'DESC');
       if(!empty($page_count)){
          $this->db->limit($offset,$limit);
          }
      $query = $this->db->get();
      $notification = $query->result_array();
        if($notification)
        {
          return $notification;
        } else {
          return  array();
        }
}







}