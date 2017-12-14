<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\captcha\Captcha;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $message app\models\Message */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
    <div class="row">
        <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,

            'columns' => [
                'username',
                'email',
                'homepage',
                'created_at',

//                [
//                    'label' => 'Message',
//                    'value' => function ($data) {
//                        return '<div>abc</div>';
//                    },
//                ],
            ]
        ]);
        ?>
    </div>
    <div class="row"></div>
</div>