<?php

use yii\grid\GridView;

/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="table-responsive" id="messages">
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
                    return $this->render('/site/index/messageColumn', ['model' => $model]);
                }
            ],
        ]
    ]);
    ?>
</div>