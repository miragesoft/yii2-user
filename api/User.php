<?php
namespace mirage\user\api;
/**
* 
*/
use Yii;
use mirage\user\UserModule;
use mirage\user\models\User as UserModel;


class User
{
	public $userModule = '';

	public function __construct($config)
	{
		$this->userModule = Yii::$app->getModule($config['moduleId']);
	}
	
	public function userInfo()
	{
		$user = UserModel::findOne(Yii::$app->user->id);
		$userUploadPath = $this->userModule->userUploadDir.'/'.$user->id;
		$roles = ['no roles'];
		if(Yii::$app->authManager !== null){
			$roles = Yii::$app->authManager->getRoles($user->id);
		}
        return (object)[
            'id' => Yii::$app->user->id,
            'username' => Yii::$app->user->identity->username,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'firstname' => $user->profile->firstname,
            'lastname' => $user->profile->lastname,
            'fullname' => $user->profile->fullname,
            'avatar' => $this->path2url($userUploadPath.'/avatar/'.$user->profile->avatar),
            'cover' => $this->path2url($userUploadPath.'/cover/'.$user->profile->cover),
            'bio' => $user->profile->bio,
            'data' => $user->profile->data,
            'roles' => $roles,
        ];
	}

	private function path2url($file)
	{
		$url = Yii::$app->homeUrl;
		$url .= $this->userModule->userUploadUrl;
		$url .= str_replace(Yii::getAlias('@webroot/'), '', $file);
		return $url;
	}
}