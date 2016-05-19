<?php

namespace mirage\user;

use Yii;
use yii\helpers\Url;
/**
 * user module definition class
 */
class UserModule extends \yii\base\Module
{
    public $userUploadDir = '@webroot/uploads/user';

    public $userUploadUrl = '';

    public $admins = [];

    public $rbacUrl = ['/rbac'];

    public $userApi;
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

        if(substr($this->userUploadDir, 0, 1) === '@'){
            $this->userUploadDir = Yii::getAlias($this->userUploadDir);
        }

        Yii::$app->errorHandler->errorAction = '/'.$this->id.'/default/error';

        $userObj = new \mirage\user\api\User();
        $userObj->userModule = $this;
        $this->userApi = (object)['data'=>$userObj->userData(), 'info'=>$userObj->userInfo()];
    }

    public function params()
    {
        Yii::$app->params['adminEmail'] = 'admin@example.com';
        Yii::$app->params['supportEmail'] = 'support@example.com';
        Yii::$app->params['user.passwordResetTokenExpire'] = 3600;
    }
}
