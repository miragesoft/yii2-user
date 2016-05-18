<?php
namespace mirage\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\FileHelper;

use \mirage\user\models\Profile;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_WAITING = 1;
    const STATUS_BANNED = 2;
    const STATUS_ACTIVE = 10;

    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirm;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['status', 'default', 'value' => self::STATUS_WAITING],
            ['status', 'in', 'range' => [
                self::STATUS_ACTIVE, 
                self::STATUS_WAITING, 
                self::STATUS_BANNED, 
                self::STATUS_DELETED
            ]],

            [['currentPassword', 'newPassword', 'newPasswordConfirm'], 'required', 'on' => 'changePassword'],
            [['newPassword', 'newPasswordConfirm'], 'required', 'on' => 'create'],
            [['currentPassword'], 'validateCurrentPassword'],
            [['newPassword', 'newPasswordConfirm'], 'string', 'min'=>3,],
            [['newPassword', 'newPasswordConfirm'], 'filter', 'filter'=>'trim',],
            ['newPasswordConfirm', 'compare', 'compareAttribute'=>'newPassword', 'message'=>"Passwords don't match"],
        ];
    }

    /*public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['changePassword'] = ['currentPassword','newPassword', 'newPasswordConfirm'];
        //Scenario Values Only Accepted
        return $scenarios;
    }*/

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    public function validateCurrentPassword()
    {
        if(!$this->verifyPassword($this->currentPassword)){
            $this->addError('currentPassword', 'Current password incorrect.');
        }
    }

    public function verifyPassword($password)
    {
        $dbpassword = static::findOne(['username' => Yii::$app->user->identity->username, 'status' => self::STATUS_ACTIVE])->password_hash;

        return Yii::$app->security->validatePassword($password, $dbpassword);
    }


    /*************EVENT**********************/
    public function afterSave($insert, $changedAttributes )
    {
        if($insert){
            $profile = new Profile();
            $profile->user_id = $this->id;

            $profile->load(Yii::$app->request->post());
            if($profile->save()) {
                $this->prepareUserDir();
            }else{
                throw new NotSupportedException('Save profile ERROR.');
            }

            parent::afterSave($insert, $changedAttributes);
        }
    }

    public function afterDelete()
    {
        $profile = Profile::findOne(['user_id' => $this->id]);
        $profile->delete();

        $removeDir = rtrim(Yii::$app->controller->module->userDir, '/').'/'.$this->id;
        FileHelper::removeDirectory($removeDir);

        parent::afterDelete();
    }

    /******************EVENT********/

    public function prepareUserDir()
    {
        $baseDir = rtrim(Yii::$app->controller->module->userDir, '/').'/'.$this->id;
        $dirs = ['avatar','cover',];
        foreach ($dirs as $key => $dir) {
            FileHelper::createDirectory($baseDir.'/'.$dir);
        }
    }

    public function getProfile()
    {
        return $this->hasOne(\mirage\user\models\Profile::className(), ['user_id' => 'id']);
    }

    public function getStatusList()
    {
        return[
            self::STATUS_ACTIVE => 'Active', 
            self::STATUS_WAITING => 'Waiting', 
            self::STATUS_BANNED => 'Banned', 
            self::STATUS_DELETED => 'Deleted'
        ];
    }

    public function getStatusName()
    {
        $list = $this->getStatusList();
        if(array_key_exists($this->status, $list)){
            return $list[$this->status];
        }

        return null;
    }
}
