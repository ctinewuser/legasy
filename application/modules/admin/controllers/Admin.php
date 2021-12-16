<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends Admin_Controller {
    public function __construct() {
        parent::__construct();
        $admin = $this->session->userdata('admin');
        $this->load->helper('common');
        $this->load->helper('file');
        $this->id = $admin['id'];
        $admin_id = $admin['id'];
    }
    public function approve_user($id) {
        $d = $this->common->updateData('user', array('status' => 1), array('id' => $id));
        $this->session->set_flashdata('success', 'Approved.');
        redirect(base_url('admin/userList'));
    }
    public function disapprove_user($id) {
        $d = $this->common->updateData('user', array('status' => 0), array('id' => $id));
        $this->session->set_flashdata('error', ' Not Approved.');
        redirect(base_url('admin/userList'));
    }
    public function disapprove_artist($id) {
        $d = $this->common->updateData('user', array('status' => 0), array('id' => $id));
        $this->session->set_flashdata('error', ' Not Approved.');
        redirect(base_url('admin/artistList'));
    }
    public function approve_artist($id) {
        $d = $this->common->updateData('user', array('status' => 1), array('id' => $id));
        $this->session->set_flashdata('success', 'Approved.');
        redirect(base_url('admin/artistList'));
    }
    //Genre List Acceptance Function
    public function disapprove_cat($id) {
        $d = $this->common->updateData('genre_category', array('status' => 1), array('genre_id' => $id));
        $this->session->set_flashdata('error', 'Not Activate.');
        redirect(base_url('admin/catList'));
    }
    public function approve_cat($id) {
        $d = $this->common->updateData('genre_category', array('status' => 0), array('genre_id' => $id));
        $this->session->set_flashdata('success', ' Activate.');
        redirect(base_url('admin/catList'));
    }
    ///End of the Function
    public function delete_user() {
        $id = $this->uri->segment(3);
        $type = $this->uri->segment(4);
        $data = $this->common->getData('user', array('id' => $id), array('single'));
        if ($data) {
            $result = $this->common->deleteData('user', array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'User deleted successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            if ($type == 0) {
                redirect(base_url('admin/userList'), 'refresh');
            } else {
                redirect(base_url('admin/artistList'), 'refresh');
            }
        }
    }
    //////////Upload Bulk songs //////////
    public function uploadfolder() {
        $this->adminHtml('Upload Songs', 'uploadsongs-list', $data);
    }
    public function songsList() {
        $data['songs'] = $this->common->getData('songs', array(), array());
        $this->adminHtml('Songs List', 'songs-list', $data);
    }
    public function uploadfolder_songs() {
        if (!empty($_FILES['file']['name'][0])) {
            $filesCount = count($_FILES['file']['name']);
            $size = 0;
            for ($i = 0;$i < $filesCount;$i++) {
                $_FILES['image']['name'] = $_FILES['file']['name'][$i];
                $_FILES['image']['type'] = $_FILES['file']['type'][$i];
                $_FILES['image']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
                $_FILES['image']['error'] = $_FILES['file']['error'][$i];
                $_FILES['image']['size'] = $_FILES['file']['size'][$i];
                $image = $this->common->do_upload('image', './assets/songs/');
                if (isset($image['upload_data'])) {
                    $images = $image['upload_data']['file_name'];
                    $img[] = $image['upload_data']['file_name'];
                    $result = $this->common->insertData('songs', array('song_name' => $_FILES['image']['name'], 'song_upload_name' => $images));
                }
            }
            if (!empty($img)) {
                $msg = "1";
            }
        } else {
            $msg = "0";
        }
        echo $msg;
    }
    /////End/////
    public function userList() {
        $data['user'] = $this->common->getData('user', array('user_type' => 0), array());
        $this->adminHtml('Fans List', 'user-list', $data);
    }
    public function artistList() {
        $data['user'] = $this->common->getData('user', array('user_type' => 1), array());
        $this->adminHtml('Artist List', 'artist-list', $data);
    }
    public function makeArtist() {
        $id = $this->uri->segment(3);
        $m = $this->common->updateData('user', array('user_type' => 1), array('id' => $id));
        $this->session->set_flashdata('success', 'Now! Fan Become an Artist .');
        redirect(base_url('admin/artistList'));
    }
    public function credit_amount() {
        $data['amount'] = $this->common->getData('credit_amount', array());
        $this->adminHtml('Payable Credits', 'credit-amount-list', $data);
    }
    public function catList() {
        $data['cat'] = $this->common->getData('genre_category', array());
        $this->adminHtml('Genre List', 'cat-list', $data);
    }
    public function feedList() {
        $data['feed'] = $this->common->getData('post', array());
        $this->adminHtml('Feed List', 'feed-list', $data);
    }
    public function show_pinboard() {
        $this->db->select('PB.*');
        $this->db->from('pinboard as PB');
        $this->db->group_by('PB.user_id');
        $query = $this->db->get();
        $pin_list = $query->result_array();
        $data['pin'] = $pin_list;
        $this->adminHtml('Pin Board List', 'pinboard-list', $data);
    }
    public function show_concert() {
        $data['concert'] = $this->common->getData('concerts', array());
        $this->adminHtml('Concert List', 'concert-list', $data);
    }
    public function show_donaterlist() {
        $data['donate'] = $this->common->getData('donate_bits', array());
        $this->adminHtml('Donaters List', 'donaters_list', $data);
    }
    public function goal_list() {
        $data['goal'] = $this->common->getData('goals', array());
        $this->adminHtml('Credit Goal List', 'goal-list', $data);
    }
    public function credit_list() {
        $this->db->select('C.*,SUM(amount) as amount');
        $this->db->from('credits as C');
        $this->db->group_by('C.refer_id');
        $query = $this->db->get();
        $credit_list = $query->result_array();
        $data['credit'] = $credit_list;
        $this->adminHtml('Credit List', 'credit-list', $data);
    }
    public function edit_amount() {
        $id = $this->uri->segment(3);
        $this->form_validation->set_rules('amount', 'credit amount', 'required');
        if ($this->form_validation->run() == false) {
            $data['amount'] = $this->common->getData('credit_amount', array('id' => $id), array('single'));
            $this->adminHtml('Update Amount', 'add-amount', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('id');
            unset($_POST["id"]);
            $result = $this->common->updateData('credit_amount', $_POST, array('id' => $id));
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/credit_amount'), 'refresh');
        }
    }
    public function edit_user() {
        $user_id = $this->uri->segment(3);
        $this->form_validation->set_rules('full_name', 'full_name', 'required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number', 'required');
        $data['user'] = $this->common->getData('user', array('id' => $user_id), array('single'));
        if ($this->form_validation->run() == false) {
            $data['user'] = $this->common->getData('user', array('id' => $user_id), array('single'));
            $this->adminHtml('Update User Details', 'add-user', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('id');
            unset($_POST["id"]);
            if ($_POST['password']) {
                $_POST['password'] = md5($_POST['password']);
            } else {
                $_POST['password'] = $data['user']['password'];
            }
            $result = $this->common->updateData('user', $_POST, array('id' => $user_id));
            if ($result) {
                $this->session->set_flashdata('success', 'Updated successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/userList'), 'refresh');
        }
    }
    public function edit_artist() {
        $user_id = $this->uri->segment(3);
        $this->form_validation->set_rules('full_name', 'full_name', 'required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number', 'required');
        $data['user'] = $this->common->getData('user', array('id' => $user_id), array('single'));
        if ($this->form_validation->run() == false) {
            $data['user'] = $this->common->getData('user', array('id' => $user_id), array('single'));
            $this->adminHtml('Update Artist Details', 'add-artist', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('id');
            unset($_POST["id"]);
            if ($_POST['password']) {
                $_POST['password'] = md5($_POST['password']);
            } else {
                $_POST['password'] = $data['user']['password'];
            }
            $result = $this->common->updateData('user', $_POST, array('id' => $user_id));
            if ($result) {
                $this->session->set_flashdata('success', 'Updated successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/artistList'), 'refresh');
        }
    }
    public function edit_concert() {
        $concert_id = $this->uri->segment(3);
        $this->form_validation->set_rules('concert_title', 'title', 'required');
        $this->form_validation->set_rules('concert_venue', 'concert venue', 'required');
        if ($this->form_validation->run() == false) {
            $data['concert'] = $this->common->getData('concerts', array('concert_id' => $concert_id), array('single'));
            $this->adminHtml('Update concert', 'editconcerts', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('concert_id');
            unset($_POST["concert_id"]);
            if (!empty($_POST['concert_date'])) {
                $_POST['concert_date'] = date('Y-m-d', strtotime($_POST['concert_date']));
            } else {
                $_POST['concert_date'] = date('Y-m-d');
            }
            $result = $this->common->updateData('concerts', $_POST, array('concert_id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Updated successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/show_concert'), 'refresh');
        }
    }
    public function contactUs() {
        $contact_id = '2';
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('address', 'address', 'required');
        if ($this->form_validation->run() == false) {
            $data['contact'] = $this->common->getData('contact_about', array('id' => $contact_id), array('single'));
            $this->adminHtml('Contact Us', 'contactus-list', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('id');
            $result = $this->common->updateData('contact_about', $_POST, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Updated successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/contactUs'), 'refresh');
        }
    }
    public function checkout() {
        $data['check'] = $this->common->getData('redeem_checkout', array());
        $this->adminHtml('Redeem Check Out', 'checkout-page', $data);
    }
    public function show_bitpack() {
        $data['bit'] = $this->common->getData('bit_pack', array());
        $this->adminHtml('Bit Pack List', 'bitpack-list', $data);
    }
    public function bit_purchased_user() {
        $data['buyed'] = $this->common->getData('bitpack_users', array());
        $this->adminHtml('Bit Purchased User List', 'bitpurchased-user', $data);
    }
    public function show_reels() {
        $data['reel'] = $this->common->getData('reels', array());
        $this->adminHtml('Reels List', 'reel-list', $data);
    }
    public function view_reels() {
        $data['video'] = $this->common->getData('reels', array('id' => $id), array('single'));
        $this->adminHtml('Reels', 'reels-detail', $data);
    }
    public function edit_bitpack() {
        $id = $this->uri->segment(3);
        $this->form_validation->set_rules('bit_name', 'bit_name', 'required');
        $this->form_validation->set_rules('amount_in_bit', 'amount_in_bit', 'required');
        $this->form_validation->set_rules('amount_in_euro', 'amount_in_euro', 'required');
        if ($this->form_validation->run() == false) {
            $data['bit'] = $this->common->getData('bit_pack', array('id' => $id), array('single'));
            $this->adminHtml('Update BitPacks', 'add-bitpack', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('id');
            unset($_POST["id"]);
            if ($_POST['amount_in_bit']) {
                $_POST['bit_amount_d'] = $_POST['amount_in_bit'] . " bits";
            }
            if ($_POST['amount_in_euro']) {
                $_POST['euro_amount_d'] = $_POST['amount_in_euro'] . " euro";
            }
            $result = $this->common->updateData('bit_pack', $_POST, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Updated successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/show_bitpack'), 'refresh');
        }
    }
    public function add_bitpack() {
        $this->form_validation->set_rules('bit_name', 'Bit Pack', 'required');
        $this->form_validation->set_rules('amount_in_bit', 'Bit Amount', 'required');
        $this->form_validation->set_rules('amount_in_euro', 'Euro Amount', 'required');
        if ($this->form_validation->run() == false) {
            $this->adminHtml('Add Bit Pack', 'add-bitpack', $data);
        } else {
            unset($_POST["submit"]);
            if ($_POST['amount_in_bit']) {
                $_POST['bit_amount_d'] = $_POST['amount_in_bit'] . " bits";
            }
            if ($_POST['amount_in_euro']) {
                $_POST['euro_amount_d'] = $_POST['amount_in_euro'] . " euro";
            }
            $post = $this->common->getField('bit_pack', $_POST);
            $result = $this->common->insertData('bit_pack', $post);
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data added successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/show_bitpack'), 'refresh');
        }
    }
    public function add_concert() {
        $this->form_validation->set_rules('concert_title', 'concert_title', 'required');
        $this->form_validation->set_rules('concert_date', 'concert_date', 'required');
        $this->form_validation->set_rules('concert_time', 'concert_time', 'required');
        $this->form_validation->set_rules('concert_venue', 'concert_venue', 'required');
        if ($this->form_validation->run() == false) {
            $this->adminHtml('Add Concert', 'editconcerts', $data);
        } else {
            unset($_POST["submit"]);
            if (!empty($_POST['concert_date'])) {
                $_POST['concert_date'] = date('Y-m-d', strtotime($_POST['concert_date']));
            } else {
                $_POST['concert_date'] = date('Y-m-d');
            }
            if (!empty($_POST['concert_time'])) {
                $_POST['concert_time'] = date('H:i:s', strtotime($_POST['concert_time']));
            } else {
                $_POST['concert_time'] = date('H:i:s');
            }
            $post = $this->common->getField('concerts', $_POST);
            $result = $this->common->insertData('concerts', $post);
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data added successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/show_concert'), 'refresh');
        }
    }
    public function show_postlist() {
        $id = $this->uri->segment(3);
        $this->db->select('PB.*,P.*');
        $this->db->from('pinboard as PB');
        $this->db->join('post as P', 'P.id = PB.post_id');
        $this->db->where('PB.user_id', $id);
        $this->db->order_by('P.id', 'desc');
        $query = $this->db->get();
        $post_list = $query->result_array();
        $data['postlist'] = $post_list;
        $this->adminHtml('Post List', 'post-list', $data);
    }
    public function delete_concert() {
        $id = $this->input->post('id');
        $data['concert'] = $this->common->deleteData('concerts', array('concert_id' => $id));
        if ($data) {
            echo '1';
        } else {
            echo '0';
        }
    }
    public function delete_bitpack() {
        $id = $this->input->post('id');
        $data['bit'] = $this->common->deleteData('bit_pack', array('id' => $id));
        if ($data) {
            echo '1';
        } else {
            echo '0';
        }
    }
    public function delete_reelist() {
        $id = $this->input->post('id');
        $data['reel'] = $this->common->deleteData('reels', array('id' => $id));
        if ($data) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function delete_songlist()
    {
        $id = $this->input->post('id');
        $data['reel'] = $this->common->deleteData('songs', array('id' => $id));
        if ($data) {
            echo '1';
        } else {
            echo '0';
        }
    }
    public function delete_gencategory() {
        $id = $this->input->post('id');
        $data['cat'] = $this->common->deleteData('genre_category', array('genre_id' => $id));
        if ($data) {
            echo '1';
        } else {
            echo '0';
        }
    }
    public function delete_feed() {
        $id = $this->uri->segment(3);
        $data = $this->common->getData('post', array('id' => $id), array('single'));
        if ($data) {
            $result = $this->common->deleteData('post', array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Feed deleted successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/feedList'), 'refresh');
        }
    }
    public function showImage() {
        $post_id = $this->uri->segment(3);
        $data['img'] = $this->common->getData('post_image', array('post_id' => $post_id), array('single'));
        $this->adminHtml('Image', 'show-image', $data);
    }
    public function termServices() {
        $this->form_validation->set_rules('editor', 'Info', 'required');
        if ($this->form_validation->run() == false) {
            $data['terms'] = $this->common->getData('contact_about', array('id' => '3'), array('single'));
            $this->adminHtml('Update Terms and services', 'edit-terms', $data);
        } else {
            $data['data'] = $this->input->post('editor');
            $id = $this->input->post('id');
            $result = $this->common->updateData('contact_about', $data, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Terms and Conditions update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/termServices'), 'refresh');
        }
    }
    public function privacyPolicy() {
        $this->form_validation->set_rules('editor', 'Info', 'required');
        if ($this->form_validation->run() == false) {
            $data['privacy'] = $this->common->getData('contact_about', array('id' => '4'), array('single'));
            $this->adminHtml('Update Terms and services', 'editprivacypolicy', $data);
        } else {
            $data['data'] = $this->input->post('editor');
            $id = $this->input->post('id');
            $result = $this->common->updateData('contact_about', $data, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Privacy Policy update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/privacyPolicy'), 'refresh');
        }
    }
    public function aboutUs_page() {
        $this->form_validation->set_rules('editor', 'Info', 'required');
        if ($this->form_validation->run() == false) {
            $data['about'] = $this->common->getData('contact_about', array('id' => '1'), array('single'));
            $this->adminHtml('About Us', 'aboutus-list', $data);
        } else {
            $data['data'] = $this->input->post('editor');
            $id = $this->input->post('id');
            $result = $this->common->updateData('contact_about', $data, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'About Us update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/aboutUs_page'), 'refresh');
        }
    }
    public function edit_genrecategory() {
        $genre_id = $this->uri->segment(3);
        $this->form_validation->set_rules('genre_name', 'genre name', 'required');
        if ($this->form_validation->run() == false) {
            $data['cat'] = $this->common->getData('genre_category', array('genre_id' => $genre_id), array('single'));
            $this->adminHtml('Update Genre', 'add-genrecat', $data);
        } else {
            unset($_POST["submit"]);
            $id = $this->input->post('genre_id');
            unset($_POST["genre_id"]);
            $result = $this->common->updateData('genre_category', $_POST, array('genre_id' => $id));
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/catList'), 'refresh');
        }
    }
    ///////
    public function add_genrecat() {
        $this->form_validation->set_rules('genre_name', 'genre name', 'required');
        if ($this->form_validation->run() == false) {
            $this->adminHtml('Add Genre', 'add-genrecat', $data);
        } else {
            unset($_POST["submit"]);
            $post = $this->common->getField('genre_category', $_POST);
            $result = $this->common->insertData('genre_category', $post);
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data added successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/catList'), 'refresh');
        }
    }
    public function add_user() {
        $this->form_validation->set_rules('full_name', 'User name', 'required');
        $this->form_validation->set_rules('date_of_birth', 'Date Of Birth', 'required');
        $this->form_validation->set_rules('country_code', 'Country Code', 'required');
        $this->form_validation->set_rules('mobile_number', 'Mobile number', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        if ($this->form_validation->run() == false) {
            $this->adminHtml('Add User', 'add-user', $data);
        } else {
            unset($_POST["submit"]);
            $_POST['password'] = md5($_POST['password']);
            if (!empty($_POST['date_of_birth'])) {
                $_POST['date_of_birth'] = date('Y-m-d', strtotime($_POST['date_of_birth']));
            } else {
                $_POST['concert_date'] = date('Y-m-d');
            }
            $post = $this->common->getField('user', $_POST);
            $result = $this->common->insertData('user', $post);
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data added successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/userList'), 'refresh');
        }
    }
    public function add_artist() {
        $this->form_validation->set_rules('artist_name', 'Artist name', 'required');
        $this->form_validation->set_rules('date_of_birth', 'Date Of Birth', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('country_code', 'Country Code', 'required');
        $this->form_validation->set_rules('mobile_number', 'Mobile number', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('genre_cat', 'Genre Categroy', 'required');
        if ($this->form_validation->run() == false) {
            $this->adminHtml('Add Artist', 'add-artist', $data);
        } else {
            unset($_POST["submit"]);
            $_POST['user_type'] = 1;
            $_POST['password'] = md5($_POST['password']);
            if (!empty($_POST['date_of_birth'])) {
                $_POST['date_of_birth'] = date('Y-m-d', strtotime($_POST['date_of_birth']));
            } else {
                $_POST['concert_date'] = date('Y-m-d');
            }
            $post = $this->common->getField('user', $_POST);
            $result = $this->common->insertData('user', $post);
            if ($result) {
                $a = $this->session->set_flashdata('success', 'Data added successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/artistList'), 'refresh');
        }
    }
    public function admineditprofile() {
        $admin_id = $this->uri->segment(3);
        $session = $this->session->userdata('admin');
        $data['admin'] = $this->common->getData('admin', array('id' => $admin_id), array('single'));
        $this->adminHtml('profile', 'profile-edit', $data);
    }
    public function usereditprofile() {
        $user_id = $this->uri->segment(3);
        $session = $this->session->userdata('user');
        $data['user'] = $this->common->getData('user', array('id' => $user_id), array('single'));
        $this->adminHtml('profile', 'user-edit', $data);
    }
    public function product_quantity() {
        $product_id = $this->uri->segment(3);
        $data['product'] = $this->common->getData('product_quantity', array('product_id' => $product_id));
        $this->adminHtml('Product Quantity List', 'product-quantity-list', $data);
    }
    public function total_revence() {
        $user = $this->common->getData('user', array(), array('count'));
        $follower = $this->common->getData('follower', array(), array('count'));
        $friends = $this->common->getData('friends', array(), array('count'));
        echo json_encode(array('user' => $user, 'follower' => $follower, 'friends' => $friends), true);
    }
    public function send_notification($tokens, $message) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array('registration_ids' => $tokens, "data" => $message);
        $headers = array('Authorization:key = AAAAA_p0eoY:APA91bFA-fYFpwSsr_C80kzYPxVYR3nBB6fgfWD6VvvmaMipIA-6S7Tmfd16s5CUkZ8p8f3ik9_NWtnM1CSrkxxdxa27vfm-82JT4trstFfdDOU9VMKHGZdGi6tCdoOz0S9ymHVmgywQ', 'Content-Type: application/json');
        return $this->curl($url, $headers, $fields);
    }
    public function send_notification_ios($tokens, $message, $title, $type) {
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAAli86Xtc:APA91bFq8_Nk446lfNtSgIrYt_6HB0Ea62wZoZmkM5LmqIjTPVy0NylOkwPd-QmKeGXsEqRbRUv3fejo7KQe5YE8hX6ShdGNdtDkAI6xl6NF0p85zWdFU8_xut1HV7QrBeNiyeKCvmrR';
        /*	$title = "new notification";*/
        $body = "$message";
        $notification = array('title' => $title, 'text' => $body, 'type' => $type, 'sound' => 'default', 'badge' => '1');
        $fields = array('to' => $tokens, 'notification' => $notification, 'priority' => 'high');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        return $this->curl($url, $headers, $fields);
    }
    public function send_notification_ios1($tokens, $message, $title, $type, $typee) {
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAAli86Xtc:APA91bFq8_Nk446lfNtSgIrYt_6HB0Ea62wZoZmkM5LmqIjTPVy0NylOkwPd-QmKeGXsEqRbRUv3fejo7KQe5YE8hX6ShdGNdtDkAI6xl6NF0p85zWdFU8_xut1HV7QrBeNiyeKCvmrR';
        $body = "$message";
        $data = $typee;
        $notification = array('title' => $title, 'text' => $body, 'type' => $type, 'sound' => 'default', 'badge' => '1');
        $fields = array('to' => $tokens, 'data' => $data, 'notification' => $notification, 'priority' => 'high');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        return $this->curl($url, $headers, $fields);
    }
    public function Contact_us() {
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('address', 'address', 'required');
        $this->form_validation->set_rules('phone_no', 'phone_no', 'required');
        if ($this->form_validation->run() == false) {
            $data['cat'] = $this->common->getData('contact_us', array('id' => 1), array('single'));
            $this->adminHtml('Contact us', 'contact_us', $data);
        } else {
            unset($_POST["submit"]);
            $result = $this->common->updateData('contact_us', $_POST, array('id' => 1));
            if ($result) {
                $this->flashMsg('success', 'contact added successfully');
                redirect(base_url('admin/contact_us'));
            } else {
                $this->flashMsg('danger', 'Some error occured. Please try again');
                redirect(base_url('admin/contact_us'));
            }
        }
    }
    public function home() {
        $data['home1'] = $this->common->getData('home', array('id' => 1), array('single'));
        $data['home2'] = $this->common->getData('home', array('id' => 2), array('single'));
        $data['home3'] = $this->common->getData('home', array('id' => 3), array('single'));
        $data['home4'] = $this->common->getData('home', array('id' => 4), array('single'));
        $data['home5'] = $this->common->getData('home', array('id' => 5), array('single'));
        $data['home6'] = $this->common->getData('home', array('id' => 6), array('single'));
        $this->adminHtml('home', 'home', $data);
    }
    public function about_us() {
        $data['about_us1'] = $this->common->getData('about_us', array('id' => 1), array('single'));
        $data['about_us2'] = $this->common->getData('about_us', array('id' => 2), array('single'));
        $this->adminHtml('About us', 'about_us', $data);
    }
    public function productlist() {
        $data['product'] = $this->common->getData('sell_products', '', array('sort_by' => 'product_id', 'sort_direction' => 'desc'));
        $data['link'] = 'admin/productDetail/';
        $this->adminHtml('Product List', 'product-list', $data);
    }
    public function categorylist($id) {
        $data['cat'] = $this->common->getData('sell_category', array('service_id' => $id), array());
        $this->adminHtml('Category List', 'categorylist', $data);
    }
    public function subcategorylist($id, $sid) {
        $data['cat'] = $this->common->getData('sell_subcategory', array('service_id' => $id, 'category_id' => $sid), array('sort_by' => 'id', 'sort_direction' => 'desc'));
        $this->adminHtml('Sub Category List', 'subcategorylist', $data);
    }
    public function delete_goal() {
        $id = $this->uri->segment(3);
        $data = $this->common->getData('goals', array('id' => $id), array('single'));
        if ($data) {
            $result = $this->common->deleteData('goals', array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Credit goals deleted successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/goal_list'), 'refresh');
        }
    }
    public function dashboard() {
        $data['user'] = $this->common->getData('user', array(), array('count'));
        $data['follower'] = $this->common->getData('follower', array(), array('count'));
        $data['concerts'] = $this->common->getData('goals', array(), array('count'));
        $data['goals'] = $this->common->getData('goals', array(), array('count'));
        $this->adminHtml('Dashboard', 'admin/dashboard', $data);
    }
    public function adminList() {
        $data['user'] = $this->common->getData('admin', array());
        $this->adminHtml('Admin List', 'admin-list', $data);
    }
    public function dissuspend_admin($id) {
        $d = $this->common->updateData('admin', array('suspend' => 0), array('id' => $id));
        //$data['user'] = $this->common->getData('provider',array('approve'=>'1'),array(''));
        redirect(base_url('admin/adminList'));
        //$this->adminHtml('provider List','user-list',$data);
        
    }
    public function suspend_admin($id) {
        $d = $this->common->updateData('admin', array('suspend' => 1), array('id' => $id));
        //$data['user'] = $this->common->getData('provider',array('approve'=>'1'),array(''));
        redirect(base_url('admin/adminList'));
        //$this->adminHtml('provider List','user-list',$data);
        
    }
    public function send_single_mail($user_id) {
        $this->form_validation->set_rules('editor', 'Info', 'required');
        if ($this->form_validation->run() == false) {
            $data['user_id'] = $user_id;
            $this->adminHtml('Add Message', 'send-single-email', $data);
        } else {
            $user_id = $this->input->post('user_id');
            $where = "id = '" . $user_id . "'";
            $user_result = $this->common->getData('user', $where, array('single'));
            $to_email = $user_result['email'];
            $message = $this->input->post('editor');
            $template = $this->load->view('template/broadcast-email', array('message' => $message), true);
            $send_mail = $this->common->sendMail($to_email, "kutz Updates", $template);
            if ($send_mail) {
                $this->session->set_flashdata('success', 'Data added successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/send_single_mail/' . $user_id));
        }
    }
    public function profile($id) {
        $data['user'] = $this->common->getData('user', array('id' => $id), array('single'));
        $this->adminHtml('User Profile', 'user-detail', $data);
    }
    public function edit_term_services() {
        $this->form_validation->set_rules('editor', 'Info', 'required');
        if ($this->form_validation->run() == false) {
            $data['terms'] = $this->common->getData('contact_about', array('id' => '3'), array('single'));
            $this->adminHtml('Update Terms and services', 'edit-terms', $data);
        } else {
            $data['data'] = $this->input->post('editor');
            $id = $this->input->post('id');
            $result = $this->common->updateData('contact_about', $data, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Terms and Conditions update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/edit_term_services'), 'refresh');
        }
    }
    public function edit_privacy_policy() {
        $this->form_validation->set_rules('editor', 'Info', 'required');
        if ($this->form_validation->run() == false) {
            $data['privacy'] = $this->common->getData('contact_about', array('id' => '4'), array('single'));
            $this->adminHtml('Update Terms and services', 'edit_privacy_policy', $data);
        } else {
            $data['data'] = $this->input->post('editor');
            $id = $this->input->post('id');
            $result = $this->common->updateData('contact_about', $data, array('id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Privacy Policy update successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/edit_privacy_policy'), 'refresh');
        }
    }
    public function send_request() {
        sleep(2);
        if ($this->input->post('send_userid')) {
            $data = array('sender_id' => $this->input->post('send_userid'), 'receiver_id' => $this->input->post('receiver_userid'));
            $this->common->Insert_chat_request($data);
        }
    }
    function load_notification() {
        sleep(2);
        if ($this->input->post('action')) {
            $data = $this->common->Fetch_notification_data($this->session->userdata('id'));
            $output = array();
            if ($data->num_rows() > 0) {
                foreach ($data->result() as $row) {
                    $userdata = $this->common->Get_user_data($row->sender_id);
                    $output[] = array('user_id' => $row->sender_id, 'first_name' => $userdata['first_name'], 'last_name' => $userdata['last_name'], 'profile_picture' => $userdata['profile_picture'], 'chat_request_id' => $row->chat_request_id);
                }
            }
            echo json_encode($output);
        }
    }
    public function send_chat() {
        $session = $this->session->userdata('admin');
        if ($this->input->post('receiver_id')) {
            $data = array('sender_id' => 0, 'sender_type' => 'admin', 'receiver_id' => $this->input->post('receiver_id'), 'receiver_type' => $this->input->post('receiver_type'), 'chat_messages_text' => $this->input->post('chat_message'), 'chat_messages_status' => 'no', 'chat_messages_datetime' => date('Y-m-d H:i:s'));
            $this->common->Insert_chat_message($data);
        }
    }
    public function load_chat_data() {
        $session = $this->session->userdata('admin');
        if ($this->input->post('receiver_id')) {
            $receiver_id = $this->input->post('receiver_id');
            $receiver_type = $this->input->post('receiver_type');
            $session = $this->session->userdata('admin');
            $sender_id = 0;
            $sender_type = 'admin';
            if ($this->input->post('update_data') == 'yes') {
                $this->common->Update_chat_message_status($sender_id);
            }
            $chat_data = $this->common->Fetch_chat_data($sender_id, $sender_type, $receiver_id, $receiver_type);
            if ($chat_data->num_rows() > 0) {
                foreach ($chat_data->result() as $row) {
                    $message_direction = '';
                    if ($row->sender_id == $sender_id && $row->sender_type == $sender_type) {
                        $message_direction = 'right';
                    } else {
                        $message_direction = 'left';
                    }
                    $date = date('D M Y H:i', strtotime($row->chat_messages_datetime));
                    $output[] = array('chat_messages_text' => $row->chat_messages_text, 'chat_messages_datetime' => $date, 'message_direction' => $message_direction);
                }
            }
            echo json_encode($output);
        }
    }
    public function check_chat_notification() {
        if ($this->input->post('user_id_array')) {
            $session = $this->session->userdata('admin');
            $receiver_id = $session['id'];
            $this->common->Update_login_data();
            $user_id_array = explode(",", $this->input->post('user_id_array'));
            $output = array();
            foreach ($user_id_array as $sender_id) {
                if ($sender_id != '') {
                    $status = "offline";
                    $last_activity = $this->common->User_last_activity($sender_id);
                    $is_type = '';
                    if ($last_activity != '') {
                        $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                        $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                        if ($last_activity > $current_timestamp) {
                            $status = 'online';
                            $is_type = $this->common->Check_type_notification($sender_id, $receiver_id, $current_timestamp);
                        }
                    }
                    $output[] = array('user_id' => $sender_id, 'total_notification' => $this->common->Count_chat_notification($sender_id, $receiver_id), 'status' => $status, 'is_type' => $is_type);
                }
            }
            echo json_encode($output);
        }
    }
    public function import() {
        $data = array();
        $memData = array();
        // If import request is submitted
        if ($this->input->post('importSubmit')) {
            // Form field validation rules
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_checks');
            // Validate submitted form data
            if ($this->form_validation->run() == true) {
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
                // If file uploaded
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    // Load CSV reader library
                    $this->load->library('CSVReader');
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
                    // Insert/update CSV data into database
                    if (!empty($csvData)) {
                        foreach ($csvData as $row) {
                            $rowCount++;
                            // Prepare data for DB insertion
                            $memData = array('vendor_id' => 0, 'category' => $row['category'], 'name' => $row['name'], 'currency' => $row['currency'], 'regular_price' => $row['regular_price'], 'sale_price' => $row['sale_price'], 'product_quantity' => $row['product_quantity'], 'product_description' => $row['product_description'], 'short_desc' => $row['short_desc'], 'long_desc' => $row['long_desc'], 'region' => $row['region'], 'ABV' => $row['ABV'],);
                            // Check whether email already exists in the database
                            $con = array('where' => array('name' => $row['name']), 'returnType' => 'count');
                            $prevCount = $this->common->getRows($con);
                            if ($prevCount > 0) {
                                // Update member data
                                $condition = array('name' => $row['name']);
                                $update = $this->common->update($memData, $condition);
                                if ($update) {
                                    $updateCount++;
                                }
                            } else {
                                // Insert member data
                                $insert = $this->common->insert($memData);
                                if ($insert) {
                                    $insertCount++;
                                }
                            }
                        }
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'Members imported successfully. Total Rows (' . $rowCount . ') | Inserted (' . $insertCount . ') | Updated (' . $updateCount . ') | Not Inserted (' . $notAddCount . ')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                } else {
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            } else {
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('admin/productlist');
    }
    /*
     * Callback function to check file value and type during validation
    */
    public function file_checks($str) {
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != "") {
            $mime = get_mime_by_extension($_FILES['file']['name']);
            $fileAr = explode('.', $_FILES['file']['name']);
            $ext = end($fileAr);
            if (($ext == 'csv') && in_array($mime, $allowed_mime_types)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }
    public function adminprofile() {
        ///chandni 11/09/2020
        $admin_id = $this->uri->segment(3);
        $session = $this->session->userdata('admin');
        $data['admin_id'] = $admin_id;
        $data['admin'] = $this->common->getData('admin', array('id' => $admin_id), array('single'));
        /*  $data['services'] = $this->common->getData('service_offer_subcategory', array(
         'created_by' => $admin_id) , array(''));*/
        $this->adminHtml('profile', 'profile', $data);
    }
    public function edit_admin_profile() {
        ///chandni 11/09/2020
        $session = $this->session->userdata('admin');
        $admin_id = $this->uri->segment(3);
        $this->form_validation->set_rules('first_name', 'first Name', 'required');
        $d = $this->common->getData('admin', array('id' => $admin_id), array('single'));
        if ($this->form_validation->run() == false) {
            $data['admin'] = $this->common->getData('admin', array('id' => $admin_id));
            $this->adminHtml('profile', 'profile-edit', $data);
        } else {
            $image = $d['image'];
            $password = $d['password'];
            if (!empty($_FILES['image'])) {
                $image1 = $this->common->do_upload('image', './assets/userfile/profile/');
                if (isset($image1['upload_data'])) {
                    $image = $image1['upload_data']['file_name'];
                }
            }
            if (!empty($_POST['password'])) {
                $password = md5($_POST['password']);
            }
            $_POST['image'] = $image;
            $_POST['password'] = $password;
            $admin_id = $_POST['admin_id'];
            $post = $this->common->getField('admin', $_POST);
            $result = $this->common->updateData('admin', $post, array('id' => $admin_id));
            if ($result) {
                $this->session->set_flashdata('success', 'Profile Updated Successfully.');
                redirect(base_url('admin/adminprofile/') . $admin_id);
            } else {
                $this->session->set_flashdata('danger', 'Profile Not Updated.Please Try Again.');
                redirect(base_url('admin/admineditprofile/') . $admin_id);
            }
        }
    }
    public function add_admin_profile() {
        ///chandni 11/09/2020
        $this->form_validation->set_rules('first_name', 'first_name', 'required');
        $this->form_validation->set_rules('last_name', 'last_name', 'required');
        /* $this->form_validation->set_rules('email','Email', 'required|trim|callback_validate_adminemail');*/
        $this->form_validation->set_rules('password', 'password', 'required');
        if ($this->form_validation->run() == false) {
            $data['allroles'] = $this->common->getData('permission_roles', array(''), array(''));
            $this->adminHtml('profile', 'profile-edit', $data);
        } else {
            if (!empty($_FILES['image'])) {
                $image1 = $this->common->do_upload('image', './assets/userfile/profile/');
                if (isset($image1['upload_data'])) {
                    $image = $image1['upload_data']['file_name'];
                }
            }
            $_POST['image'] = $image;
            $_POST['password'] = md5($_POST['password']);
            if (!empty($_POST['role_id'])) {
                $role_id = $_POST['role_id'];
            }
            $_POST['created_at'] = date('Y-m-d');
            $post = $this->common->getField('admin', $_POST);
            $result = $this->common->insertData('admin', $post);
            $admin_id = $this->db->insert_id();
            if ($result) {
                $sizeof = sizeof($role_id);
                for ($i = 0;$i < $sizeof;$i++) {
                    $datadetail = array('role_id' => $role_id[$i], 'admin_id' => $admin_id, 'created_at' => date('Y-m-d'), 'created_time' => date('H:i:s'));
                    $result1 = $this->common->insertData('user_permission_map', $datadetail);
                }
                $this->session->set_flashdata('success', 'Profile Added Successfully.');
                redirect(base_url('admin/adminprofile/') . $admin_id);
            } else {
                $this->session->set_flashdata('danger', 'Profile Not Added.Please Try Again.');
                redirect(base_url('admin/admineditprofile/') . $admin_id);
            }
        }
    }
    ///////Report User List  Function created by Naincy 17/08/21/////
    public function post_report() {
        $data['report'] = $this->common->getData('report_user', array('type' => 0));
        $this->adminHtml('Post Report', 'reportusers-list', $data);
    }
    public function artist_report() {
        $data['report'] = $this->common->getData('report_artist', array());
        $this->adminHtml('Artist Report ', 'reportartist-list', $data);
    }
    public function block_report() {
        $data['report'] = $this->common->getData('report_user', array());
        $this->adminHtml('Block User List', 'blockusers-list', $data);
    }
    public function unblock_report_user() {
        $id = $this->uri->segment(3);
        $artist_report = $this->uri->segment(4);
        if (!empty($artist_report)) {
            $result = $this->common->deleteData('report_artist', array('id' => $artist_report));
        }
        $result = $this->common->deleteData('report_user', array('id' => $id));
        $this->session->set_flashdata('success', ' User UnBlocked.');
        redirect(base_url('admin/block_report'));
    }
    public function block_user() {
        $id = $this->uri->segment(3);
        $d = $this->common->updateData('report_user', array('type' => 1), array('id' => $id));
        $this->session->set_flashdata('error', ' User Blocked.');
        redirect(base_url('admin/post_report'));
    }
    public function block_artist() {
        $id = $this->uri->segment(3);
        $d = $this->common->getData('report_artist', array('id' => $id), array('single'));
        $arr = array("user_id" => $d['user_id'], "block_id" => $d['block_id'], "type" => 1, "artist_report" => $d['id']);
        $update = $this->common->updateData('report_artist', array('status' => 1), array('id' => $id));
        $insert = $this->common->insertData('report_user', $arr);
        redirect(base_url('admin/artist_report'));
    }
    public function graph_chart() {
        $data = array('month' => array(0 => '04', 1 => '05', 2 => '06', 3 => '07',), 'customer' => array(0 => 212, 1 => 6475, 2 => 74, 3 => 0,), 'year' => array(0 => '2021', 1 => '2021', 2 => '2021', 3 => '2021',), 'driver_price' => array(0 => 8, 1 => 4483, 2 => 30, 3 => 0,),);
        echo json_encode($data, TRUE);
    }
    public function deleteRoles() {
        $id = $this->uri->segment(3);
        $data = $this->common->getData('permission_roles', array('role_id' => $id), array('single'));
        if ($data) {
            $result = $this->common->deleteData('permission_roles', array('role_id' => $id));
            $result1 = $this->common->deleteData('permission_role_map', array('role_id' => $id));
            if ($result) {
                $this->session->set_flashdata('success', 'Roles deleted successfully');
            } else {
                $this->session->set_flashdata('danger', 'Some Error occured.');
            }
            redirect(base_url('admin/roleList'), 'refresh');
        }
    }
    public function permission_denied() {
        $this->adminHtml('Permission', 'permission_denied', $data);
    }
}
