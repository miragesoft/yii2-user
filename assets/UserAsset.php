<?php
namespace mirage\user\assets;

use yii\base\Exception;
use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class UserAsset extends AssetBundle
{
    public $sourcePath = '@mirage/user/client';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'dmstr\web\AdminLteAsset',
    ];
}
