<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\Modevent\models\Modevent $model
 */

$this->title = 'Create Modevent';
$this->params['breadcrumbs'][] = ['label' => 'Modevents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modevent-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
