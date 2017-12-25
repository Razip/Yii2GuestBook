<?php

namespace app\models;

use app\components\SmartTextValidator;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;

class Message extends ActiveRecord
{
    /**
     * @var null|yii\web\UploadedFile
     */
    public $file;

    public $captcha;

    public $file_path;

    public function rules()
    {
        return [
            [['email'], 'email'],

            [['homepage'], 'url'],

            // if you want to disable the captcha while
            // debugging, simply comment following array elements

            [['captcha'], 'required'],

            [
                ['captcha'],

                'match',
                'pattern' => '/^[a-zA-Z\d]+$/',
                'message' => 'Please, use only English letters and digits',
            ],

            [
                ['captcha'],

                'string',
                'length' => 7,
            ],

            [['captcha'], 'captcha'],

            [
                ['username'],

                'match',
                'pattern' => '/^\d+$/',
                'not' => true,
                'message' => 'Username cannot exclusively consist of digits'
            ],

            [
                ['username'],

                'match',
                'pattern' => '/^[a-zA-Z\d]+$/',
                'message' => 'Please, use only English letters and digits',
            ],

            [
                ['username'],

                'string',
                'min' => 4,
                'max' => 20,
            ],

            [
                ['file'],

                'file',
                'extensions' => ['txt', 'png', 'jpg', 'gif'],
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

            // a custom validator
            [['text'], SmartTextValidator::className()],

            [['username', 'email', 'text', 'ip', 'browser', 'created_at'], 'required'],

            [['file_id', 'file_real_name'], 'safe'],
        ];
    }

    public function beforeValidate()
    {
        // removing forbidden HTML tags and CSS styles
        $text = HTMLPurifier::process($this->getAttribute('text'), function (\HTMLPurifier_Config $config) {
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $config->set('HTML.Allowed', 'a[href], em, br, p, code, strong, span[style], pre[class]');
            $config->set('CSS.AllowedProperties', 'text-decoration');
            $config->set('AutoFormat.RemoveEmpty', true);

            $config->set('HTML.Nofollow', true);
            $config->set('HTML.TargetBlank', true);

            $css = $config->getCSSDefinition();

            // allowed values of text-decoration CSS style
            $css->info['text-decoration'] = new \HTMLPurifier_AttrDef_Enum(['line-through']);
        });

        $this->setAttribute('text', $text);

        return true;
    }

    /**
     * This function generates a unique ID and a path
     * that are used to save the file
     */
    protected function generateIDAndPath()
    {
        while (true) {
            $randID = rand();

            $filename = join('.', [
                $randID,
                $this->file->getExtension(),
            ]);

            $filePath = join('/', [
                Yii::getAlias('@message_files_root'),
                $filename,
            ]);

            if (!file_exists($filePath)) {
                $this->setAttributes([
                    'file_id' => $randID,
                    'file_real_name' => $this->file->name,
                ]);

                $this->file_path = $filePath;

                break;
            }
        }
    }

    public function afterValidate()
    {
        if (!empty($this->file)) {
            $this->generateIDAndPath();

            if ($this->file->getExtension() === 'txt') {
                $this->file->saveAs($this->file_path);
            } else {
                $imagine = new Imagine();

                $image = $imagine->open($this->file->tempName);

                $width = $image->getSize()->getWidth();

                $height = $image->getSize()->getHeight();

                // we resize images when they're too big
                if ($width > 320 && ($height > 240)) {
                    $image->resize(new Box(320, 240));
                }

                $image->save($this->file_path);
            }
        }

        return true;
    }
}