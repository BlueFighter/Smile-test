<?php
/* @var $this yii\web\View */

 echo $this->render('_form', [
    'model' => $modelForm,
]);

$script = <<< JS
var tree = $query;
$('#tree').tree({
        data: tree
    });
JS;
$this->registerJs($script, yii\web\view::POS_READY);

?>

<div id="tree">
</div>
