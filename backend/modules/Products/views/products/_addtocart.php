<script>backend\backend\backend\
$(document).ready(function () {
    $(document).click(function (event) {
        var clickover = $(event.target);
        var _opened = $("#cartprices").hasClass("collapse in");
        console.log(clickover);
        if (_opened === true && !clickover.hasClass("cartprices-collapse") && !clickover.hasClass('glyphicon-plus')
                && !clickover.hasClass('glyphicon-minus') && !clickover.hasClass('addtocart-amount')
                 && !clickover.hasClass('btn-number')) {
            $("button.cartprices-collapse").click();
        }
    });
});
</script>

<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
backend\assets\DatetimepickerAsset::register($this);
use backend\modules\Products\models\Products;
use backend\models\Shopcurrency;

$addtocart = new \yii\base\DynamicModel([
            'product_id', 'amount', 'date', 'time'
        ]);

$addtocart->addRule(['product_id', 'amount'], 'number')
->addRule('amount', 'each', ['rule' => ['integer']])
->validate();

$addtocart->product_id=$product->id;

$form = ActiveForm::begin([
    'id' => 'addtocart-form',
    'action' => ['/order/shoppingcart/create'],
    'method' => 'post',
]);

echo $form->field($addtocart, 'product_id', ['options' => ['id'=>'addtocart-productid']])->hiddenInput(['name'=>'addtocart-productid'])->label(false);

$divwidth=(empty($product->times))?'3':'2';

$nomp=($product->marketplace==1)?'<span class="no-mp">'.  Shopcurrency::priceFormat($product->minimalpricenomp).'</span>':'';
echo'
<div class="col-sm-'.$divwidth.'">

<div class="ac-price-title">'.Yii::t('app','Bruttó ár').' :</div>

<div class="ac-price-num" id="total-price">'.$nomp.'<span>'.  Shopcurrency::priceFormat($product->minimalprice).'</span> '.Yii::t('app','/fő').'</div>

</div>

<div class="col-sm-3 add-cart-col-pad">'.Yii::t('app','válasszon dátumot!');

        $addtocart->date=$product->firstavailableday;

        echo $form->field($addtocart, 'date', ['template' => "
                {label}{input}\n{error}{hint}"])->label('<i class="glyphicon glyphicon-calendar"></i>');
        ?>
        <?php /*$mindate=($product->start_date!='' && strtotime($product->start_date)>=strtotime(date('Y-m-d',time())))?$product->start_date:date('Y-m-d', time());
            $mindatedelay=Yii::$app->formatter->asDatetime(strtotime("+".$product->start_date_delay." day",time()),'php:Y-m-d');
            if(strtotime($mindate)<strtotime($mindatedelay))$mindate=$mindatedelay;*/
        ?>
        <script type="text/javascript">
            $(function () {
                $('#dynamicmodel-date').datetimepicker({
                    locale: '<?= Yii::$app->language ?>',
                    format: 'YYYY-MM-DD',
                    disabledDates: [<?= $product->blockoutdays ?>],
                    minDate : new Date("<?= $product->firstavailableday ?>"),
                    <?= ($product->end_date!='')?'maxDate : new Date("'.$product->end_date.'"),':'' ?>
                }).on("dp.change", function(e) {
                    $.ajax({
                        type: "POST",
                        url: '/products/products/gettimes',
                        data: {date: $('#dynamicmodel-date').val(), prodid: <?= $product->id ?>},
                        success: function( response ) {
                            $('#cart-time').html(response.times);
                        }
                    });
                });
            });
        </script>
<?php

echo '</div>';

if(!empty($product->times)) {
    echo '<div class="col-sm-2 add-cart-col-pad">'.Yii::t('app','Időpontot');
    echo $form->field($addtocart, 'time')->dropDownList($product->timestodropdown, ['id'=>'cart-time'])->label(false);
    echo '</div>';
}

echo '<div class="col-sm-3 add-cart-col-pad2">&nbsp;<br/>';
echo'<button class="cartprices-collapse" data-toggle="collapse" data-target="#cartprices" id="cartprices-collapse">'.Yii::t('app','VÁLASSZ JEGYET').'</button>';
echo '<div id="cartprices" class="collapse">';
    $i=0;
    foreach ($product->prices as $price) {
        $nomp=($product->marketplace==1)?'<span class="nomp">('.Shopcurrency::priceFormat($price->price).')</span>':'';
        echo '<input type="hidden" name="ticket-price-'.$i.'" id="ticket-price-'.$i.'" value="'.$price->dprice.'">';
        echo '<div class="input-group pricelist">
                <div class="prices-info">
                    '.$price->name.' - '.$nomp.Shopcurrency::priceFormat($price->dprice).'<br/>
                    <span>('.$price->description.')</span>
                </div>
                <div class="prices-amount">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-danger btn-number"  data-type="minus" data-field="DynamicModel[amount]['.$price->id.']">
                      <span class="glyphicon glyphicon-minus"></span>
                    </button>
                </span>';
        echo $form->field($addtocart, "amount[$price->id]", ['template' => '{input}' ,'options' => ['class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'ticket-price-amount-'.$i, 'placeholder' => '', 'class' => 'addtocart-amount input-number', 'value'=>0, 'min'=>0, 'max'=>999])->label(false);
        echo '<span class="input-group-btn">
                    <button type="button" class="btn btn-success btn-number" data-type="plus" data-field="DynamicModel[amount]['.$price->id.']">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </span>
                </div>
            </div>';
        $i++;
    }
echo '</div></div>';

echo '<div class="col-sm-'.$divwidth.'">';
echo Html::submitButton(Yii::t('app','KOSÁRBA TESZEM'), ['class' => 'addtocart-sub', 'id'=>'addtocart-send']);
echo'</div>';
ActiveForm::end();
?>

<script>
$("form#addtocart-form").submit(function(e){
    e.preventDefault();
    return false;
});
$("#addtocart-send").click(function(e){
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: '/order/shoppingcart/create',
        data: $("form#addtocart-form").serialize(),
        success: function( response ) {
            $('#myModal').modal('show');
            $('#myModal #mymodalcontent').html(response.notify);
            $('#cart').html(response.cart);
        }
    });

    return false;
});

$('.btn-number').click(function(e){
    e.preventDefault();

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

    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }

    var val=0;
    for (i = 0; i < <?= $i ?>; i++) {
        var priceid='#ticket-price-'+i; var priceamiuntid='#ticket-price-amount-'+i;
        val=val+($(priceid).val()*$(priceamiuntid).val());
    }
    $.ajax({
        type     :'POST',
        cache    : false,
        url  : '/shopcurrency/priceformat',
        data: {value: val},
        success  : function(response) {
            $('#total-price').html(response);
        }
    });
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
