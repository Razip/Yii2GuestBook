<?php
use yii\helpers\Html;

/* @var $model app\models\Message */
?>
    <div><?= $model->getAttribute('text') ?></div>
<?php if (!is_null($model->getAttribute('file_id'))) { ?>
    <div style="margin-top: 40px">
        <span class="glyphicon glyphicon-paperclip"></span>
        Attached file:
        <?= Html::a(
            $model->getAttribute('file_real_name'),
            ['site/get-file', 'id' => $model->getAttribute('file_id')]
        ) ?>
    </div>
<?php } ?>