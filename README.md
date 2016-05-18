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

You can add links to registration, login and logout as follows:
```php
$userModule = new \mirage\user\api\User(['moduleId' => 'user']);
print_r($userModule->userInfo());
```

Example Result
```html
stdClass Object
(
    [id] => 1
    [username] => admin
    [created_at] => 1463204071
    [updated_at] => 1463540422
    [firstname] => Mirage
    [lastname] => Studio
    [fullname] => Mirage Studio
    [avatar] => /uploads/user/1/avatar/mirage-avatar.jpg
    [cover] => /uploads/user/1/cover/mirage-cover.jpg
    [bio] => 'Thailand'
    [data] => 
    [roles] => Array
        (
            [0] => no roles
        )

)
```