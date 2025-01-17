<?php



use yii\helpers\Html;

use yii\widgets\ActiveForm;



/* @var $this yii\web\View */

/* @var $model backend\modules\Products\models\BlockoutsSearch */

/* @var $form yii\widgets\ActiveForm */

?>



<div class="blockouts-search">



    <?php $form = ActiveForm::begin([

        'action' => ['index'],

        'method' => 'get',

    ]); ?>



    <?= $form->field($model, 'id') ?>



    <?= $form->field($model, 'start_date') ?>



    <?= $form->field($model, 'end_date') ?>



    <?= $form->field($model, 'product_id') ?>



    <div class="form-group">

        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>

        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>

    </div>



    <?php ActiveForm::end(); ?>



</div>

