<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    
    class PaymentsController extends AppController
    {
        public function initialize(): void {
            parent::initialize();
            $this->loadModel(['Payments','Divisions']);
            $this->viewBuilder()->setLayout('niceadmin');
        }
        
        public function add() {
            $division_list= $this->Payments->find('all',['order'=>'Payment_id']);
            $this->set('title','Add Payment');
        }
    }
?>