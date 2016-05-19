# yii2-user

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require miragesoft/yii2-user "dev-master"
```

or add

```
"miragesoft/yii2-user": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by :

Config
```php
    'modules' => [
        ...
        'user' => [
            'class' => 'mirage\user\UserModule',
            'userUploadDir' => '@webroot/uploads/user', //base user upload directory
            'admins' => ['admin', 'mirage'], //Username for CRUD user 
        ],
        ...
    ],
```

# List of available actions

- **/user/regist/signup**                 Displays registration form
- **/user/auth/login**                    Displays login form
- **/user/auth/logout**                   Logs the user out (available only via POST method)
- **/user/auth/request-password-reset**   Displays request password reset form
- **/user/auth/reset-password**           Displays reset password form
- **/user/settings/profile**              Displays profile settings form
- **/user/settings/account**              Displays account settings form 
- **/user/settings/change-password**      Displays change password settings form
- **/user/admin/index**                   Displays user management interface

## Example of menu

You can add links to registration, login and logout as follows:

```php
Yii::$app->user->isGuest ?
    ['label' => 'Sign in', 'url' => ['/user/auth/login']] :
    ['label' => 'Sign out (' . Yii::$app->user->identity->username . ')',
        'url' => ['/user/auth/logout'],
        'linkOptions' => ['data-method' => 'post']],
['label' => 'Register', 'url' => ['/user/regist/signup'], 'visible' => Yii::$app->user->isGuest]
```

## Another modules call user information

```php
$userObj = new $this->userModuleClass(['moduleId' => 'user']);
$userApi = (object)['data'=>$userObj->userData(), 'info'=>$userObj->userInfo()];
print_r($userApi);
echo '<hr />';
echo 'Fullname: '.$userApi->info->fullname;
```

Example Result
```html
stdClass Object
(
    [data] => stdClass Object
        (
            [id] => 1
            [username] => admin
            [created_at] => 1463562063
            [updated_at] => 1463562063
            [firstname] => Mirage
            [lastname] => Studio
            [fullname] => Mirage Studio
            [avatar] => avatar-img.jpg
            [cover] => 
            [bio] => 
            [data] => 
            [roles] => Array
                (
                )

        )

    [info] => stdClass Object
        (
            [id] => 1
            [username] => admin
            [created_at] => 1463562063
            [updated_at] => 1463562063
            [firstname] => Mirage
            [lastname] => Studio
            [fullname] => Mirage Studio
            [avatar] => /uploads/user/1/avatar/avatar-img.jpg
            [cover] => /assets/c5f36f99/images/default-cover.jpg
            [bio] => Not set
            [data] => Not set
            [roles] => Array
                (
                    [0] => stdClass Object
                        (
                            [name] => 
                        )

                )

        )

)
<hr />
Fullname: Mirage Studio
```