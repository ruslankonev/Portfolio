<?php
namespace app\assets;

use yii\web\AssetBundle as BaseAdminLteAsset;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class AdminPanelAsset extends BaseAdminLteAsset
{
    public $sourcePath = '@app/assets/admin-panel-dist';
    public $css = [
        'css/alert.css',
        'css/panel.css',
        'css/table.css',
        'css/bootstrap-grid.css',

        'css/icons.css',
        /*'css/metisMenu.min.css',
        'css/icons.css',*/
        'plugins/sweet-alert2/sweetalert2.css',
        'plugins/select2/css/select2.min.css',
        'css/style.css',
        'css/media.css',

    ];
    public $js = [
        /*'js/jquery.slimscroll.min.js',
        'js/metisMenu.min.js',
        'js/jquery.app.js',*/
        'js/main.js',
        'plugins/sweet-alert2/sweetalert2.min.js',
        'plugins/select2/js/select2.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}