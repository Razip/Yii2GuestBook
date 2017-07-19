<?php

namespace app\models;

use yii\base\DynamicModel;
use yii\db\ActiveRecord;
use Yii;

class Message extends ActiveRecord
{
    /**
     * @var yii\web\UploadedFile
     */
    public $file;
    public $captcha;

    protected function getMessageFilesPath()
    {
        return Yii::$app->getBasePath() . '/web/message_files';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'text', /*'captcha'*/], 'required'],

            [
                ['username'],
                'match',
                'pattern' => '/^[a-zA-Z\d]+$/',
                'message' => 'Please, use English letters and digits only',
            ],

            [['email'], 'email'],
            [['homepage'], 'url'],

//            [['text']],

//            [['captcha'], 'captcha'],

            [
                ['file'],
                'file',
                'extensions' => ['txt', 'png', 'jpg', 'jpeg', 'gif'],
                'checkExtensionByMimeType' => false,
            ],
        ];
    }

    public function saveFile()
    {
        if (!empty($this->file)) {
            // .txt files require additional validation
            if ($this->file->getExtension() === 'txt') {
                $tempModel = DynamicModel::validateData(['file'], [
                    [['file'], 'file', 'maxSize' => 1024 * 100],
                ]);

                $tempModel->file = $this->file;

                if (!$tempModel->validate()) {
                    // putting the error to the main model
                    // so it can be shown
                    $this->addErrors($tempModel->errors);

                    return false;
                }
            } else { // working with pictures
                
            }

            $pathToNewFile = '';

            // a way to ensure that the filename
            // is absolutely unique
            while (true) {
                $filename = uniqid(rand()) . '.' . $this->file->getExtension();

                $pathToNewFile = $this->getMessageFilesPath() . '/' . $filename;

                if (!file_exists($pathToNewFile)) {
                    break;
                }
            }

            $this->file->saveAs($pathToNewFile);

            return true;
        }

        return true;
    }
}