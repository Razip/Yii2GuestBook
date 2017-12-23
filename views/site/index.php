<?php

use app\assets\AppAsset;
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
            <div class="table-responsive">
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,

                    'columns' => [
                        'username',
                        'email',

                        [
                            'attribute' => 'homepage',
                            'label' => 'Homepage',
                            'enableSorting' => false,
                        ],

                        [
                            'attribute' => 'created_at',
                            'label' => 'Posted at',
                            'format' => ['datetime', 'Y-M-d H:i:s'],
                        ],

                        [
                            'label' => 'Message',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $this->render('index/messageColumn', ['model' => $model]);
                            }
                        ],
                    ]
                ]);
                ?>
            </div>
            <div class="row"></div>
        </div>
    </div>
<?php
$this->registerJsFile('js/site/index.js', ['depends' => [AppAsset::className()]]);
?>