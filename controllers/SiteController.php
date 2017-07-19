<?php

namespace app\controllers;

use app\models\Message;
use yii\web\Controller;
use Yii;
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

            $message->file = UploadedFile::getInstance($message, 'file');

            // saveFile() may do some extra validation too
            if ($message->validate() && $message->saveFile()) {
                // emptying the form
                $message = new Message();
            }
        }

        return $this->render('index', ['message' => $message]);
    }
}
