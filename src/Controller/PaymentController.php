<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    use Cake\Datasource\ConnectionManager;   
    
    class PaymentController extends AppController
    {
        public $interest,$total_amt,$col_amt,$pri_amt;
        public function initialize(): void {
            parent::initialize();
            $this->connection = ConnectionManager::get('default');
            $this->Divisions= $this->loadModel('Divisions');
            $this->Payments= $this->loadModel('Payments');
            $this->Tenures= $this->loadModel('Tenures');
            $this->Arrears= $this->loadModel('Arrears');
            $this->Properties=$this->loadModel('Properties');
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
//            $this->request->allowMethod(['post']);
            
            $crr= $this->request->getQuery('crr');
            $div_code= $this->request->getQuery('division_code');
            $ten_code= $this->request->getQuery('tenure_code');
            
            $pid= $this->Properties->find('all');
            $pid->where(['crr_number'=>$crr,'division_code'=>$div_code,'tenure_code'=>$ten_code]);
            
             $this->loadComponent('RequestHandler');
            $this->RequestHandler->renderAs($this, 'json');
            $this->response->type('application/json');
            
        }
         public function table1() {
            $this->autoRender=false;
//            $this->request->allowMethod(['post']);
            
            $crr= $this->request->getQuery('crr');
            $div_code= $this->request->getQuery('division_code');
            $ten_code= $this->request->getQuery('tenure_code');
            
            echo '<table class="table" id="table"><thead><tr><th>Due Date</th><th>End Date</th><th>Amount</th><th>Interest</th><th>Total</th></tr></thead>';
            
            //Getting Details
            $arrear= $this->Arrears->find('all');
            $arrear->where(['crr_number'=>$crr,'division_code'=>$div_code,'tenure_code'=>$ten_code,'ispaid'=>false]);
            
            foreach($arrear as $key=>$data){
                $this->pri_amt=$data->amount;
                $due_date=$data->due_date;
                echo '<tr>';
                echo '<td>'.date('Y/m/d',strtotime($data->due_date)).'</td>';
                echo '<td>'.date('Y/m/d',strtotime($data->end_date)).'</td>';
                
                //Getting Unpaid ammounts if any
                $stmt = $this->connection->prepare('select * from elrms.arrears where arrears.crr_number=:crr and tenure_code=:ten and division_code=:div and update_amount<0 and due_date=:due');
                $stmt->bindValue('crr',$crr);
                $stmt->bindValue('ten',$ten_code);
                $stmt->bindValue('div',$div_code);
                $stmt->bindValue('due',$due_date);
                $stmt->execute();
                
                $rows = $stmt->fetchAll('assoc');
                $rowCount = $stmt->rowCount();
                if($rowCount==1)
                {
                    foreach($rows as $row)
                    {
                        $this->pri_amt=($row['update_amount']*-1);
                        $due_date=$row['due_date'];
                    }
                }
                echo '<td>'.$this->pri_amt.'</td>';
                
                //getting Interest
                $int= $this->connection
                        ->newQuery()
                        ->select("getinterest(".$this->pri_amt.",6,'".date('Y/m/d',strtotime($due_date))."') as int");
                $data1=$int->execute()->fetchAll('assoc');
                
                foreach($data1[0] as $key){
                    echo '<td>'.$key.'</td>';
                    echo '<td>'.($this->pri_amt+$key).'</td>';
                    $this->interest+=$key;
                    $this->total_amt+=($key+$this->pri_amt);
                }
                $this->col_amt+= $this->pri_amt;
            }
            echo '</tr><tr>
            <th colspan=2 align="right">
                Total
            </th>
            <th>'.$this->col_amt.'</th>
            <th>'.$this->interest.'</th>
            <th>'.$this->total_amt.'</th>
            <input type="hidden" name="total_due" id="total_due" value='.round($this->total_amt,0).'>
            </tr>
            <tr>
                <th colspan=2 align="right">
                    Rounded
                </th>
                <th>'.round($this->col_amt, 0).'</th>
                <th>'.round($this->interest,0).'</th>
                <th>'.round($this->total_amt,0).'</th>
            </tr>';
            echo '</table>';
        }
    }
?>
