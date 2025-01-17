<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\components\extra;
use \backend\modules\Products\models\Productscategory;

$this->title = Yii::t('app', 'Kategóriák');

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="productscategory-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Kategória létrehozása'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'pager' => [
            'firstPageLabel' => Yii::t('app','Első oldal'),
            'lastPageLabel'  => Yii::t('app','Utolsó oldal'),
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            ['attribute' => 'intro',
                'format' => 'raw',
                'value' => function ($model) {
                    return extra::getIntro($model->intro);
                },
            ],
            ['attribute' => 'description',
                'format' => 'raw',
                'value' => function ($model) {
                    return extra::getIntro($model->description);
                },
            ],
            [
            'attribute' => 'status',
                'value' => function ($model) {
                    return Productscategory::status($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status', Productscategory::status(),['class'=>'form-control','prompt' => '']),
            ],
            ['attribute' => 'parent_id',
                'value' => function ($model) {
                        return (!empty($model->parent))?$model->parent->name:Yii::t('app','Főkategória');
                },
                'filter' => Html::activeDropDownList($searchModel, 'parent_id', Productscategory::getParentslisttoadmin(),['class'=>'form-control','prompt' => '']),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>



