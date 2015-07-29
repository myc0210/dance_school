<?php
    namespace backend\controllers;

    use Yii;
    use yii\web\Controller;
    use common\models\Paypal;
    use yii\web\Response;

    /**
     * Site controller
     */
    class PaypalController extends Controller
    {
        public $enableCsrfValidation = false;
        /**
         * @inheritdoc
         */
        public function behaviors()
        {
            return [
//                'bearerAuth' => [
//                    'class' => HttpBearerAuth::className(),
//                    'except' => ['login']
//                ],
//                'access' => [
//                    'class' => AccessControl::className(),
//                    'rules' => [
//                        [
//                            'actions' => ['login', 'error'],
//                            'allow' => true,
//                        ],
//                        [
//                            'actions' => ['logout', 'index'],
//                            'allow' => true,
//                            'roles' => ['@'],
//                        ],
//                    ],
//                ],
//                'verbs' => [
//                    'class' => VerbFilter::className(),
//                    'actions' => [
//                        'login' => ['post'],
//                        'logout' => ['post'],
//                    ],
//                ],
            ];
        }

        /**
         * @inheritdoc
         */
        public function actions()
        {
            return [
                'error' => [
                    'class' => 'yii\web\ErrorAction',
                ],
            ];
        }

        public function actionIndex()
        {
            return $this->render('index');
        }

        public function actionPaypalTest()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;

            $response->data = Paypal::testCredential('ATc9xs39o4x2uddeXEVm_aLPJrHdJuiW8lcrDVoBZzF_4IdHW9h7hnSdLa97x4yFxA-POCPAI2A1mjcu', 'EMius7M661vR3T3qy9idvDLOVSWWwXUH68PNmCDN2UYAk6QubgOhvM7kztpqULxo-0JFCDY_cC5FsU0-');
        }
    }
