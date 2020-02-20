<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    
    class PaymentController extends AppController
    {
        public function initialize(): void {
            parent::initialize();
            $this->Divisions= $this->loadModel('Divisions');
            $this->Payments= $this->loadModel('Payments');
            $this->Tenures= $this->loadModel('Tenures');
            $this->viewBuilder()->setLayout('niceadmin');
        }
        
        public function add() {
            //Getting Divisions List
            $division_list= $this->Divisions->find('all',['order'=>'division_code']);
            $this->set('div_list',$division_list);
            
            //Getting Tenures List
            $tenure_list= $this->Tenures->find('all',['order'=>'tenure_code']);
            $this->set('ten_list',$tenure_list);
            
            //Getting Maximum Payment Id
            $pid= $this->Payments->find();
            $pid->select(['pay_id'=>$pid->func()->max('payment_id')]);
            $this->set('id',$pid);
            
            $this->set('title','Add Payment');
        }
        
        public function table1() {
            $pid= $this->Payments->find('');
            $pid->select(['pay_id'=>$pid->func()->max('payment_id')]);
        }
    }
?>
