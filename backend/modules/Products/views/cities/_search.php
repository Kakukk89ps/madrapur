<?php





use yii\helpersbackend\l;


use yii\widgets\ActiveForm;





/* @var $this yii\web\View */


/* @var $model backend\modules\Products\models\CitiesSearch */


/* @var $form yii\widgets\ActiveForm */


?>





<div class="cities-search">





    <?php $form = ActiveForm::begin([


        'action' => ['index'],


        'method' => 'get',


    ]); ?>





    <?= $form->field($model, 'id') ?>





    <?= $form->field($model, 'name') ?>





    <div class="form-group">


        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>


        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>


    </div>





    <?php ActiveForm::end(); ?>





</div>


