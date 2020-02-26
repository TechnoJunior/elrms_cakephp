<?php
    namespace App\Controller;
    
    use App\Controller\AppController;
    use Cake\Datasource\ConnectionManager;
    
    class DbController extends AppController
    {
        public $interest,$row_amt,$col_amt,$pri_amt,$advance_amt,$other_amt,$tpa,$due_amount,$check,$due_date,$int,$amt;
        public function initialize(): void {
            parent::initialize();
            $this->connection = ConnectionManager::get('default');
            $this->Arrears= $this->loadModel('Arrears');
            $this->viewBuilder()->setLayout('niceadmin');
        }
        public function table1()
        {
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
                $this->due_date=$data->due_date;
                echo '<tr>';
                echo '<td>'.date('Y/m/d',strtotime($data->due_date)).'</td>';
                echo '<td>'.date('Y/m/d',strtotime($data->end_date)).'</td>';
                
                //Getting Unpaid ammounts if any
                $stmt = $this->connection->prepare('select * from elrms.arrears where arrears.crr_number=:crr and tenure_code=:ten and division_code=:div and update_amount<0 and due_date=:due');
                $stmt->bindValue('crr',$crr);
                $stmt->bindValue('ten',$ten_code);
                $stmt->bindValue('div',$div_code);
                $stmt->bindValue('due',$this->due_date);
                $stmt->execute();
                
                $rows = $stmt->fetchAll('assoc');
                $rowCount = $stmt->rowCount();
                if($rowCount==1)
                {
                    foreach($rows as $row)
                    {
                        $this->pri_amt=($row['update_amount']*-1);
                        $this->due_date=$row['due_date'];
                    }
                }
                echo '<td>'.$this->pri_amt.'</td>';
                
                //getting Interest
                $this->int= $this->connection
                        ->newQuery()
                        ->select("getinterest(".$this->pri_amt.",6,'".date('Y/m/d',strtotime($this->due_date))."') as int");
                $data1=$this->int->execute()->fetchAll('assoc');
                
                foreach($data1[0] as $key){
                    echo '<td>'.$key.'</td>';
                    echo '<td>'.($this->pri_amt+$key).'</td>';
                    $this->interest+=$key;
                    $this->row_amt+=($key+$this->pri_amt);
                }
                $this->col_amt+= $this->pri_amt;
            }
            echo '</tr><tr>
            <th colspan=2 align="right">
                Total
            </th>
            <th>'.$this->col_amt.'</th>
            <th>'.$this->interest.'</th>
            <th>'.$this->row_amt.'</th>
            <input type="hidden" name="total_due" id="total_due" value='.round($this->row_amt,0).'>
            </tr>
            <tr>
                <th colspan=2 align="right">
                    Rounded
                </th>
                <th>'.round($this->col_amt, 0).'</th>
                <th>'.round($this->interest,0).'</th>
                <th>'.round($this->row_amt,0).'</th>
            </tr>';
            echo '</table>';
        }
        
        public function table2()
        {
            //Checking for Other Ammount and reducing it from TPA
            if(!empty($this->request->getQuery('tpa'))){
                $this->check= $this->request->getQuery('tpa');
                $this->tpa= $this->request->getQuery('tpa');
            } else{
                $this->tpa=0;
            }
            $this->autoRender=false;
            if(!empty($this->request->getQuery('other_amt'))){
                $this->other_amt=$this->request->getQuery('other_amt');
                $this->tpa-= $this->other_amt;
            } else{
                $this->other_amt=0;
            }
            $this->due_amount= $this->request->getQuery('total_due');
            $crr= $this->request->getQuery('crr');
            $div_code= $this->request->getQuery('division_code');
            $ten_code= $this->request->getQuery('tenure_code');
            
//            echo $crr.'<br>'.$div_code.'<br>'.$ten_code.'<br>'.$this->other_amt.'<br>'.$this->tpa.'<br>';
            echo '<table class="table" id="disptable" border=1 cellpadding=10><thead><tr><th>Due Date</th><th>End Date</th><th>Principal</th><th>Interest</th><th>Total</th><th>Interest Round</th><th>Principal Round</th><th>Bal</th></tr></thead>';
                        
            //checking for Arrears
            $stmt = $this->connection->prepare("select arrears.update_amount from elrms.arrears where arrears.division_code=:div_code and arrears.tenure_code=:ten_code and  arrears.crr_number=:crr and arrears.due_date=(select arrears.due_date from elrms.arrears where arrears.division_code=:div_code and arrears.tenure_code=:ten_code and arrears.crr_number=:crr and arrears.ispaid=true and arrears.update_amount>0 order by arrears.due_date desc limit 1)");
            $stmt->bindValue('crr',$crr);
            $stmt->bindValue('ten_code',$ten_code);
            $stmt->bindValue('div_code',$div_code);
            $stmt->execute();
            
            $rows = $stmt->fetchAll('assoc');
            $rowCount = $stmt->rowCount();
            if($rowCount==1)
            {
                foreach($rows[0] as $row)
                {
                    $this->advance_amt=$row;
                    echo "<b>Advance :".$this->advance_amt."</b>";
                    $this->tpa+= $this->advance_amt;
                }
            }
            
            //Getting Details
            $arrear= $this->Arrears->find('all');
            $arrear->where(['crr_number'=>$crr,'division_code'=>$div_code,'tenure_code'=>$ten_code,'ispaid'=>false]);
            foreach($arrear as $key=>$data)
            {
                $this->pri_amt=$data->amount;
                $this->due_date=$data->due_date;
                echo '<tr>';
                echo '<td>'.date('Y/m/d',strtotime($data->due_date)).'</td>';
                echo '<td>'.date('Y/m/d',strtotime($data->end_date)).'</td>';
                
                //Getting Unpaid ammounts if any
                $stmt = $this->connection->prepare('select * from elrms.arrears where arrears.crr_number=:crr and tenure_code=:ten and division_code=:div and update_amount<0 and due_date=:due');
                $stmt->bindValue('crr',$crr);
                $stmt->bindValue('ten',$ten_code);
                $stmt->bindValue('div',$div_code);
                $stmt->bindValue('due',$this->due_date);
                $stmt->execute();
                $rows = $stmt->fetchAll('assoc');
                $rowCount = $stmt->rowCount();
                if($rowCount==1)
                {
                    foreach($rows as $row)
                    {
                        $this->pri_amt=($row['update_amount']*-1);
                        $this->due_date=$row['due_date'];
                    }
                }
                echo '<td>'.$this->pri_amt.'</td>';
                
                //getting interest
                $this->int= $this->connection
                        ->newQuery()
                        ->select("getinterest(".$this->pri_amt.",6,'".date('Y/m/d',strtotime($this->due_date))."') as int");
                $data1=$this->int->execute()->fetchAll('assoc');
                
                foreach($data1[0] as $key)
                {
                    echo '<td>'.$key.'</td>';
                    echo '<td>'.($this->pri_amt+$key).'</td>';
                    $this->interest=$key;
                    $this->row_amt+=($key+$this->pri_amt);
                    $this->due_amt=($this->interest+$this->pri_amt).'<br>';

                    //checking for each row to get cleared
                    if(($this->tpa > $key) and ($this->tpa > $this->pri_amt) and ($this->tpa >= round($this->due_amt,0)))
                    {
                        echo '<td>'. round($this->interest,0).'</td>';
                        $this->tpa-= round($this->interest,0);

                        //checking if tpa can clear each principla ammount

                        if($this->tpa >= $this->pri_amt)
                        {
                            echo '<td>'. round($this->pri_amt,0).'</td>';
                            $this->tpa-= round($this->pri_amt,0);
                            echo '<td>'.$this->tpa.'</td>';
                        }
                        else
                        {
                            $this->tpa=0;
                        }
                    }
                    else
                    {
                        if($this->tpa!=0){
                            //calculating Percentage
                            $percentage= $this->tpa/($this->pri_amt+$this->interest);
                            $this->int= $this->interest*$percentage;
                            $this->amt= $this->pri_amt*$percentage;

                            echo '<td>'.round($this->int,0).'</td>';
                            $this->tpa-= round($this->int);
                            echo '<td>'.round($this->amt,0).'</td>';
                            $this->tpa-= round($this->amt,0);

                            //Showing Remaining Balance

                            echo '<td><b>'.'(I)'. round($this->interest-$this->int,0).'  (P)'. round($this->pri_amt-$this->amt).'</b></td>';

                            $this->tpa=0;
                        }
                    }
                }
            }
            echo '</tr>';
            echo '</table>';
                      
            //Showing Advances Message
            if(($this->check+$this->advance_amt)>$this->due_amount)
            {
                echo '<br>Dues Cleared and Advances : '.($this->tpa);
            }
            echo '</div></div>';
        }
        
        public function fetchname()
        {
            $this->autoRender=false;            
            $crr= $this->request->getQuery('crr');
            $div_code= $this->request->getQuery('division_code');
            $ten_code= $this->request->getQuery('tenure_code');
            
            $stmt = $this->connection->prepare('SELECT  * from elrms.properties '
                    . 'inner join elrms.arrears on elrms.properties.crr_number=elrms.arrears.crr_number '
                    . 'inner join elrms.assessments on elrms.properties.crr_number=elrms.assessments.crr_number '
                    . 'where  elrms.properties.tenure_code=:ten and elrms.arrears.tenure_code=:ten and '
                    . 'elrms.properties.crr_number=:crr and elrms.arrears.crr_number=:crr and elrms.assessments.crr_number=:crr and '
                    . 'elrms.properties.division_code=:div and elrms.arrears.division_code=:div and elrms.assessments.division_code=:div limit 1');
            $stmt->bindValue('crr',$crr);
            $stmt->bindValue('ten',$ten_code);
            $stmt->bindValue('div',$div_code);
            $stmt->execute();
            $details= $stmt->fetchAll('assoc');
            $rowCount = $stmt->rowCount();
            if($rowCount==1)
            {
                $this->RequestHandler->renderAs($this, 'json');
                echo json_encode($details);
            }
        }
    }
?>