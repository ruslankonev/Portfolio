<?php
/**
 * Created by PhpStorm.
 * User: sandman
 * Date: 07.03.2018
 * Time: 23:44
 */

namespace app\components;

use Yii;
use yii\base\Widget;

class SAWidget extends Widget
{
    public $alertTypes = [
        'sweet-success' => [
            'class' => 'alert-success',
            'icon' => '<i class="icon fa fa-check"></i>',
        ],
    ];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    /**
     * @var boolean whether to removed flash messages during AJAX requests
     */
    public $isAjaxRemoveFlash = true;

    public function init()
    {
        parent::init();

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();

        foreach($flashes as $type => $data)
        {
            if($type == "sweet-success")
            {
                $data = (array) $data;

                foreach ($data as $message)
                {
                    echo "swal({ title: '".\Yii::t("app", "Success")."', text: '".$message."',  type: 'success', confirmButtonColor: '#4fa7f3' })";
                }

                if($this->isAjaxRemoveFlash /*&& !\Yii::$app->request->isAjax*/)
                {
                    $session->removeFlash($type);
                }
            }
        }
    }
}