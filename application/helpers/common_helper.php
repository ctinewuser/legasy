<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function user_detail($id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');
    $user_detail = $CI->common->getData('user',array('id'=>$id),array('single'));

    if(!empty($user_detail)){
     return $user_detail;
    }
    else{
      return false;
    }
}

function exists_events_member($event_id,$user_id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');
    $events = $CI->common->getData('group_events_member',array('event_id'=>$event_id,'user_id'=>$user_id),array('single'));

    if(!empty($events)){
     return 1;
    }
    else{
      return false;
    }
}

function group_events_member($event_id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');

    $CI->db->select('U.profile_image,U.full_name');
    $CI->db->from('group_events_member as E');
    $CI->db->join('user as U', 'E.user_id = U.id');
      $CI->db->where('E.event_id', $event_id);
    $query =  $CI->db->get();
    $events = $query->result_array();
    if(!empty($events)){
     return $events;
    }
    else{
      return array();
    }
}


function create_group_member($group_id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');

    $CI->db->select('C.*,U.profile_image,U.full_name');
    $CI->db->from('create_group_member as C');
    $CI->db->join('user as U', 'C.member_id = U.id');
      $CI->db->where('C.group_id', $group_id);
    $query =  $CI->db->get();
    $group = $query->result_array();
    if(!empty($group)){
     return $group;
    }
    else{
      return array();
    }
}

function user_full_name($id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');
    $user_detail = $CI->common->getData('user',array('id'=>$id),array('single'));

    if(!empty($user_detail)){
      return $user_detail['full_name'];
    }else{
     return false;
    }
}
function user_members($user_id,$member_id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');
    $user_block = $CI->common->getData('user_members',array('member_id'=>$member_id,'user_id'=>$user_id),array('single'));

    if(!empty($user_block)){
      return 1;
    }else{
     return 0;
    }
}

function category_name($id)
{
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');
    $category_name = $CI->common->getData('category',array('id'=>$id,'status'=>0),array('single'));

    if(!empty($category_name)){
      return $category_name['cat_name'];
    }else{
      return false;
    }
}

function legacy_title_name($id)
{
  $CI = &get_instance();
    $CI->load->database();
    $CI->load->model('common');
    $title_name = $CI->common->getData('legacy',array('id'=>$id),array('single'));

    if(!empty($title_name)){
     return $title_name['title'];
    }
    else{
      return false;
    }
}