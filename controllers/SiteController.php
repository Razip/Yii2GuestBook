<?php

namespace app\controllers;

use app\models\Message;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'app\actions\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $message = new Message();

        if (Yii::$app->request->isPost) {
            $message->load(Yii::$app->request->post());

            $message->setAttributes([
                'file' => UploadedFile::getInstance($message, 'file'),
                'ip' => Yii::$app->request->getUserIP(),
                'browser' => Yii::$app->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s', time()),
            ]);

            if ($message->save()) {
                // we empty the form emptying the model
                // whose data is used to fill it
                $message = new Message();
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Message::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $this->render('index', [
            'message' => $message,
            'dataProvider' => $dataProvider
        ]);
    }
}
