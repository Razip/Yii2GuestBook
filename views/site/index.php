<?php

use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $message app\models\Message */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Yii2 Guestbook';
?>
    <div class="site-index">
        <div class="row">
            <?php
            $form = ActiveForm::begin([
                'id' => 'message-form',
                'options' => ['enctype' => 'multipart/form-data'],
            ])
            ?>
            <div class="col-md-4">
                <?= $form->field($message, 'username') ?>

                <?= $form->field($message, 'email') ?>

                <?= $form->field($message, 'homepage') ?>

                <?= $form->field($message, 'file')->fileInput() ?>

                <?= $form->field($message, 'captcha')->widget(Captcha::className()) ?>
            </div>
            <div class="col-md-8">
                <?= $form->field($message, 'text')->textarea() ?>
            </div>
            <div class="row">
                <div class="form-group col-md-12" style="margin-left: 15px">
                    <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
        <div class="row" style="padding: 15px">
            <?= $this->render('/site/index/messages', ['dataProvider' => $dataProvider]) ?>
            <div class="row"></div>
        </div>
    </div>
<?php
$this->registerJsFile('js/site/index.js', ['depends' => [AppAsset::className()]]);
?>