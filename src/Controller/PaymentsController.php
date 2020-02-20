<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    
    class PaymentsController extends AppController
    {
        public function initialize(): void {
            parent::initialize();
        }
        
        public function add() {
            $this->set('title','Add Payment');
        }
    }
?>