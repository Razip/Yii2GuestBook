<?php

namespace app\models;

use Imagine\Gd\Imagine;
use yii\base\DynamicModel;
use yii\db\ActiveRecord;
use Yii;
use Imagine\Image\Box;

class Message extends ActiveRecord
{
    /**
     * @var yii\web\UploadedFile
     */
    public $file;
    public $captcha;

    public function rules()
    {
        return [
            [['username', 'email', 'text', 'captcha'], 'required'],

            [
                ['username'],
                'match',
                'pattern' => '/^[a-zA-Z\d]+$/',
                'message' => 'Please, use English letters and digits only',
            ],

            [['email'], 'email'],
            [['homepage'], 'url'],

//            [['text']],

            [['captcha'], 'captcha'],

            [
                ['file'],
                'file',
                'extensions' => ['txt', 'png', 'jpg', 'jpeg', 'gif'],
                'checkExtensionByMimeType' => false,
            ],
        ];
    }

    protected function pathToNewFile() {
        while (true) {
            $filename = uniqid(rand()) . '.' . $this->file->getExtension();

            $pathToNewFile = join('/', [
                Yii::getAlias('@webroot'),
                Yii::getAlias('@message_files'),
                $filename
            ]);

            if (!file_exists($pathToNewFile)) {
                $this->file_url = join('/', [
                    Yii::getAlias('@web'),
                    Yii::getAlias('@message_files'),
                    $filename
                ]);

                return $pathToNewFile;
            }
        }

        return null;
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
                    $this->addErrors($tempModel->getErrors());

                    return false;
                }

                $this->file->saveAs($this->pathToNewFile());
            } else {
                $imagine = new Imagine();

                $image = $imagine->open($this->file->tempName);

                $width = $image->getSize()->getWidth();

                $height = $image->getSize()->getHeight();

                if ($width > 320 && ($height > 240)) {
                    $image->resize(new Box(320, 240));
                }

                $image->save($this->pathToNewFile());
            }

            return true;
        }

        return true;
    }
}