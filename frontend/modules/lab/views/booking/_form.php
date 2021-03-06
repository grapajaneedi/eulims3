<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\lab\Customer;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\widgets\DatePicker;
use common\components\Functions;
use common\models\system\Rstl;
/* @var $this yii\web\View */
/* @var $model common\models\lab\Booking */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="booking-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
         <div class="col-sm-12">
        <?php
        echo $form->field($model, 'rstl_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Rstl::find()->asArray()->all(), 'rstl_id', 'name'),
            'language' => 'en-gb',
            'options' => ['placeholder' => 'Select Testing Laboratory'],
            'pluginOptions' => [
                'allowClear' => true,
                //'disabled' => !Yii::$app->user->can('super-administrator'),
            ],
        ])->label('Laboratory');
        ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
             $func=new Functions();
            echo $func->GetCustomerList($form,$model,false,"Customer");
            ?>  
        </div>
        <div class="col-sm-6">
             <?php
             echo $form->field($model, 'scheduled_date')->widget(DatePicker::classname(), [
             'options' => ['placeholder' => 'Select Date ...',
             'autocomplete'=>'off'],
             'type' => DatePicker::TYPE_COMPONENT_APPEND,
                 'pluginOptions' => [
                     'format' => 'yyyy-mm-dd',
                     'todayHighlight' => true,
                    
                 ]
             ]);
             ?>
        </div>
    </div>    
  
    <div class="row">
        <div class="col-sm-3" style="margin-top: 10px;">
            <label>Sample Quantity</label>
        </div>
        <div class="col-sm-3">
            <div class="input-group" style="margin-bottom: 15px;">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="qty_sample">
                        <span class="glyphicon glyphicon-minus"></span>
                    </button>
                </span>
                <input type="text" name="qty_sample" class="form-control input-number" value="1" min="1" max="100" style="width: 50px;text-align: center;">
                <span class="input-group-btn" style="float:left;">
                    <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="qty_sample">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </span>
            </div>
        </div>
        <div class="err-message" style="margin-top: 10px;font-size: 12px;color: #FF0000;"></div>
    </div>
      <div class="row">
            <div class="col-lg-12"> 
                <?= $form->field($model, 'description')->textarea(['maxlength' => true]); ?>
            </div>
        </div>
    <div class="form-group pull-right">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'id'=>'createBooking']) ?>
            <?php if(Yii::$app->request->isAjax){ ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><script type="text/javascript">
$('.btn-number').click(function(e){
    e.preventDefault();
    
    $(".err-message").html("");
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(0);
    }
});
$('.input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {
    
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    $(".err-message").html("");
    
    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        //alert('Sorry, the minimum value was reached');
        $(".err-message").html("Sorry, the minimum value was reached.");
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        //alert('Sorry, the maximum value was reached');
        $(".err-message").html("Sorry, the maximum value was reached.");
        $(this).val($(this).data('oldValue'));
    }
    
});

$(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
</script>
