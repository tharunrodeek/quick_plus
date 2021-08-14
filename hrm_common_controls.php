<?php
$year=date('Y');
$month=date('m');
$selected='';
$sele_month='';

$months=array('01'=>"Janaury (01)",'02'=>"February (02)",'03'=>"March (03)",'04'=>"April (04)",'05'=>"May (05)",
              '06'=>"June (06)",'07'=>"July (07)",'08'=>"August (08)",'09'=>"September (09)",'10'=>"October (10)",
              '11'=>"November (11)",'12'=>"December (12)");
?>


<div class="col-lg-2">
                                        <label><?= trans('YEAR') ?>:
                                        </label>
                                        <select class="form-control kt-select2 ap-year"
                                                name="year" id="ddl_year">
                                               <option value="">SELECT</option>
                                            <?php
                                              for($i=2019;$i<=2040;$i++):

                                                if($i==$year)
                                                {
                                                   $selected='selected="selected"'; 
                                                }
                                                else
                                                {
                                                    $selected='';
                                                }
                                             ?>
                                                <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                                             <?php endfor; ?>   
                                        </select>
                                        <span class="error_note form-text text-muted kt-hidden">Please enter your full name</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class=""><?= trans('MONTH') ?>:</label>
                                        <select class="form-control kt-select2 ap-select2 ClsMonths"
                                                name="ddl_months" >
                                            <option value="">SELECT</option>
                                             <?php
                                              foreach($months as $key=>$val):

                                                if($key==$month)
                                                {
                                                    $sele_month='selected="selected"'; 
                                                }
                                                else
                                                {
                                                    $sele_month=''; 
                                                }
                                             ?>
                                             <option value="<?php echo $key; ?>" <?php echo $sele_month; ?>><?php echo $val; ?></option>
                                         <?php endforeach; ?>
                                        </select>
                                    </div>