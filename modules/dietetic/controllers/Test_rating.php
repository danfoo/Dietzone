<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Contrôleur pour la page de diagnostic du système de notation
 * Accessible publiquement (sans authentification requise)
 */
class Test_rating extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Page de test du système de notation
     * Accessible à: /dietetic/test_rating
     */
    public function index()
    {
        $this->load->view('portal/test_rating');
    }
}
