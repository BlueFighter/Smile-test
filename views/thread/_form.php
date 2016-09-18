<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GenerateForm */
/* @var $form yii\widgets\ActiveForm */

?>

    <?php $form = ActiveForm::begin([
        'id' => 'TreeGenerator'
    ]); ?>

    <?= $form->field($model, 'nodes')->textInput()->label('Количество узоов')?>


<div class="form-group">
    <?= Html::submitButton('Генерировать дерево', ['class' => 'btn btn-primary']) ?>
</div>

    <?php ActiveForm::end(); ?>
