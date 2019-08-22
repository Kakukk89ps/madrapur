<?php
use common\grid\EnumColumn;
use common\models\Page;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 *
 * @var $this yii\web\View
 * @var $searchModel \backend\models\search\PageSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model common\models\Page
 */

$this->title = Yii::t('backend', 'Email templates');

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="box box-success collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('backend', 'Create {modelClass}', ['modelClass' => 'Email Template']) ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <?php

        echo $this->render('_form', [
            'model' => $model,
        ])?>
    </div>
</div>

<?php

echo GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'grid-view table-responsive',
        ],
        'columns' => [
            [
                'attribute' => 'id',
            ],

            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return Html::a($model->name, [
                        'update',
                        'id' => $model->id
                    ]);
                },
                'format' => 'raw',
            ],
            'body',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]);
?>