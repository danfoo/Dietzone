<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_view_render extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_recipes_model');
    }

    public function index()
    {
        // Force error display
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        echo "<!-- START TEST VIEW RENDER -->\n";
        echo "<!-- Error reporting enabled -->\n";
        
        // Get a recipe
        $recipe_id = 7; // Yassa
        $data['recipe'] = $this->dietetic_recipes_model->get($recipe_id);
        
        if (!$data['recipe']) {
            echo "<h1>Recipe not found!</h1>";
            return;
        }
        
        $data['title'] = $data['recipe']->name;
        $data['ratings'] = $this->dietetic_recipes_model->get_ratings($recipe_id);
        
        echo "<!-- Recipe loaded: " . $data['recipe']->name . " -->\n";
        echo "<!-- Now loading view... -->\n";
        
        try {
            // Load the view exactly as the controller does
            $this->load->view('admin/recipes/view', $data);
        } catch (Exception $e) {
            echo "<h1>Exception caught!</h1>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
        echo "\n<!-- END TEST VIEW RENDER -->\n";
    }
}
