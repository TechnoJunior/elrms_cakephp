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
            $this->Arrears= $this->loadModel('Arrears');
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
//            if($this->request->is('post')){
//                pr($this->request->getData('remarks'));
//            }
        }


        public function fetchname() {
            $this->autoRender=false;
            //$this->request->allowMethod(['post']);
            
            $crr= $this->request->getQuery('crr');
            $div_code= $this->request->getQuery('division_code');
            $ten_code= $this->request->getQuery('tenure_code');
            
            $pid= $this->Arrears->find('all');
            $pid->where(['crr_number'=>$crr,'division_code'=>$div_code,'tenure_code'=>$ten_code]);
            foreach($pid as $key=>$p)
            {
                echo $p->division_code."<br>";
            }
        }
    }
?>
