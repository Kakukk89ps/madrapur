<?php
/**
 * Created by PhpStorm.
 * User: ROG
 * Date: 2019. 02. 05.
 * Time: 20:38
 */

use kartik\helpers\Html;
use backend\components\extra;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Foglalások');
$this->params['breadcrumbs'][] = $this->title;
?>

<!--suppress ALL -->
<div class="products-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    $chartProvider=$chartDataProvider->query->all();
    $series2=array();
    $price=array();
    $sources=array();


    /**
     * Visualise Chart on Bookings data TODO: Sort, Import Refunded Bookings + Voucher Orders
     */
    foreach($chartProvider as $data){


        if(!in_array($data->source,$sources)){
            $sources[]=$data->source;

        }
        $data->data=json_decode($data->data);



        $cBookingId=$data->bookingId;
        if(isset($data->data->orderDetails)) {
            $cBookingPaidDate = date('Y-m-d', strtotime($data->data->orderDetails->paid_date));

            if(!isset($data->data->orderDetails->order_currency)) {
                $cBookingCurrency='EUR';

            }else{
                $cBookingCurrency = $data->data->orderDetails->order_currency;

            }

                if(isset($data->data->boookingDetails->booking_cost)) {
                $cBookingTotal = $data->data->boookingDetails->booking_cost;
            }
            else{
                $cBookingTotal ='0';//**Todo  update $data booking cost;

            }
            if ($cBookingCurrency != 'EUR') {
                $cBookingTotal = intval($cBookingTotal) / 300;
            }



            /**
             * A fenti if azt az esetet vizsgálja ha az order total mégse annyi mint a booking total pl kupon/teszt vásárlás
             */
            if (isset($price[$cBookingPaidDate][$data->source])) {
                if(isset($data->data->orderDetails->order_total)) {
                    if ($data->data->orderDetails->order_total == '0') {
                        $cBookingTotal = 0;
                    }
                }

                $price[$cBookingPaidDate][$data->source] += intval($cBookingTotal);
            } else {
                if(isset($data->data->orderDetails->order_total)) {
                    if ($data->data->orderDetails->order_total == '0') {
                        $cBookingTotal = 0;
                    }
                }
                $price[$cBookingPaidDate][$data->source] = intval($cBookingTotal);
            }
        }

    }


    foreach($sources as $source){
        $entity['name']=$source;
        $entity['data']=array();
        foreach ($price as $pDate=>$pValue){
            if(isset($pValue[$source])) {
                $oneDate = array(0 => date('Y-m-d',strtotime($pDate)), 1 => $pValue[$source]);
                $entity['data'][] = $oneDate;
            }

        }

        /**
         * $array[] is 3x faster than array push
         */
        $series2[]=$entity;
    }
    function sortFunction( $a, $b ) {
        return strtotime($a["0"]) - strtotime($b["0"]);
    }

    /**
     * Sorba teszem itt a dátumokat
     */
    $finalSeries=[];

    foreach ($series2 as $serie){
      #  echo '<br/><br/>Szeria sorting előtt';
      #  var_dump($serie);


        usort($serie["data"],"sortFunction");
        $finalSeries[]=$serie;
       # echo '<br/><br/>Szeria sorting után';
       # var_dump($serie);


    }


    #echo(json_encode($series2));

    if(Yii::$app->user->getIdentity()->username !== "manager") {

        echo \onmotion\apexcharts\ApexchartsWidget::widget([
            'type' => 'area', // default area
            'height' => '450', // default 350

            'chartOptions' => [
                'chart' => [
                    'toolbar' => [
                        'show' => true,
                        'autoSelected' => 'zoom'
                    ],
                ],

                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
             /*   'markers'=>[
                    'markers'=> [
                        'size'=> '0',
                        'strokeColor'=> "#fff",
                        'strokeWidth'=> '3',
                        'strokeOpacity'=> '1',
                        'fillOpacity'=> '1',
                        'hover'=> [
                            'size'=> '6',
                        ],
                    ],
                ],*/

                'dataLabels' => [
                    'enabled' => false,
                ],
                'stroke' => [
                    'show' => true,
                    'colors' => ['transparent']
                ],
                'legend' => [
                    'verticalAlign' => 'bottom',
                    'horizontalAlign' => 'left',
                ],
            ],
            'series' => $finalSeries
        ]);
    }

    ?>

    <?php
    $gridColumns = [
        'id',
        'bookingId',
        'productId',
        'source',
        'invoiceDate',
        'bookingDate',
        [
            'label' => 'Edit Booking',
            'format'=>'html',
            'value' => function ($model) {
                return '<a href="/Reservations/reservations/bookingedit?id='.$model->returnId().'">Edit'.'</a>';
            }
        ],

    ];

    echo \yii\grid\GridView::widget([
        'pager' => [
            'firstPageLabel' => Yii::t('app', 'Első oldal'),
            'lastPageLabel' => Yii::t('app', 'Utolsó oldal'),
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]);


    ?>

    <div id="jsonPre">


    </div>

    <?php


    $form = ActiveForm::begin([
        'id' => 'date-import',
        'action' => 'admin',
        'options' => ['class' => 'modImp'],
    ]) ?>

    <style>
        .dateImportWidget {
            background-color: #eee;
            padding: 10px;
        }

        .dateImportWidget input {
            width: 20%;

        }


    </style>

    <div class="box">
        <div class="box-title"><h2>Importálás</h2></div>

        <?php


        # foreach ($response as $booking){
        if (isset($importResponse)) {
            // echo 'Import Státusza: ' . $importResponse . '<br/>';
            if (YII_ENV_DEV) {
                echo($importResponse);
            }
        }


        #}


        ?>


        <div class="dateImportWidget">
            <?php


            ?>
            <?= $form->field($dateImportModel, 'dateFrom')->widget(\yii\jui\DatePicker::class, [         //'language' => 'ru',
                //'dateFormat' => 'yyyy-MM-dd',
            ]) ?>
            <?= $form->field($dateImportModel, 'dateTo')->widget(\yii\jui\DatePicker::class, [         //'language' => 'ru',
                //'dateFormat' => 'yyyy-MM-dd',
            ]) ?>
            <?= $form->field($dateImportModel, 'source')->dropDownList(array('https://budapestrivercruise.eu' => 'https://budapestrivercruise.eu', 'https://silver-line.hu' => 'https://silver-line.hu'), array('options' => array('https://budapestrivercruise.eu' => array('selected' => true)))); ?>

            <?php
            echo Html::submitButton(Yii::t('backend', 'Importálás'),
                [
                    'class' => 'btn btn-primary btn-flat btn-block',
                    'name' => 'import-button'
                ])

            ?>



            <?php ActiveForm::end() ?>
        </div>
        <div class="dateImportStatus">
            <strong>Log</strong>
            <span id="currLog">

            </span>
        </div>


    </div>


    <script>


    </script>

</div>