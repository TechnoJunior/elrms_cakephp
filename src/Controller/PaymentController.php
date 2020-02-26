<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    use Cake\Datasource\ConnectionManager;   
    
    class PaymentController extends AppController
    {
        //Getting data from form
        private $crr,$div_code,$ten_code;
        private $payment_date,$payment_mode;
        private $tpa,$total_due_amount,$other_amount;
        private $grass_number,$remarks;
        private $ip_address;


        //Variables for Database
        public $interest,$row_amt,$col_amt,$pri_amt,$check;
        private $inte,$amt;
        private $payment_id,$receipt_no,$due_date,$advance_amount;
        
        public function initialize(): void {
            parent::initialize();
            $this->connection = ConnectionManager::get('default');
            
            $this->Divisions= $this->loadModel('Divisions');
            $this->Payments= $this->loadModel('Payments');
            $this->Tenures= $this->loadModel('Tenures');
            $this->Arrears= $this->loadModel('Arrears');
            $this->Properties=$this->loadModel('Properties');
            $this->advance_amount=0;
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
            
            //checking for IP Address
            if(!empty($_SERVER['HTTP_CLIENT_IP'])){$this->ip_address = $_SERVER['HTTP_CLIENT_IP'];}
            elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$this->ip_address= $_SERVER['HTTP_X_FORWARDED_FOR'];}
            else{$this->ip_address = $_SERVER['REMOTE_ADDR'];}
            
            //Getting Last Receipt Number
            $query1 = $this->Payments->find()->max('receipt_no');

            if($query1!=null){
                $this->receipt_no=$query1->receipt_no+1;
            }
            else{
                $this->receipt_no=1;
            }
//            echo $this->receipt;
//            pr($this->request->getData());
            $this->getDataDetails();
            $this->insertdata();
        }
        
        public function getDataDetails() {
            if($this->request->is('post'))
            {
                $this->crr= $this->request->getData('crr');
                $this->div_code= $this->request->getData('division_code');
                $this->ten_code= $this->request->getData('tenure_code');
                $this->payment_date= $this->request->getData('payment_date');
                $this->payment_mode= $this->request->getData('payment_mode');
                $this->check= $this->request->getData('total_amount');
                $this->tpa= $this->request->getData('total_amount');
                $this->other_amount= $this->request->getData('other_amount');
                if($this->other_amount!=null){
                    $this->tpa-= $this->other_amount;
                }
                echo 'due '.$this->total_due_amount= $this->request->getData('total_due');
                $this->grass_number= $this->request->getData('grass_number');
                $this->remarks= $this->request->getData('remarks');
                //echo 'CRR : '.$this->crr.'<br>Division_Code : '.$this->div_code.'<br>Tenure_Code : '.$this->ten_code;
            }
        }
        
        public function lastpaymentid(){
            //Getting Last Payment Id
            $this->autoRender=false;
            $query = $this->Payments->find()->max('payment_id');
            if($query!=null){
                $this->payment_id=$query->payment_id+1;
            }
            else{
                $this->payment_id=1000;
            }
        }
        
        public function insertdata()
        {
            //checking for advances due_date                
            $duedate= $this->Arrears->find()->where([
                        'crr_number'=> $this->crr,
                        'division_code'=> $this->div_code,
                        'tenure_code'=> $this->ten_code,
                        'ispaid'=>true,
                        'update_amount > '=>0
                    ])->order(['due_date'=>'desc'])->limit(1)->first();
            if($duedate!=null)
            {
                $this->due_date=$duedate['due_date'];
                echo $this->due_date;
                echo '<br>';
                //checking for advances
                $advances = $this->Arrears->find()
                        ->select('update_amount')
                        ->where([
                            'crr_number'=> $this->crr,
                            'division_code'=> $this->div_code,
                            'tenure_code'=> $this->ten_code,
                            'due_date'=> $this->due_date
                        ]);
                foreach ($advances as $row_data)
                {
                    $this->advance_amount=$row_data->update_amount;
                    echo "<b>Advance :".$this->advance_amount."</b><br>";
                    echo "Cleared Previous Advance Ammount<br>";
                    $this->tpa+= $this->advance_amount;
                }
                
                $update_date= $this->Arrears->query();
                $update_date->update()
                        ->set(['update_amount'=>0,])
                        ->where([
                            'crr_number'=> $this->crr,
                            'division_code'=> $this->div_code,
                            'tenure_code'=> $this->ten_code,
                            'due_date'=> $this->due_date
                        ])
                        ->execute();
                echo 'Data Updated of : CRR : '.$this->crr.' Tenure : '.$this->ten_code.' Div : '.$this->div_code."<br>";   
            }
            
            $query= $this->Arrears->find()->where(['crr_number'=> $this->crr,'division_code'=> $this->div_code,'tenure_code'=> $this->ten_code,'ispaid'=>false]);
            foreach($query as $data)
            {
                $this->pri_amt=$data->amount;
                $this->due_date=$data->due_date;
                
                //Getting Unpaid ammounts if any
                $stmt = $this->connection->prepare('select * from elrms.arrears where arrears.crr_number=:crr and tenure_code=:ten and division_code=:div and update_amount<0 and due_date=:due');
                $stmt->bindValue('crr', $this->crr);
                $stmt->bindValue('ten', $this->ten_code);
                $stmt->bindValue('div', $this->div_code);
                $stmt->bindValue('due', $this->due_date);
                $rows = $stmt->fetchAll('assoc');
                $rowCount = $stmt->rowCount();
                $stmt->execute();
                if($rowCount==1){
                    foreach($rows as $row)
                    {
                        $this->pri_amt=($row['update_amount']*-1);
                        $this->due_date=$row['due_date'];
                    }
                }
                
                //Getting Lst Payment Id
                $this->lastpaymentid();
                
                //getting interest
                $int= $this->connection
                        ->newQuery()
                        ->select("getinterest(".$this->pri_amt.",6,'".date('Y/m/d',strtotime($this->due_date))."') as int");
                $data1=$int->execute()->fetchAll('assoc');
                
                //Inserting if each row complies
                foreach($data1[0] as $key)
                {
                    $this->interest=$key;
                    $this->row_amt+=($key+$this->pri_amt);
                    $this->due_amt=($this->interest+$this->pri_amt).'<br>';

                    //checking for each row to get cleared
                    if(($this->tpa > $key) and ($this->tpa > $this->pri_amt) and ($this->tpa >= round($this->due_amt,0)))
                    {
                        $this->tpa-= round($this->interest,0);
                        //checking if tpa can clear each principal ammount
                        if($this->tpa >= $this->pri_amt)
                        {
                            $this->tpa-= round($this->pri_amt,0);
                            $insert= $this->Payments->query();
                            $insert->insert([
                                'division_code', 'tenure_code', 'crr_number', 'payment_id', 'payment_date', 'total_amount', 
                                'principle_amount', 'interest_amount', 'other_amount', 'pay_references', 'remarks', 'lastupdateon', 'lastupdatedby', 'lastupdatedfromip', 'grn', 'receipt_no'
                            ])
                                    ->values([
                                        'division_code'=> $this->div_code,
                                        'tenure_code'=> $this->ten_code,
                                        'crr_number'=> $this->crr,
                                        'payment_id'=> $this->payment_id,
                                        'payment_date'=>$this->payment_date,
                                        'total_amount'=> $this->check, 
                                        'principle_amount'=> $this->pri_amt,
                                        'interest_amount'=> $this->interest,
                                        'other_amount'=> $this->other_amount,
                                        'pay_references'=> $this->payment_mode,
                                        'remarks'=> $this->remarks,
                                        'lastupdateon'=>date("Y/m/d h:i:s"),
                                        'lastupdatedby'=>'collectr',
                                        'lastupdatedfromip'=> $this->ip_address,
                                        'grn'=> $this->grass_number,
                                        'receipt_no'=> $this->receipt_no
                                    ])
                                    ->execute();
                            if($insert){
                                echo "Insert Done of CRR : ".$this->crr.' Div : '.$this->div_code.' Ten : '.$this->ten_code.' Due Date : '.$this->due_date.'<br>';
                            }
                            
                            //Updating Ammount
                            $update_amt= $this->Arrears->query();
                            $update_amt->update()
                                    ->set([
                                        'ispaid'=>true,
                                        'payment_id'=> $this->payment_id,
                                        'remarks'=> $this->remarks,
                                        'lastupdateon'=>date("Y/m/d h:i:s"),
                                        'lastupdatedby'=>'collectr',
                                        'lastupdatedfromip'=> $this->ip_address,
                                        'update_amount'=>0
                                        ])
                                    ->where([
                                        'crr_number'=> $this->crr,
                                        'division_code'=> $this->div_code,
                                        'tenure_code'=> $this->ten_code,
                                        'due_date'=> $this->due_date
                                    ])
                                    ->execute();
                            if($update_amt){
                                echo "Data Updated CRR : ".$this->crr.' Div : '.$this->div_code.' Ten : '.$this->ten_code.$this->due_date.'<br>';
                            }
                        }
                        else
                        {
                            $this->tpa=0;
                        }
                    }
                    else
                    {
                        //updating the ammount which is not cleared for the year
                        if($this->tpa!=0){
                            //calculating Percentage
                            echo $this->pri_amt;
                            echo "<br>--------------<br>";
                            echo $this->interest;
                            echo "<br>--------------<br>";
                            echo $percentage= $this->tpa/($this->pri_amt+$this->interest);
                            echo '<br>';
                            echo $this->inte= $this->interest*$percentage;
                            echo '<br>';
                            echo $this->amt= $this->pri_amt*$percentage;
                            
                            $this->tpa-= round($this->inte,0);
                            $this->tpa-= round($this->amt,0);
                            $insert= $this->Payments->query();
                            $insert->insert([
                                'division_code', 'tenure_code', 'crr_number', 'payment_id', 'payment_date', 'total_amount', 
                                'principle_amount', 'interest_amount', 'other_amount', 'pay_references', 'remarks', 'lastupdateon', 'lastupdatedby', 'lastupdatedfromip', 'grn', 'receipt_no'
                            ])
                                    ->values([
                                        'division_code'=> $this->div_code,
                                        'tenure_code'=> $this->ten_code,
                                        'crr_number'=> $this->crr,
                                        'payment_id'=> $this->payment_id,
                                        'payment_date'=>$this->payment_date,
                                        'total_amount'=> $this->check, 
                                        'principle_amount'=> $this->amt,
                                        'interest_amount'=> $this->inte,
                                        'other_amount'=> $this->other_amount,
                                        'pay_references'=> $this->payment_mode,
                                        'remarks'=> $this->remarks,
                                        'lastupdateon'=>date("Y/m/d h:i:s"),
                                        'lastupdatedby'=>'collectr',
                                        'lastupdatedfromip'=> $this->ip_address,
                                        'grn'=> $this->grass_number,
                                        'receipt_no'=> $this->receipt_no
                                    ])
                                    ->execute();
                            if($insert){
                                echo "New Insert Done of CRR : ".$this->crr.' Div : '.$this->div_code.' Ten : '.$this->due_date.'<br>';
                            }
                            
                            $update_amt= $this->Arrears->query();
                            $update_amt->update()
                                    ->set([
                                        'ispaid'=>false,
                                        'payment_id'=> $this->payment_id,
                                        'remarks'=> $this->remarks,
                                        'lastupdateon'=>date("Y/m/d h:i:s"),
                                        'lastupdatedby'=>'collectr',
                                        'lastupdatedfromip'=> $this->ip_address,
                                        'update_amount'=>(($this->amt)-($this->pri_amt))
                                        ])
                                    ->where([
                                        'crr_number'=> $this->crr,
                                        'division_code'=> $this->div_code,
                                        'tenure_code'=> $this->ten_code,
                                        'due_date'=> $this->due_date
                                    ])
                                    ->execute();
                            if($update_amt){
                                echo "New Data Updated CRR : ".$this->crr.' Div : '.$this->div_code.' Ten : '.$this->ten_code.$this->due_date.'<br>';
                            }
                            $this->tpa-= round($this->inte,0);
                            $this->tpa-= round($this->pri_amt,0);
                        }
                    }
                }
            }
            echo 'totalcash : '.$thisamt=($this->check+$this->advance_amount);
            echo 'Due Ammount : '.$thisdue=$this->total_due_amount; 
            if($thisamt>$thisdue)
            {
                $update_amt= $this->Arrears->query();
                $update_amt->update()
                        ->set(['update_amount'=> $this->tpa])
                        ->where([
                            'crr_number'=> $this->crr,
                            'division_code'=> $this->div_code,
                            'tenure_code'=> $this->ten_code,
                            'due_date'=> $this->due_date
                        ])
                        ->execute();
                echo '<br>Dues Cleared and Advances : '.($this->tpa);
            }
        }
    }
?>
