<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;

use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use dektrium\user\models\SettingsForm;
use dektrium\user\traits\EventTrait;
use dektrium\user\models\Profile;
use dektrium\user\Finder;
use dektrium\user\models\query\AccountQuery;
use dektrium\user\traits\AjaxValidationTrait;
use yii\data\ActiveDataProvider;

/*Add for DocUpload*/
use app\models\DocUpload;
use yii\web\UploadedFile;
use dektrium\user\models\User;

class SiteController extends Controller
{
    private $currencies = null;

    /**
     * Event is triggered before updating user's account settings.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_ACCOUNT_UPDATE = 'beforeAccountUpdate';

    /**
     * Event is triggered after updating user's account settings.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_ACCOUNT_UPDATE = 'afterAccountUpdate';
    /**
     * Event is triggered before updating user's profile.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';

    /**
     * Event is triggered after updating user's profile.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';

    use AjaxValidationTrait;
    use EventTrait;


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if($action->id == 'error')
        {
            $this->layout = '@app/views/layouts/content.php';
        }

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                //устанавливем layout для ошибок. Пока для все единая заглушка.
                'layout'=>'@app/views/layouts/main-login.php'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = "";
        //если пользователь авторизовался то его перекидывает на этот контроллер. Здесь и устанавливаем ref связь
        $ref = (string) Yii::$app->request->get("ref");

        if($ref && Yii::$app->user->identity)
        {
            \app\models\Referrers::setRefRelation($ref); //вызываем метод установки ref связей
        }

        //проверяем выполнилась ли транзакция, т.е. смотрим есть ли метка об этотм в профиле.
        if(Yii::$app->user->identity->profile->notify_alert == 1)
        {
            $finder = new Finder();
            $finder->setProfileQuery(new AccountQuery(Profile::className()));
            $model = $finder->findProfileById(Yii::$app->user->identity->getId());
            $model->notify_alert = 0;

            if($model->save())
            {
                Yii::$app->getSession()->setFlash('sweet-success', Yii::t("app", "All transactions were successful.<br>You have added GloW tokens to your account."));
                return $this->refresh();
            }
        }

        if(!Yii::$app->user->identity->profile->terms || !Yii::$app->user->identity->eth_address)
        {
            $model = Yii::createObject(\dektrium\user\models\SettingsForm::className());

            if(Yii::$app->request->post())
            {
                $event = $this->getFormEvent($model);
                $this->performAjaxValidation($model);
                $this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);

                if($model->load(Yii::$app->request->post()) && $model->saveEtherWallet())
                {
                    $this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
                }

                return $this->refresh();
            }
        }

        return $this->render('index', [
                "model" => $model
            ]);
    }

    /**
    *  Страница документации
    *
    *   @return string
    **/
    public function actionDocs()
    {
       return $this->render('docs');
    }

   

    /**
    *   Страница инструкции
    *
    *   @return string
    **/
    public function actionInstuction()
    {
        return $this->render('intructions');
    }

    /**
    *   Страница KYC
    *
    *   @return string
    **/
    public function actionKyc()
    {
        $finder = new Finder();
        $finder->setProfileQuery(new AccountQuery(Profile::className()));
        $model = $finder->findProfileById(Yii::$app->user->identity->getId());
        $event = $this->getProfileEvent($model);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        $this->performAjaxValidation($model);

        $getCheck = $model->occupation;

        

        if($model->load(Yii::$app->request->post()))
        {
            //проверяем если валидация всех 5 условияй пройдена ставим общий флаг что пользователь подписался.
           /* if ($model->validate())
            {
                $model->terms = 1;
            }*/

           $model->save(false); 
            /*test checked*/

            
            
        }

        if($model->load(Yii::$app->request->post()) && $model->save(false))
        {
            Yii::$app->getSession()->setFlash('sweet-success', Yii::t('user', 'Your profile has been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

      /*  
      if($model->terms)
        {
            $model->terms1 = 1;
            $model->terms2 = 1;
            $model->terms3 = 1;
            $model->terms4 = 1;
            $model->terms5 = 1;
        }
        */

        /*DocUpload*/
        $modelUp = new DocUpload;
        
       

            $finder = new Finder();
            $finder->setProfileQuery(new AccountQuery(Profile::className()));
            $userImg = $finder->findProfileById(Yii::$app->user->identity->getId());
            
        

        /*pre($userImg->user_img);
        //$profile->save(false);
        die();*/
        

        //$thisUser = $profileId->user_id;
        $thisUser = Yii::$app->user->identity->profile->user_img;
        //$thisUser = $profileId->findOne(118);
        if (\Yii::$app->request->isAjax)
        {


            
            $file = UploadedFile::getInstance($modelUp, 'image');
            $userImg->user_img = $modelUp->uploadFile($file, $userImg->user_img);
            $userImg->save(false);


            
            
        }

        return $this->render('kyc', ['model' => $model, 'modelUp' => $modelUp, 'profileId' => $thisUser, 'getCheck' => $getCheck]);
    }

    /**
    *   Страница транзакций
    *
    *   @return string
    **/
    public function actionTransactions()
    {
        $filter["user_id"] = Yii::$app->user->identity->id;
        $query = \app\models\Transactions::getTransactions($filter);

        $provider = null;

        if($query->all())
        {
            $provider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 12,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'createdate' => SORT_DESC
                    ]
                ],
            ]);
        }

        return $this->render('transactions', [
            'provider' => $provider
        ]);
    }

    /**
    *   Страница реферальной программы
    *
    *   @return string
    **/
    /*
    public function actionReferrals()
    {
        $ref_link = Yii::$app->getRequest()->getHostInfo()."/?ref=".Yii::$app->user->identity->ref_link;
    */

        /*
            TODO

            График на будущее
        */
            /*
        $statistics = \app\models\Referrals::find()
                                            ->select(['SUM(referrer_bonus) AS total'])
                                            ->where(['referral_id' => Yii::$app->user->identity->id])
                                            ->groupBy(['MONTH(createdate)'])
                                            ->asArray()
                                            ->all();

        pre($statistics);

        return $this->render('referrals', [
            'ref_link' => $ref_link,
            'statistics' => null
        ]);
    }
    */

    /**
     * Login action.
     *
     * @return Response|string
     */

    public function actionTestmassage()
    {

         if(\Yii::$app->request->isAjax)
         {


             $modelUp = new DocUpload;
        
       

            $finder = new Finder();
            $finder->setProfileQuery(new AccountQuery(Profile::className()));
            $userImg = $finder->findProfileById(Yii::$app->user->identity->getId());          

        
            $thisUser = Yii::$app->user->identity->profile->user_img;
        
        
            
            $file = UploadedFile::getInstance($modelUp, 'image');
            $userImg->user_img = $modelUp->uploadFile($file, $userImg->user_img);
            $userImg->save(false);

            return "Sucsess";

            
         }
        $this->layout = false;
         return $this->render('kyc');
    }


    public function actionLogin()
    {
        if(!Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model = new LoginForm();

        if($model->load(Yii::$app->request->post()) && $model->login())
        {
            return $this->goBack();
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}