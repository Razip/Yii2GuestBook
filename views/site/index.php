<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\captcha\Captcha;

/* @var $this yii\web\View */

$this->title = 'Yii2 Guestbook';
?>
<div class="site-index">
    <div class="row">
        <?php
        $form = ActiveForm::begin([
//        'id' => 'message-form',
            'options' => ['enctype' => 'multipart/form-data'],
        ])
        ?>
        <div class="col-md-4">
            <?= $form->field($message, 'username') ?>

            <?= $form->field($message, 'email') ?>

            <?= $form->field($message, 'homepage') ?>

            <?= $form->field($message, 'file')->fileInput() ?>

            <?= $form->field($message, 'captcha')->widget(Captcha::className()) ?>

            <div class="form-group">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <div class="col-md-8">
            <?= $form->field($message, 'text')->textarea() ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
    <div class="row"></div>
</div>
