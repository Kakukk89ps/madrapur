<?php
    /**
     * Created by PhpStorm.
     * User: ROG
     * Date: 2019. 02. 05.
     * Time: 20:38
     */

    use backend\modules\Product\models\Product;
    use backend\modules\Product\models\ProductPrice;
    use backend\modules\Reservations\models\Reservations;
    use dosamigos\datepicker\DatePicker;
    use kartik\helpers\Html;
    use kartik\icons\Icon;
    use kartik\switchinput\SwitchInput;
    use kartik\touchspin\TouchSpin;
    use lo\widgets\Toggle;
    use yii\web\JsExpression;
    use yii\web\View;
    use yii\widgets\ActiveForm;

    $this->title = Yii::t('app', 'New Reservation');
    $this->params['breadcrumbs'][] = $this->title;

    $huf = Yii::$app->keyStorage->get('currency.huf-value') ? Yii::$app->keyStorage->get('currency.huf-value') : null;

?>

<!--suppress ALL -->
<style>
    .wrapper {
        overflow-y: hidden;
    }
</style>
<div class="products-index">
    <div class="panel">
        <div class="panel-heading">

        </div>
        <div class="panel">
        </div>


        <div class="panel-body">

            <?php
                if ($disableForm != 1) {
                    if ($newReservation) {

                        echo $newReservation;
                    }
                    $form = ActiveForm::begin(['id' => 'product-form']);

                    ?>


                    <?php
                    $allMyProducts = Product::getAllProducts();
                    echo $form->field($model, 'title')
                        ->dropDownList(\yii\helpers\ArrayHelper::map($allMyProducts, 'id', 'title'),
                            ['prompt' => 'Please select a product']);


                    ?>

                    <?= $form->field($model, "start_date")->widget(DatePicker::class, [
                        'options' => ['value' => date('Y-m-d', time()), 'class' => 'bg-aqua bordered'],
                        'inline' => true,
                        'template' => '<div class="datepicker datepicker-inline bg-blue-gradient" >{input}</div>',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'startDate' => date(time()),

                        ],

                        // 'options' => ['class'=>'bg-green-gradient']
                    ]); ?>








                    <?= $form->field($model, 'times')
                        ->dropDownList(['prompt' => 'Please select a time']);

                    ?>

                    <div class="panel">
                        <?=Toggle::widget(
                            [
                                'name' => 'paid_status', // input name. Either 'name', or 'model' and 'attribute' properties must be specified.
                                'checked' => false,
                                'options' => [
                                    'data-on'=>'Paid',
                                    'data-off'=>'Unpaid',
                                    'data-width'=>'100px'
                                ],
                                // checkbox options. More data html options [see here](http://www.bootstraptoggle.com)
                            ]
                        );?>


                        <?=Toggle::widget(
                            [
                                'name' => 'paid_method', // input name. Either 'name', or 'model' and 'attribute' properties must be specified.
                                'checked' => false,
                                'options' => [
                                    'data-on'=>'Card',
                                    'data-off'=>'Cash',
                                    'data-width'=>'100px'
                                ],
                                // checkbox options. More data html options [see here](http://www.bootstraptoggle.com)
                            ]
                        );?>
                        <?=   Toggle::widget(
                            [
                                'name' => 'paid_currency', // input name. Either 'name', or 'model' and 'attribute' properties must be specified.
                                'checked' => true,
                                'options' => [
                                    'data-on'=>'EUR',
                                    'data-off'=>'HUF',
                                    'data-width'=>'100px'
                                ],
                                // checkbox options. More data html options [see here](http://www.bootstraptoggle.com)
                            ]
                        );?>


                    </div>


                    <div id="myTimes"></div>
                    <div id="myPrices"></div>
                    <?= Html::submitButton('Create Reservation', ['class' => 'btn btn-block bg-aqua btn-lg btn-primary prodUpdateBtn']) ?>


                    <?php ActiveForm::end();
                } else {
                    if (isset($_POST['paid_status'])) {
                        $paid_status = 'paid';

                    } else {
                        $paid_status = 'unpaid';
                    }

                    if (isset($_POST['paid_method'])) {
                        $paid_method = 'card';
                    } else {
                        $paid_method = 'cash';
                    }
                    if (isset($_POST['paid_currency'])) {
                        $paid_currency = 'EUR';
                    } else {
                        $paid_currency = 'HUF';
                    }

                    $form = ActiveForm::begin(['id' => 'product-form']);
                    $model = new ProductPrice();
                    # var_dump($myPrices);
                    echo '</br>';

                    // TODO fix this nonsense másmodelenátpushingolni egy Reservation
                    ?>
                    <?=Html::hiddenInput('paid_currency', $paid_currency);?>
                    <?=Html::hiddenInput('paid_status', $paid_status);?>
                    <?=Html::hiddenInput('paid_method', $paid_method);?>


                    <?php


                    echo'<div class="row">';


                    foreach ($myPrices as $i => $price) {
                        echo'<div class="col-lg-12">';
                        if ($paid_currency == 'HUF') {
                            $price = ProductPrice::eurtohuf($price);
                        }

                        echo $price->name;

                        $currentProdId = (Yii::$app->request->post('Product'))['title'];
                        echo $form->field($model, "description[$i]")->widget(TouchSpin::class,
                            ['options' =>
                                [

                                    'placeholder' => 'Adjust ...',
                                    'data-priceid' => $price->id,
                                    'autocomplete' => 'off',
                                    'type'   => 'number'
                                ],
                                'pluginOptions' => [
                                    'buttonup_txt'=>Icon::show('plus-circle', ['class'=>'fa-lg','framework'
                                    =>Icon::FA]),
                                    'buttondown_txt'=>Icon::show('minus-circle', ['class'=>'fa-lg','framework'
                                    =>Icon::FA]) ,
                                    'buttonup_class'=>'btn bg-aqua btn-2x',
                                    'buttondown_class'=>'btn bg-aqua'
                                ]
                            ]   );
                        echo'</div>';
                    }
                    echo $form->field($model, 'product_id')->hiddeninput(['value' => $currentProdId])->label(false);
                    echo $form->field($model, 'booking_date')->hiddeninput(['autocomplete' => 'off', 'autocapitalize' => 'off', 'autocorrect' => 'off', 'value' => (Yii::$app->request->post('Product'))['start_date']])->label(false);
                    echo $form->field($model, 'time_name')->hiddeninput(['value' => (Yii::$app->request->post('Product'))['times']])->label(false);

                    echo $form->field($model, 'discount')->hiddeninput()->label(false);
                  ?>






            <div class="col-lg-12 customPrice">
                <div class="box  box-solid collapsed-box">
                    <div class="box-header  bg-blue-gradient with-border">
                        <h3 class="box-title">Seller Tools</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <?=Icon::show('plus-circle', ['class'=>'fa-lg','framework'=>Icon::FA
                                ,'style'=>'color:white'
                                ])?>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                       <div class="panel">
                           <div class="col-lg-12">
                               Custom Price
                               <?= TouchSpin::widget(
                                   [   'name'=>'customPrice',
                                       'options' =>
                                           [

                                               'placeholder' => 'Adjust ...',
                                               'data-priceid' => $price->id,
                                               'autocomplete' => 'off',
                                               'type'   => 'number',

                                           ],
                                       'pluginOptions' => [
                                           'buttonup_txt'=>Icon::show('plus-circle', ['class'=>'fa-lg','framework'
                                           =>Icon::FA]),
                                           'buttondown_txt'=>Icon::show('minus-circle', ['class'=>'fa-lg','framework'
                                           =>Icon::FA]) ,
                                           'buttonup_class'=>'btn bg-aqua btn-2x',
                                           'buttondown_class'=>'btn bg-aqua',
                                           'max'=>'9999999'
                                       ]
                                   ]   );
                               ?>

                           </div>
                       <div class="col-lg-12">
                           <?php
                              // echo Yii::$app->runAction('Reservations/assigner');
                               echo $this->render('assingui',['model'=>new Reservations()]);

                           ?>
                       </div>


                       </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <div class="col-lg-12">
                <?= \insolita\wgadminlte\LteInfoBox::widget([
                        'bgIconColor' => \insolita\wgadminlte\LteConst::COLOR_BLUE,
                        'bgColor' => 'aqua',
                        'number' => "<h4><div id=\"total_price\">0</div></h4>",
                        'text' => 'Total Price' . ' <strong>(' . $paid_currency . ')</strong>',
//                        'description'=>'asd',
                        'icon' => 'fa fa-cart-plus',
                        'showProgress' => true,
                        'progressNumber' => 100,


                    ]);
                ?>
                </div>'
            </div>



                    <?php
                    echo'<div class="col-lg-12">';
                    echo Html::submitButton('Create Reservation', ['class' => 'btn btn-block bg-aqua btn-lg btn-primary prodUpdateBtn']);
                    echo'</div>';
                    ActiveForm::end();
                }

                //

            ?>
        </div>
        </div>
    </div>


<script type="text/javascript">


</script>



<?php
    $toggledisplay = <<< SCRIPT

         function toggleshowcPrice(){
        if($('.customPrice').css('display')!='none'){
            $('.customPrice').hide();
        }
        else{
            $('.customPrice').show();
        }

    }
    
     $().ready(() => {
     
        $('.bootstrap-switch-label').on('click',toggleshowcPrice);
        $('.bootstrap-switch-handle-on').on('click',toggleshowcPrice);
        console.log('megy');
     })



SCRIPT;


    $this->registerJs($toggledisplay, View::POS_HEAD);

?>


    <script>
        var countPrices =<?=$countPrices?>;

        function myFunction(item, index) {


            $('#product-times').html($('#product-times').html() + '<option>' + item['name'] + '</option>');
            $('#product-times option:eq(2)').attr('selected', 'selected');
            $('#product-times option:eq(1)').attr('selected', 'selected');


        }


        function gatherPrices() {
            var PricesObj = {}; // note this
            var i = 0;
            while (i < countPrices) {
                PricesObj[$('#productprice-description-' + i).attr('data-priceid')] = $('#productprice-description-' + i).val();
                i = i + 1
            }

            return PricesObj;

        }


        $().ready(() => {






        $('#product-title').change(function () {
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/Reservations/reservations/gettimes' ?>',
                type: 'post',
                data: {
                    id: $(this).val(),

                },
                success: function (data) {
                    console.log(data.search);
                    mytimes = data.search
                    $('#myTimes').html('');
                    $('#product-times').html('<option>Please select a time</option>')
                    mytimes.forEach(myFunction)


                }
            });
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/Reservations/reservations/getprices' ?>',
                type: 'post',
                data: {
                    id: $(this).val(),

                },
                success: function (data) {
                    console.log(data.search);
                    mytimes = data.search
                    $('#myPrices').html(mytimes);


                }
            });
        });
        })
        ;

        function myFunction(item, index) {


            $('#product-times').html($('#product-times').html() + '<option>' + item['name'] + '</option>');

        }
        <?php

        $currentProdId = (Yii::$app->request->post('Product'))['title'];
        if (!$currentProdId) {
            $currentProdId = 0;
        }
        ?>

        function gatherPrices() {
            var PricesObj = {}; // note this
            var i = 0;
            while (i < countPrices) {
                PricesObj[$('#productprice-description-' + i).attr('data-priceid')] = $('#productprice-description-' + i).val();
                i = i + 1
            }

            return PricesObj;

        }

        var Total = 0;
        $('#product-form').change(function () {
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/Reservations/reservations/calcprice' ?>',
                type: 'post',
                data: {
                    prices: gatherPrices(),
                    productId: $('#product-title').val(),
                    date: $('#product-start_date').val(),
                    time: $('#product-times').val(),
                    prodid: <?=(Yii::$app->request->post('Product'))['title'] ? (Yii::$app->request->post('Product'))['title'] : 999 ?>,
                    currency: '<?=isset($paid_currency) ? $paid_currency : 0 ?>',
                    customPrice:$('input[name=customPrice]').val(),
                },
                success: function (data) {

                    mytimes = data.search;

                    if(data.customPrice){

                        $('#total_price').html(data.customPrice);
                        mytimes = data.customPrice
                    }
                        $('#total_price').html( $('#total_price').html()+mytimes);
                        $('#total_price').html(mytimes);

                        // To be continued
                    $('#productprice-discount').val(mytimes);
                    if (data.response == 'places') {
                        $('#myPrices').html(mytimes);
                    }


                }
            });
        });

        $().ready(() => {
            $('#product-start_date'
        ).attr('autocomplete', 'off');
        })
        ;


    </script>

    <style>
        #product-form {
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none;
            /* Non-prefixed version, currently
                                             supported by Chrome and Opera */
        }
    </style>

</div>

<div id="app-container"></div>