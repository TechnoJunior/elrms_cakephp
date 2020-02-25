<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    use Cake\Datasource\ConnectionManager;   
    
    class PaymentController extends AppController
    {
        
        public function initialize(): void {
            parent::initialize();
            $this->connection = ConnectionManager::get('default');
            $this->Divisions= $this->loadModel('Divisions');
            $this->Payments= $this->loadModel('Payments');
            $this->Tenures= $this->loadModel('Tenures');
            $this->Arrears= $this->loadModel('Arrears');
            $this->Properties=$this->loadModel('Properties');
            //Request Handling and encoding JSON
            $this->loadComponent('RequestHandler');
            //Setting Custom Layout
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
            if($this->request->is('post')){
                $this->autoRender=false;
                pr($this->request->getData());
                
            }
        }
        
        public function insert()
        {
            $this->autoRender=false;
            if(!empty($_SERVER['HTTP_CLIENT_IP'])){$ip = $_SERVER['HTTP_CLIENT_IP'];}
            elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];}
            else{$ip = $_SERVER['REMOTE_ADDR'];}
            
            if($this->request->is('post')){
                
            }
        }
    }
?>
