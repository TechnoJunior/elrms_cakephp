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
            $division_list= $this->Divisions->find('all',['order'=>'division_code']);
            $this->set('div_list',$division_list);
            $this->set('title','Add Payment');
        }
    }
?>