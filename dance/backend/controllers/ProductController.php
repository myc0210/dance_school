<?php
    namespace backend\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\CategoryProduct;
    use yii\filters\VerbFilter;
    use yii\web\Response;
    use yii\filters\auth\HttpBearerAuth;


    /**
     * Site controller
     */
    class UserController extends Controller
    {
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

        public function actionCategoryUpdate()
        {
            $app = Yii::$app;
            $request = $app->request;
            $post = $request->post();


        }
    }
