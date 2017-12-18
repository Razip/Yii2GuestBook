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
            <?php
            $template = <<< 'NOW'
            {label}
            <ul class="list-inline">
                <li>
                    <input type="button" class="btn btn-xs" value="link">
                </li>
                <li>
                    <input type="button" class="btn btn-xs" value="code">
                </li>
                <li>
                    <input type="button" class="btn btn-xs" value="italic">
                </li>
                <li>
                    <input type="button" class="btn btn-xs" value="strike">
                </li>
                <li>
                    <input type="button" class="btn btn-xs" value="bold">
                </li>
            </ul>
            {input}{error}{hint}
NOW;

            echo $form->field($message, 'text', ['template' => $template])->textarea();
            ?>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>
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

                [
                    'label' => 'Message',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return $data->text;
                    }
                ],
            ]
        ]);
        ?>
    </div>
    <div class="row"></div>
</div>