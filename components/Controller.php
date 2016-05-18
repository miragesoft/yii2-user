<?php
namespace mirage\user\components;

use Yii;

class Controller extends \yii\web\Controller
{
	
	public function beforeAction($action)
	{
		if(!parent::beforeAction($action))
			return false;

		if(Yii::$app->user->isGuest){
			Yii::$app->user->setReturnUrl(Yii::$app->request->url);
			Yii::$app->getResponse()->redirect(['/'.$this->module->id.'/auth/login'])->send();
			return false;
		}
		else{
			return true;
		}
	}
}