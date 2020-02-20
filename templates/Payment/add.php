<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-file-text-o"></i> Payments</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i>
                        <?= $this->Html->link(__('Home'), ['action' => 'index']) ?>
                    </li>
                    <li><i class="icon_document_alt"></i>Payments</li>
                    <li><i class="fa fa-file-text-o"></i>Tenure</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        Form Elements
                    </header>
                    <?= $this->Form->create($payment,['class'=>'form-horizontal']) ?>
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-5 control-label" for="inputSuccess">Selects Division<span class="required">*</span></label>
                                        <div class="col-lg-7">
                                            <select class="form-control m-bot15" name="division_code" id="division_code" required>
                                                <option hidden>Select Division</option>
                                                <?php 
                                                    foreach($div_list as $key=>$div_list)
                                                    {
                                                        echo "<option value='".$div_list->division_name."'>".$div_list->division_name."</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <label class="col-lg-5 control-label" for="inputSuccess">Select Tenure<span class="required">*</span></label>
                                    <div class="col-lg-5">
                                        <select class="form-control m-bot15" name='tenure_code' id="tenure_code" onchange ="getdetails(this.value);showdetails(this.value)" >
                                            <option hidden>Select Tenure</option>
                                            <?php 
                                                    foreach($ten_list as $key=>$tenure_list)
                                                    {
                                                        echo "<option value='".$tenure_list->tenure_name."'>".$tenure_list->tenure_name."</option>";
                                                    }
                                                ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">CRR Number<span class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <input type="Number" class="form-control" name="crr" id="crr" onkeyup="getdetails(this.value);showdetails(this.value);validate()" required><span id="er" class="required"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Name</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="name" id="name" disabled required>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Address</label>
                                        <div class="col-sm-5">
                                            <textarea name="address" rows="3" cols="50" class="form-control" disabled id="address"></textarea>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">CS Number</label>
                                            <div class="col-sm-5">
                                                <input type="Number" class="form-control" name="cs" id="cs" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">Area</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" name="area" id="area" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="panel-body" id="table">
                        <div class="col-lg-12">
                            <table class="table" id="disptable">
                                <thead>
                                    <tr>
                                        <th>Due Date</th>
                                        <th>End Date</th>
                                        <th>Amount</th>
                                        <th>Interest</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <table class="table">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">Payment Date</label>
                                            <div class="col-sm-5">
                                                <input type="date" id="date" class="form-control" name="payment_date" required>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">Payment ID</label>
                                            <div class="col-sm-5">
                                                <input type="number" class="form-control" name="payment_id" disabled value= "<?php foreach ($id as $key=>$max_id){echo $max_id->pay_id+1;}?>">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-5 control-label" for="inputSuccess">Payment Mode</label>
                                            <div class="col-lg-5">
                                                <select class="form-control m-bot15" name='payment_mode' id="paymode" onchange="if (this.value=='GRAS'){this.form['grass_number'].style.visibility='visible'}else {this.form['grass_number'].style.visibility='hidden'};" >
                                                    <option value="CASH">CASH</option>
                                                    <option value="CHEQUE">CHEQUE</option>
                                                    <option value="GRAS">GRAS</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">Total Paid Amount</label>
                                            <div class="col-sm-5">
                                                <input type="Number" class="form-control" name="total_amount" id="tpa" onkeyup="docal1();validate()" required><span id="erone" class="required"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">Other Amount</label>
                                            <div class="col-sm-5">
                                                <input type="Number" class="form-control m-bot15" value="0" name='other_amount' id="other" onkeyup="docal1()" >
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                         <label class="col-sm-5" control-label"></label>
                                         <div class="col-sm-5">
                                           <input type="textbox" placeholder="GRASS Number" name="grass_number" style="visibility:hidden;"/>
                                        </div>      
                                     </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <a href="#demo" class="btn btn-info" data-toggle="collapse">Show Details</a>
                            <div id="demo" class="collapse">
                                <table class="table" id="table1">

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class ="panel-body">
                        <div class="col-lg-12">
                            <table class="table">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label">Remarks</label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" name="remarks">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-sm-5"></div>
                                <div class="col-sm-2">
                                    <?= $this->Form->button(__('Submit')) ?>
                                    <input type="submit" name='submit' placeholder="Submit" onclick="validate()" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>    
                    <?= $this->Form->end() ?>
                </section>
            </div>
        </div>
    </section>
</section>