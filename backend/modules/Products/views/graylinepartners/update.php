<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Products\models\Graylinepartners */

$this->title = Yii::t('app', 'Módosítás') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grayline partnerek'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Módosítás');
?>
<div class="graylinepartners-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
