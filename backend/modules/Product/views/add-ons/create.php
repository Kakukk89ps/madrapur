<?php
/**
 * @var \backend\modules\Product\models\AddOn $model
 * @var array $attributes
 */

use kartik\form\ActiveForm;

?>

<div class="row add-ons-create" id="add-ons-create">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="far fa-chart-bar"></i>
                    Create Add-on
                </h3>
                ​
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php




                    $form = ActiveForm::begin([
                        'id' => 'create-add-on',
                        'class' => 'form-inline',
                        'options' => ['data-pjax' => true ],
                        'action' => 'admin'
                    ]);

//                echo $form->field($model, "prodId", [
//                    'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-text">ID</span>{input}</div>{error}{hint}',
//                    'hintType' => \kartik\form\ActiveField::HINT_DEFAULT,
//                    'hintSettings' => [
//                        'onLabelClick' => false,
//                        'onLabelHover' => true,
//                        'onIconHover' => true,
//                    ]
//                ])->widget(\kartik\select2\Select2::class, [
//                    'data' => [],
//                    'options' => [
//                        'placeholder' => 'Select ',
//                        'name' => 'prodId',
//                        'id' => 'prodId',
//                        'required' => true,
//                        'multiple' => true
//                    ]
//                ])->hint('Entering the full ticket ID is mandatory. In case of voucher ticket block include the initial letter, too.');

                    foreach (['id', 'type'] as $disabledField) {
                        if (false !== ($idx = array_search($disabledField, $attributes))) {
                            unset($attributes[$idx]);
                        }
                    }

                    echo $form->field($model, 'name')
                        ->textInput([
                            'name' => 'name',
                            'id' => 'name',
                        ]);

                    echo $form->field($model, 'icon')
                        ->textInput([
                            'name' => 'icon',
                            'id' => 'icon',
                        ]);

                    echo $form->field($model, 'price')
                        ->textInput([
                            'name' => 'price',
                            'id' => 'price',
                        ]);

                    echo \kartik\helpers\Html::submitButton("Create add-on", [
                        'class' => 'btn btn-primary',
                        'name' => 'create-add-on',
                        'value' => 'save'
                    ]);

                    ActiveForm::end();




                ?>
            </div>
            <!-- /.card-body-->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>