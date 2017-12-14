<?php

namespace app\controllers;

use app\models\Message;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

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

            $message->file = UploadedFile::getInstance($message, 'file');

            $message->ip = Yii::$app->request->getUserIP();

            $message->browser = Yii::$app->request->getUserAgent();

            $message->created_at = date('Y-m-d H:i:s', time());

            // saveFile() may do some validation too
            if ($message->validate() && $message->saveFile()) {
                $message->save(false);

                // emptying the form
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
