<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Widget extends CI_Controller {

    function __construct() {
        parent::__construct();
        define('LANG', $this->Csz_admin_model->getLang());
        $this->lang->load('admin', LANG);
        $this->template->set_template('admin');
        $this->_init();
    }

    public function _init() {
        $row = $this->Csz_admin_model->load_config();
        $pageURL = $this->Csz_admin_model->getCurPages();
        $this->template->set('core_css', $this->Csz_admin_model->coreCss());
        $this->template->set('core_js', $this->Csz_admin_model->coreJs());
        $this->template->set('title', 'Backend System | ' . $row->site_name);
        $this->template->set('meta_tags', $this->Csz_admin_model->coreMetatags('Backend System for CSZ Content Management'));
        $this->template->set('cur_page', $pageURL);
    }

    public function index() {
        admin_helper::is_logged_in($this->session->userdata('admin_email'));
        $this->load->library('pagination');
        $this->csz_referrer->setIndex();

        // Pages variable
        $result_per_page = 20;
        $total_row = $this->Csz_admin_model->countTable('widget_xml');
        $num_link = 10;
        $base_url = BASE_URL . '/admin/widget/';

        // Pageination config
        $this->Csz_admin_model->pageSetting($base_url, $total_row, $result_per_page, $num_link);
        ($this->uri->segment(3)) ? $pagination = ($this->uri->segment(3)) : $pagination = 0;

        //Get users from database
        $this->template->setSub('widget', $this->Csz_admin_model->getIndexData('widget_xml', $result_per_page, $pagination, 'widget_xml_id', 'ASC'));

        //Load the view
        $this->template->loadSub('admin/widget_index');
    }

    public function addWidget() {
        admin_helper::is_logged_in($this->session->userdata('admin_email'));
        //Load the form helper
        $this->load->helper('form');
        //Load the view
        $this->template->loadSub('admin/widget_add');
    }

    public function insert() {
        admin_helper::is_logged_in($this->session->userdata('admin_email'));
        admin_helper::chkVisitor($this->session->userdata('user_admin_id'));
        //Load the form validation library
        $this->load->library('form_validation');
        //Set validation rules
        $this->form_validation->set_rules('widget_name', 'Widget Name', 'required');
        $this->form_validation->set_rules('xml_url', 'Widget XML URL', 'required');
        if ($this->form_validation->run() == FALSE) {
            //Validation failed
            $this->addWidget();
        } else {
            //Validation passed
            //Add the user
            $this->Csz_admin_model->insertWidget();
            //Return to user list
            $this->db->cache_delete_all();
            $this->session->set_flashdata('error_message', '<div class="alert alert-success" role="alert">' . $this->lang->line('success_message_alert') . '</div>');
            redirect($this->csz_referrer->getIndex(), 'refresh');
        }
    }

    public function editWidget() {
        admin_helper::is_logged_in($this->session->userdata('admin_email'));
        //Load the form helper
        $this->load->helper('form');
        if ($this->uri->segment(4)) {
            //Get user details from database
            $this->template->setSub('widget', $this->Csz_model->getValue('*', 'widget_xml', 'widget_xml_id', $this->uri->segment(4), 1));
            //Load the view
            $this->template->loadSub('admin/widget_edit');
        } else {
            redirect($this->csz_referrer->getIndex(), 'refresh');
        }
    }

    public function edited() {
        admin_helper::is_logged_in($this->session->userdata('admin_email'));
        admin_helper::chkVisitor($this->session->userdata('user_admin_id'));
        //Load the form validation library
        $this->load->library('form_validation');
        //Set validation rules
        $this->form_validation->set_rules('widget_name', 'Widget Name', 'required');
        $this->form_validation->set_rules('xml_url', 'Widget XML URL', 'required');
        if ($this->form_validation->run() == FALSE) {
            //Validation failed
            $this->editWidget();
        } else {
            //Validation passed
            if($this->uri->segment(4)){
                //Update the user
                $this->Csz_admin_model->updateWidget($this->uri->segment(4));
                //Return to user list
                $this->db->cache_delete_all();
                $this->session->set_flashdata('error_message', '<div class="alert alert-success" role="alert">' . $this->lang->line('success_message_alert') . '</div>');
            }          
            redirect($this->csz_referrer->getIndex(), 'refresh');
        }
    }

    public function delete() {
        admin_helper::is_logged_in($this->session->userdata('admin_email'));
        admin_helper::chkVisitor($this->session->userdata('user_admin_id'));
        if ($this->uri->segment(4)) {
            //Delete the widget
            $this->Csz_admin_model->removeData('widget_xml', 'widget_xml_id', $this->uri->segment(4));
            $this->db->cache_delete_all();
            $this->session->set_flashdata('error_message', '<div class="alert alert-success" role="alert">' . $this->lang->line('success_message_alert') . '</div>');
        }
        //Return to widget list
        redirect($this->csz_referrer->getIndex(), 'refresh');
    }

}
