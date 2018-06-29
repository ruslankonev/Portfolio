<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models;

use dektrium\user\traits\ModuleTrait;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string $name
 * @property string $middle_name
 * @property string $last_name
 * @property string $passport
 * @property string $birthday
 * @property string $phone
 * @property string $nationality
 * @property string $country
 * @property string $city
 * @property string $address
 * @property double $balance
 * @property double $bonus
 * @property double $ref_bonus
 * @property integer $terms
 * @property integer $notify_alert
 *
 * @property User $user
 */
class Profile extends ActiveRecord
{
    use ModuleTrait;
    /** @var \dektrium\user\Module */
    protected $module;

    public $terms1;
    public $terms2;
    public $terms3;
    public $terms4;
    public $terms5;



    /** @inheritdoc */
    public function init()
    {
        $this->module = \Yii::$app->getModule('user');
    }

    public  function getCountryName($id){
        return $this->_country[$id];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    } 
    public function getUserImg($props)
    {
        $a = self::findOne(118);
        
        
        
        $a->user_img = $props;
        $a->save(false);
        $b = $a->user_img;

        return $b;
    }

      

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','name',  'last_name', 'birthday', 
            'date_auth', 'user_img', 'phone', 'nationality', 'country', 'city', 'address', 'id_number'], 'required'],
            [['user_id', 'terms', 'notify_alert', 'occupation'], 'integer'],
            [['balance', 'bonus','ref_bonus'], 'number'],
            [['passport'], 'safe'],
            [['name', 'middle_name', 'last_name', 'passport', 'birthday', 'phone', 'user_img', 'nationality', 'city', 'address',
                'vol_auth', 'reason_auth', 'id_number'], 'string', 'max' => 255],
            [['terms'], 'required', 'requiredValue' => true, 'message' => \Yii::t("app",'You must agree to this condition')]
          /*  [['terms1','terms2','terms3','terms4','terms5'], 'required', 'requiredValue' => true, 'message' => \Yii::t("app",'You must agree to this condition'),
                'when' => function($model){
                return $model->terms != 1;
            }],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'           => \Yii::t('user', 'Name'),
            'middle_name'    => \Yii::t('user', 'Middle name'),
            'last_name'      => \Yii::t('user', 'Last name'),
            'passport'       => \Yii::t('user', 'Passport'),
            'birthday'       => \Yii::t('user', 'Birth date'),
            'date_auth'       => \Yii::t('user', 'Date auth'),
            'phone'          => \Yii::t('user', 'Phone'),
            'nationality'    => \Yii::t('user', 'Nationality'),
            'country'        => \Yii::t('user', 'Country'),
            'city'           => \Yii::t('user', 'City'),
            'vol_auth'           => \Yii::t('user', 'Vol auth'),
            'reason_auth'           => \Yii::t('user', 'Reason auth'),
            'id_number'           => \Yii::t('user', 'Id number'),
            'occupation'           => \Yii::t('user', 'Occupation'),
            'user_img'           => \Yii::t('user', 'User img'),
            'address'        => \Yii::t('user', 'Address'),
            'balance' => \Yii::t('user', 'Balance'),
            'bonus' => \Yii::t('user', 'Balance Bonus'),
            'ref_bonus' => \Yii::t('user', 'Bonus with referal'),
            'notify_alert' => \Yii::t('user', 'Notify Alert'),
            'terms'          => \Yii::t('user', 'I hereby confirm to be more than 18 years and not under any restrictions to use the Website and participate in the E-talon token sale or conduct any operations with cryptocurrency under applicable law;') .' '.
                \Yii::t('user', 'I hereby confirm that I have never been engaged in any illegal activity, including but not limited to money laundering and financing of terrorism, and will not be using the Website for any illegal activity;').' '. \Yii::t('user', 'I hereby confirm that I have never been engaged in any illegal activity, including but not limited to money laundering and financing of terrorism, and will not be using the Website for any illegal activity;').' '. \Yii::t('user', 'I hereby confirm to take full responsibility for compliance with all local laws, rules and regulations;'),
            'terms1' => \Yii::t('user', 'I hereby confirm to be more than 18 years and not under any restrictions to use the Website and participate in the E-talon token sale or conduct any operations with cryptocurrency under applicable law;'),
            'terms2' => \Yii::t('user', 'I hereby confirm that I have never been engaged in any illegal activity, including but not limited to money laundering and financing of terrorism, and will not be using the Website for any illegal activity;'),
            'terms3' => \Yii::t('user', 'I hereby confirm to solely control the address and/or cryptocurrency wallet used for the token sale contribution and not act on behalf of any third party and not to transfer the control of the mentioned address to any third party prior to have received ELP tokens;'),
            'terms4' => \Yii::t('user', 'I hereby confirm to take full responsibility for compliance with all local laws, rules and regulations;'),
            'terms5' => \Yii::t('user', 'I hereby confirm that I have carefully read and accept with Whitepaper and Terms and conditions of E-talon token sale.'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }
}