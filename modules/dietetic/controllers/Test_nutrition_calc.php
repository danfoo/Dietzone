<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Nutrition Calculation - Debug
 */
class Test_nutrition_calc extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_foods_model');
    }

    public function index()
    {
        $data['foods'] = $this->dietetic_foods_model->get_active();
        $data['title'] = 'Test Calcul Nutrition';

        $this->load->view('admin/test_nutrition_calc', $data);
    }
}
