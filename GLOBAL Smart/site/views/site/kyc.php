<?php

    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    

    $this->title = 'KYC';
    $this->params['breadcrumbs'][] = $this->title;
?>


<div class="site-index">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <?php if ($model->validate()) : ?>
                        <!-- <div class="alert alert-danger alert-white alert-dismissible fade in" role="alert">
                           <?= \Yii::t("app",'<strong>Warning!</strong> Complite in the KYC form for investment opportunity');?>
                        </div> -->
                    <?php endif; ?>
                </div>
            </div>
            <input type="hidden" name="getCheck" value="<?php echo $getCheck; ?>">
		<!-- 
            <?php $form = ActiveForm::begin(['id' => 'kyc-form']); ?>
            
                <div class="row m-b-20 ">

                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h4 class="header-title m-t-0"><?= \Yii::t("app", 'Personal data');?></h4>

                       <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?> 
                         <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control']) ?> 
                         <?= $form->field($model, 'middle_name')->textInput(['class' => 'form-control']) ?> 
                         <?= $form->field($model, 'birthday')->widget(\yii\widgets\MaskedInput::className(), ['clientOptions' => ['alias' =>  'dd.mm.yyyy']]) ?> 
                         <?= $form->field($model, 'nationality')->textInput(['class' => 'form-control']) ?> 

                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h4 class="header-title m-t-0"><?= \Yii::t("app",'Contact details');?></h4>

                        <?php
                            $options = ['0' => ['Selected' => true]];
                            //вызываем плагин select2 для списка стран
                            $this->registerJs('$(".select2").select2();');
                            if (isset($model->country))
                            $curent_mask = app\models\Country::find()->where(['id' => $model->country])->all()[0]["tel_code"];
                            else
                                $curent_mask = "+7 (999) 999 99 99";
                            $options = yii\helpers\ArrayHelper::merge($options,yii\helpers\ArrayHelper::map(app\models\Country::find()->all(),  function() {
                                    return 'data-mask';
                                }, 'tel_code','id'));

                        ?>

                       
                       <?php
                        $lang_sufix = (Yii::$app->language == 'ru') ? 'ru' : 'en';
                        echo $form->field($model, 'country')->dropDownList((yii\helpers\ArrayHelper::map(app\models\Country::find()->all(), 'id', 'country_'.$lang_sufix )), ['prompt' => \Yii::t("app",'Choose country...'), 'options' => $options,'class' => 'form-control 
                        select2',
                                'onchange' => '$("#profile-phone").inputmask($("#profile-country").find(":selected").attr("data-mask"));',
                            ]
                        ) ?> 
                        

                        
                        <?= $form->field($model, 'city')->textInput(['class' => 'form-control']) ?>
                        <?= $form->field($model, 'address')->textInput(['class' => 'form-control']) ?> 
                        
                        <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), ['mask' => $curent_mask]) ?> 
                    </div>
                </div>

                <?= $form->field($model, 'terms' )->checkbox(['template' => '<div class="checkbox checkbox-custom">{input}<label for="profile-terms">'.\Yii::t('user', 'I hereby confirm to be more than 18 years and not under any restrictions to use the Website and participate in the E-talon token sale or conduct any operations with cryptocurrency under applicable law;').'<br><br>'.\Yii::t('user', 'I hereby confirm that I have never been engaged in any illegal activity, including but not limited to money laundering and financing of terrorism, and will not be using the Website for any illegal activity;').'<br><br>'.\Yii::t('user', 'I hereby confirm to solely control the address and/or cryptocurrency wallet used for the token sale contribution and not act on behalf of any third party and not to transfer the control of the mentioned address to any third party prior to have received GloW tokens;').'<br><br>'.\Yii::t('user', 'I hereby confirm to take full responsibility for compliance with all local laws, rules and regulations;').'<br><br>'.\Yii::t('user', 'I hereby confirm that I have carefully read and accept with <a href="WP_en.docx" download target="_balnk"><b>Whitepaper</b></a> and <a href="TermsConditionsENG.docx" download target="_balnk"><b>Terms and conditions</b></a>.').'</label>{error}</div>']); ?>


                <div class="form-group">
                    <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn ']) ?>
                </div>

            <?php ActiveForm::end(); ?> 
             -->

            <!-- <a  style="color: black; padding: 2%;" href="<?= yii\helpers\Url::to('assets/docs/WP_en.docx', true);?>" download target="_balnk">Terms and conditions</a> -->

            
            
            <?= Html::checkbox('agree', false, ['label' => 'I have fully read, understood and accepted this document 
            (<a  style="color: black;" href="assets/docs/TermsConditionsENG.docx" download target="_balnk">Terms and Conditions</a>)']) ?>
           
            <?= Html::checkbox('agree1', false, ['label' => 'I have fully read, understood and accepted this document
            (<a  style="color: black;" href="assets/docs/PrivacyPolicy.docx" download target="_balnk">Privacy Policy</a>)']) ?>
            
            <?= Html::checkbox('agree2', false, ['label' => 'I have fully read, understood and accepted this document
            (<a  style="color: black;" href="assets/docs/RefundPolicy.docx" download target="_balnk">Policy Refund</a>)']) ?>

            <?= Html::checkbox('agree3', false, ['label' => 'I have fully read, understood and accepted this document
            (<a  style="color: black;" href="assets/docs/Tokensaleagreement.docx" download target="_balnk">Token Sale Agreement</a>)']) ?>

           <!--  <?= Html::checkbox('agree4', false, ['label' => 'I have fully read, understood and accepted this document
            (<a  style="color: black;" href="assets/docs/WP_en.docx" download target="_balnk">Refusal of liability</a>)']) ?> -->

            <?= Html::checkbox('agree5', false, ['label' => 'I have fully read, understood and accepted this document
            (<a  style="color: black;" href="assets/docs/AML.docx" download target="_balnk">KYC и AML</a>)']) ?>
          <div class="forHiddenContent"> 
            <div class="forCheckbox">
                <?= Html::radio('radio', false, ['label' => 'Natural person', 'class' => 'selectDocument', 'value'=>'4']);?>
                <?= Html::radio('radio', false, ['label' => 'Legal person', 'class' => 'selectDocument', 'style'=>'margin-left: 20px', 'value'=>'5']);?>  
                             
            </div>
            <br><br>
                <div class="forRadio4">
                    <?= Html::checkbox('сonditions', false, ['label' => 'I, as a natural person am the citizen/resident  of an offshore (low tax) jurisdiction and/or the jurisdiction that does not cooperate with FATF (Financial Action Task Force of Money Laundering)']) ?>
                    <?= Html::checkbox('сonditions2', false, ['label' => 'I, as a natural person am a resident (tax or otherwise), living or not living in my country, and a holder of a green card (residence permit) confirm, that the legislation of my country prohibits me from taking part to finance  and support the development of such innovations and designs']) ?>

                    <br>

                    <?= Html::checkbox('5сonditions3', false, ['label' => 'I have reviewed and accepted all the sections. I bear full responsibility for the accuracy of the provided information']) ?>

			<br><br>
                    <?php 
                    //var_dump($model->user_img); var_dump($model->user_id); 
                        $forPhoto = $model->user_img;
                        if($forPhoto == NULL):
                    ?>
                    <div id="divForImage" style="display: flex; align-items:stretch;">
	                    <div>
		                    <?php $form = ActiveForm::begin(['id' => 'forPhoto']); ?>

		                    <?= $form->field($modelUp, 'image')->fileInput(['maxlength' => true]) ?>
		                </div>
	                    
	                    <div>
	                    	<?= Html::submitButton('Upload', [
	                    		'class' => 'btn btn-success',
	                    		'id' => 'btnImage', 
	                    		'style' => ['color' => 'black']
	                    	]) ?>
	                   	</div>
	                 </div>
                 

                    <?php ActiveForm::end(); ?>

                    <?php endif; ?>

                    <!-- Форма сверху -->

                    <?php $form = ActiveForm::begin(['id' => 'kyc-form']); ?>

                		

                    <?= $form->field($model, 'occupation')->hiddenInput(['value' => '4', 'data-ocup'=>'jk'])->label(false); ?>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                    	<div id="for3ColumnFlex" style="display: flex; justify-content: space-between;">
                    	<div style="max-width : 48%; width: 100%;">	
                        <h4 class="header-title m-t-0"><?= \Yii::t("app", 'Personal data');?></h4>

                        <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?>
                        <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control']) ?>
                        <?= $form->field($model, 'middle_name')->textInput(['class' => 'form-control']) ?>
                        <!-- <?= $form->field($model, 'nationality')->textInput(['class' => 'form-control']) ?> -->
                        <section>
                        <h3>Citizenship</h3>
                        <ul style="list-style:none;">
                            <li><?= Html::radio('country', false, ['label' => 'USA', 'class' => 'selectDocument', 'value'=>'168']);?>
                                <span class="forCountrySpan" data-id="168">
                                    I am a qualified investor in the aforementioned jurisdiction and my activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore)
                                </span>
                            </li>
                            <li><?= Html::radio('country', false, ['label' => 'Canada', 'class' => 'selectDocument', 'value'=>'83']);?>
                                <span class="forCountrySpan" data-id="83">
                                    I am a qualified investor in the aforementioned jurisdiction and my activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore)  
                                </span>
                            </li>
                            <li><?= Html::radio('country', false, ['label' => 'Singapore', 'class' => 'selectDocument', 'value'=>'160']);?>
                                <span class="forCountrySpan" data-id="160">
                                    I am a qualified investor in the aforementioned jurisdiction and my activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore)
                                </span>
                            </li>                            
                            <li><?= Html::radio('country', false, ['label' => 'South Korea', 'class' => 'selectDocument', 'value'=>'208']);?>                 
                                <span class="forCountrySpan" data-id="208">
                                    I am a qualified investor in the aforementioned jurisdiction and my activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore) 
                                </span>
                            
                            </li>
                        </ul>


                        <?php
                            $options = ['0' => ['Selected' => true]];
                            //вызываем плагин select2 для списка стран
                            $this->registerJs('$(".select2").select2();');
                            if (isset($model->country))
                            $curent_mask = app\models\Country::find()->where(['id' => $model->country])->all()[0]["tel_code"];
                            else
                                $curent_mask = "+7 (999) 999 99 99";
                            $options = yii\helpers\ArrayHelper::merge($options,yii\helpers\ArrayHelper::map(app\models\Country::find()->all(),  function() {
                                    return 'data-mask';
                                }, 'tel_code','id'));

                        ?>

                        <?php
                        $lang_sufix = (Yii::$app->language == 'ru') ? 'ru' : 'en';
                        echo $form->field($model, 'country')->dropDownList((yii\helpers\ArrayHelper::map(app\models\Country::find()->all(), 'id', 'country_'.$lang_sufix )), ['prompt' => \Yii::t("app",'Choose country...'), 'options' => $options,'class' => 'form-control 
                        select2',
                                'onchange' => '$("#profile-phone").inputmask($("#profile-country").find(":selected").attr("data-mask"));',
                            ]
                        ) ?>
                                <span class="forCountrySpan" data-id="20">
                                    I am a qualified investor in the aforementioned jurisdiction and my activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore) 
                                </span>

                         </div>

                         <div style="max-width : 48%; width: 100%; margin-left: auto;">
                        <h3>Other jurisdictions</h3>      

                        <?= $form->field($model, 'birthday')->widget(\yii\widgets\MaskedInput::className(), ['clientOptions' => ['alias' =>  'dd.mm.yyyy']]) ?>

                        
                        <?= $form->field($model, 'id_number')->textInput(['class' => 'form-control']) ?>

                        <?= $form->field($model, 'city')->textInput(['class' => 'form-control']) ?>
                        <?= $form->field($model, 'address')->textInput(['class' => 'form-control']) ?>


                        <h3>Occupation</h3>

                        <?= Html::checkbox('occupation', false, ['label' => 'I, as a state official person (The «state official person»  is a  person who has a state appointed position in any country of the European Union or any other country or who holds a position in an International organization as well)']) ?>

                    </div>

                    </div>

                    <div>
                        <h3>Other</h3>

                        <span>Reason for initiated commercial relations</span>
                        <?= Html::input('text', 'commercial_relations', 'Commercial relations', ['class' => 'form-control'])?>


                        <h3>Contact information</h3>

                        <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), ['mask' => $curent_mask]) ?>


                        <?= Html::checkbox('occupation', false, ['label' => 'I confirm the fullness and accuracy of the information and bear full responsibility for providing false or inaccurate information', 'style' => ['margin-top' => '40px'] ]) ?>


                <div>
                    <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn', 'data-send' => 'finalSend']) ?>
                </div>

                </div>

                
                 

             </div>

                <?php ActiveForm::end(); ?>


                </div> 

                            <div class="forRadio5">
                                <?= Html::checkbox('dition', false, ['label' => 'I, as a representative of a legal person and having legal authority for representation, confirm, that the present legal person is a resident (tax or otherwise) of an offshore (low tax)  jurisdiction and/or the jurisdiction that does not cooperate with FATF (Financial Action Task Force of Money Laundering)']) ?>
                                <?= Html::checkbox('dition2', false, ['label' => 'I, as a representative of a legal person and having legal authority for representation, confirm, that the legislation of my country prohibits that present legal person from taking part to finance  and support the development of such innovations and designs']) ?>

                                 <br>

                                <?= Html::checkbox('5сondit3', false, ['label' => 'I have reviewed and accepted with all the sections. I bear for responsibility for the accuracy of the provided information']) ?>

<div class="col-md-12 col-sm-12 col-xs-12">
        <div id="for3ColumnFlex">
            <div> 
                        <h4 class="header-title m-t-0"><?= \Yii::t("app", 'Personal data');?></h4>
                        <?php 
                            //var_dump($model->user_img); var_dump($model->user_id); 
                            $forPhoto = $model->user_img;
                            if($forPhoto == NULL):
                        ?>
                        <div id="divForImage" style="display: flex; align-items:stretch;">
                        <div>
                            <?php $form = ActiveForm::begin(['id' => 'forPhoto2']); ?>

                            <?= $form->field($modelUp, 'image')->fileInput(['maxlength' => true]) ?>
                        </div>
                        
                        <div>
                            <?= Html::submitButton('Upload', [
                                'class' => 'btn btn-success',
                                'id' => 'btnImage2', 
                                'style' => ['color' => 'black']
                            ]) ?>
                        </div>
                     </div>


                    <?php ActiveForm::end(); ?>

                     <?php endif; ?>

                        <?php $form = ActiveForm::begin(['id' => 'kyc-form2']); ?>
                        <?= $form->field($model, 'occupation')->hiddenInput(['value' => '9', 'data-ocup'=>'jk'])->label(false); ?>
                        <div style="display: flex; flex-direction: column; max-width : 48%; width: 100%;">
                        <?= $form->field($model, 'name')->textInput(['class' => 'form-control'])->label('Name of the legal entity') ?>
                        <?= $form->field($model, 'last_name')->
                        textInput(['class' => 'form-control'])->label('Registration number') ?>
                        </div>
                        <div style="display: flex; flex-direction: column; max-width : 48%; width: 100%; margin-left: auto; margin-top: -245px;">
                        <?= $form->field($model, 'address')->textInput(['class' => 'form-control']) ?>
                        <?= $form->field($model, 'id_number')->textInput(['class' => 'form-control']) ?>
                        </div>
                        <!-- <?= $form->field($model, 'nationality')->textInput(['class' => 'form-control']) ?> -->
                        
                        <h3>Country of incorporation</h3>
                        <ul style="list-style:none;">
                            <li><?= Html::radio('country', false, ['label' => 'USA', 'class' => 'selectDocument', 'value'=>'15']);?>
                                <span class="forCountrySpan" data-id="15">
                                    I, as a representative of a legal person and having legal authority for representation, confirm, that the present legal person is a qualified investor in the aforementioned jurisdiction and its activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore)
                                </span>
                            </li>
                            <li><?= Html::radio('country', false, ['label' => 'Canada', 'class' => 'selectDocument', 'value'=>'16']);?>
                                <span class="forCountrySpan" data-id="16">
                                    I, as a representative of a legal person and having legal authority for representation, confirm, that the present legal person is a qualified investor in the aforementioned jurisdiction and its activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore)  
                                </span>
                            </li>
                            <li><?= Html::radio('country', false, ['label' => 'Singapore', 'class' => 'selectDocument', 'value'=>'17']);?>
                                <span class="forCountrySpan" data-id="17">
                                    I, as a representative of a legal person and having legal authority for representation, confirm, that the present legal person is a qualified investor in the aforementioned jurisdiction and its activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore)
                                </span>
                            </li>                            
                            <li><?= Html::radio('country', false, ['label' => 'South Korea', 'class' => 'selectDocument', 'value'=>'18']);?>                 
                                <span class="forCountrySpan" data-id="18">
                                    I, as a representative of a legal person and having legal authority for representation, confirm, that the present legal person is a qualified investor in the aforementioned jurisdiction and its activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore) 
                                </span>
                            
                            </li>
                        </ul>


                        <?php
                            $options = ['0' => ['Selected' => true]];
                            //вызываем плагин select2 для списка стран
                            $this->registerJs('$(".select2").select2();');
                            if (isset($model->country))
                                $curent_mask = app\models\Country::find()->where(['id' => $model->country])->all()[0]["tel_code"];
                            else
                                $curent_mask = "+7 (999) 999 99 99";
                                $options = yii\helpers\ArrayHelper::merge($options,yii\helpers\ArrayHelper::map(app\models\Country::find()->all(),  function() {
                                    return 'data-mask';
                                }
                                , 'tel_code','id'));

                        ?>

                    <?php
                        $lang_sufix = (Yii::$app->language == 'ru') ? 'ru' : 'en';
                        echo $form->field($model, 'country')->
                        dropDownList((yii\helpers\ArrayHelper::map(app\models\Country::find()->all(), 'id', 'country_'.$lang_sufix )), 
                            ['data-id'=>'55', 'prompt' => \Yii::t("app",'Choose country...'), 'options' => $options,'class' => 'form-control select2',
                            'onchange' => '$("#profile-phone").inputmask($("#profile-country").find(":selected").attr("data-mask"));',
                            ]) 
                    ?>
                <span class="forCountrySpan" data-id="21">
                    I, as a representative of a legal person and having legal authority for representation, confirm, that the present legal person is a qualified investor in the aforementioned jurisdiction and its activities are regulated by the state regulatory organs (example: SEC in the USA, MAS in Singapore) 
                </span>

            </div>

            <div>
                        <h3>Corporate form</h3>
                        <h3>Full information regarding legal representatives</h3> 

                        <div id="needleFileds">
                        
                        <?= $form->field($model, 'vol_auth')->textInput(['class' => 'form-control'])->label('Volume of authority') ?>
                            

                        <?= $form->field($model, 'date_auth')->widget(\yii\widgets\MaskedInput::className(), ['clientOptions' => ['alias' =>  
                        'dd.mm.yyyy']])->label('The date the authority') ?>

                        <div id="add1" class="add">
                        
                        <?= $form->field($model, 'reason_auth')->textInput(['class' => 'form-control'])
                        ->label('Reason the authority was provided') ?>
                        
                        </div>

                        <br><br>
                        </div>

                    



                     <button id="addFields" class="btn btn-success">Add Beneficiary</button>

                     <br><br>
                     <div style="display: flex; flex-direction: column; max-width : 48%; width: 100%;">
                     	<?= $form->field($model, 'name')->textInput(['class' => 'form-control'])?>  
                     </div> 
                     <div style="display: flex; flex-direction: column; max-width : 48%; width: 100%; margin-left: auto; margin-top: -124px;">
                     <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control'])?>
                     </div>
                     <br><br>

                      <?= Html::checkbox('sale', false, ['label' => 'less than 25% of participation in a legal entity']) ?>
                      <?= Html::checkbox('sale2', false, ['label' => '25% or more participation in a legal entity']) ?>

                        <h3>Occupation</h3>


                        <?= Html::checkbox('occupation', false, ['label' => 'I, as a state official person (The «state official person»  is a  person who has a state appointed position in any country of the European Union or any other country or who holds a position in an International organization as well)']) ?>

                    </div>

                    <br><br>

                        <div>
                            <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn', 'data-send' => 'finalSend']) ?>
                        </div>

        </div>
    </div>

                    

                        

                        <?php ActiveForm::end(); ?>


                            </div> 

    

                </section>

        </div>
    </div> 
    </div>
</div>
<?=  $this->registerJsFile('js/newFormKyc.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?=  $this->registerJsFile('js/phone-masks.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
