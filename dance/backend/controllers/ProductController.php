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
    class ProductController extends Controller
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

        public function actionCategoryUpdate()
        {
            $app = Yii::$app;
            $response = $app->response;
            $response->format = Response::FORMAT_JSON;
            $request = $app->request;
            $post = $request->post();
            $categoryHierarchyList = $post['categories'];
            $categoryModel = new CategoryProduct();
            $categoryFlatList = $categoryModel->updateCategoryList($categoryHierarchyList);
            $response->data = ['categories' => $categoryFlatList];
        }

        public function actionCategoryList()
        {
            $app = Yii::$app;
            $response = $app->response;
            $response->format = Response::FORMAT_JSON;
            $categoryModel = new CategoryProduct();
            $categoryHierarchyList = $categoryModel->getCategoryList();
            $response->data = ['categories' => $categoryHierarchyList['categories'], 'maxId' =>
            $categoryHierarchyList['maxId']];
        }
    }
