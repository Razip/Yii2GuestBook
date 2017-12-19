<?php

namespace app\models;

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

    public function rules()
    {
        return [
            [['email'], 'email'],

            [['homepage'], 'url'],

            // If you want to disable the captcha while
            // debugging, simply comment two following array elements

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

            [['username', 'email', 'text', 'ip', 'browser', 'created_at'], 'required'],
        ];
    }

    public function beforeValidate()
    {
        // It removes all of the forbidden HTML tags and CSS styles
        $text = HTMLPurifier::process($this->getAttribute('text'), function (\HTMLPurifier_Config $config) {
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $config->set('HTML.Allowed', 'a[href], em, br, p, code, strong, span[style], pre[class]');
            $config->set('CSS.AllowedProperties', 'text-decoration');
            $config->set('AutoFormat.RemoveEmpty', true);

            // Next lines of code add rel="nofollow" attribute and
            // set the necessary target to all links in one place
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.TargetBlank', true);

            $css = $config->getCSSDefinition();

            // only text-decoration:line-through is allowed
            $css->info['text-decoration'] = new \HTMLPurifier_AttrDef_Enum(['line-through']);
        });

        $this->setAttribute('text', $text);

        return true;
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

    public function afterValidate()
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