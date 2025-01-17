<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 *
 * @var $this yii\web\View
 * @var $model common\models\WidgetText
 */

?>

<?php

$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
])?>

<?php echo $form->field($model, 'key')->textInput(['maxlength' => 1024]) ?>

<?php echo $form->field($model, 'title')->textInput(['maxlength' => 512]) ?>

<?php

echo $form->field($model, 'body')->widget(trntv\aceeditor\AceEditor::class, [
    'mode' => 'html',
])?>

<?php echo $form->field($model, 'status')->checkbox() ?>

<div class="form-group">
    <?php

echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'),
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ])?>
</div>

<?php ActiveForm::end() ?>
