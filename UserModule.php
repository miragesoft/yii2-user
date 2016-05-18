<?php

namespace mirage\user;

use Yii;
use yii\helpers\Url;
/**
 * user module definition class
 */
class UserModule extends \yii\base\Module
{
    public $userDir = '@webroot/uploads/user';

    public $admins = [];
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'mirage\user\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->layoutPath = '@mirage/user/views/layouts';
        $this->layout = 'main';

        Yii::$app->user->loginUrl = ['/'.$this->id.'/auth/login'];

        $this->params();

        Yii::$app->mailer->viewPath = '@mirage/user/mail';
        // custom initialization code goes here

        if(substr($this->userDir, 0, 1) === '@'){
            $this->userDir = Yii::getAlias($this->userDir);
        }

        Yii::$app->errorHandler->errorAction = '/'.$this->id.'/default/error';
    }

    public function params()
    {
        Yii::$app->params['adminEmail'] = 'admin@example.com';
        Yii::$app->params['supportEmail'] = 'support@example.com';
        Yii::$app->params['user.passwordResetTokenExpire'] = 3600;
    }
}
