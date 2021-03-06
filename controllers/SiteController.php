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

        // used by data providers
        $parameters = [
            'query' => Message::find()
                ->select([
                    'username',
                    'email',
                    'homepage',
                    'created_at',
                    'text',
                    'file_id',
                    'file_real_name',
                ])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 25],
        ];

        if (Yii::$app->request->isPost) {
            $message->load(Yii::$app->request->post());

            $message->setAttributes([
                'file' => UploadedFile::getInstance($message, 'file'),
                'ip' => Yii::$app->request->getUserIP(),
                'browser' => Yii::$app->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s', time()),
            ]);

            $result = $message->save();

            // this is used for updating the
            // messages on the client side
            $dataProvider = new ActiveDataProvider($parameters);

            if (Yii::$app->request->isAjax) {
                return $this->asJson([
                    'status' => $result ? 'success' : 'fail',
                    'messages' => $this->renderPartial('/site/index/messages', [
                        'dataProvider' => $dataProvider,
                    ]),
                ]);
            }

            if ($result) {
                // we empty the form emptying the model
                // whose data is used to fill it
                $message = new Message();
            }
        } else {
            $dataProvider = new ActiveDataProvider($parameters);
        }

        return $this->render('index', [
            'message' => $message,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * This action is used to get a certain file by its ID
     *
     * @param string $id
     * @throws \Exception
     * @return mixed
     */
    public function actionGetFile(string $id)
    {
        $fileData = Message::find()
            ->where(['file_id' => $id])
            ->select(['file_real_name'])
            ->one();

        try {
            if (is_null($fileData)) {
                throw new \Exception('No such file was found');
            }

            $realFilename = $fileData->getAttribute('file_real_name');

            $extension = pathinfo($realFilename, PATHINFO_EXTENSION);

            $storageFilename = join('.', [
                $id,
                $extension,
            ]);

            $filePath = join('/', [
                Yii::getAlias('@message_files_root'),
                $storageFilename,
            ]);

            // sending the file with its original name
            return Yii::$app->response->sendFile($filePath, $realFilename);

        } catch (\Exception $e) {
            return $this->render('error', [
                'message' => $e->getMessage()
            ]);
        }
    }
}