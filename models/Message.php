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
            [['email'], 'email'],
            [['homepage'], 'url'],

            // If you want to disable the captcha while
            // debugging, simply comment two following array elements
            [
                ['captcha'],
                'captcha',
            ],

            [['captcha'], 'required'],

            [
                ['username'],

                'match',
                'pattern' => '/^[a-zA-Z\d]+$/',
                'message' => 'Please, use English letters and digits only',
            ],

            [
                ['file'],

                'file',
                'extensions' => ['txt', 'png', 'jpg', 'jpeg', 'gif'],
                'checkExtensionByMimeType' => false,

            ],

            [
                ['file'],

                'file',
                'maxSize' => 1024 * 100,

                'when' => function ($model) {
                    return $model->file->getExtension() === 'txt';
                },

                'whenClient' => "function (attribute, value) {
                    return value.split('.').pop() === 'txt';
                }",
            ],

//            [['text']],

            [['username', 'email', 'text'], 'required'],
        ];
    }

    protected function pathToNewFile()
    {
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
            if ($this->file->getExtension() === 'txt') {
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
        }

        return true;
    }
}