<?php

namespace app\actions;

use yii\captcha\CaptchaAction as Old;

/**
 * Class CaptchaAction
 *
 * This class customizes default captcha
 *
 * @package app\actions
 */
class CaptchaAction extends Old {
    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    protected function generateVerifyCode()
    {
        if ($this->minLength > $this->maxLength) {
            $this->maxLength = $this->minLength;
        }
        if ($this->minLength < 3) {
            $this->minLength = 3;
        }
        if ($this->maxLength > 20) {
            $this->maxLength = 20;
        }
        $length = mt_rand($this->minLength, $this->maxLength);

        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '123456789';
        $code = '';

        for ($i = 0; $i < $length; ++$i) {
            if ($i % 2 && mt_rand(0, 10) > 2 || !($i % 2) && mt_rand(0, 10) > 9) {
                $code .= $digits[mt_rand(0, strlen($digits) - 1)];
            } else {
                $code .= $letters[mt_rand(0, strlen($letters) - 1)];
            }
        }

        return $code;
    }
}