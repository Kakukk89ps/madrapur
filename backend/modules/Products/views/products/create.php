<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Products\models\Products */

$this->title = Yii::t('app', 'Termék létrehozása');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Termékek'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="products-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelTranslations' => $modelTranslations,
        'modelPrices' => $modelPrices,
        'modelTimes' => $modelTimes
    ]) ?>

</div>

