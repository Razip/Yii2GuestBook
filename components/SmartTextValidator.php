<?php

namespace app\components;

use yii\validators\Validator;

/**
 * Class SmartTextValidator
 *
 * Here, we'll be applying all the smart validation
 * stuff on message texts to prevent vandalism
 *
 * @package app\components
 */
class SmartTextValidator extends Validator
{
    /**
     * The minimum length of a message text,
     * excluding whitespace characters and HTML-tags
     *
     * @var int
     */
    protected $minLength = 20;

    public function validateAttribute($model, $attribute)
    {
        $html = $model->$attribute;

        $text = strip_tags($html);

        $pureText = preg_replace('/\s+/u', '', $text);

        $charactersEntered = strlen($pureText);

        if ($charactersEntered < $this->minLength) {
            $message = "{attribute} should contain at least {$this->minLength} characters;"
                . " $charactersEntered entered";

            $this->addError($model, $attribute, $message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<< JS
        var minLength = {$this->minLength};
        
        var html = value;

        var text = $(html).text();

        var pureText = text.replace(/\s/g, '');
        
        var charactersEntered = pureText.length;
        
        if (charactersEntered < minLength) {
            var attributeName = attribute.name;
            
            // we make the first letter of the attribute uppercase
            // because this is how auto-generated Yii2 validation code
            // displays attributes name in the error messages
            attributeName = attributeName.charAt(0).toUpperCase() + attributeName.slice(1);
            
            var message = attributeName + ' should contain at least ' + minLength + ' characters;'
                + ' ' + charactersEntered +  ' entered';
            
            messages.push(message);
        }
JS;
    }
}