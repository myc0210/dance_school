<?php
    namespace backend\controllers;
    
    use common\models\Branch;
    use League\Flysystem\Exception;
    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\BranchCreateForm;
    use common\models\BranchListTable;
    use yii\filters\VerbFilter;
    use yii\web\Response;
    use yii\web\HttpException;
    use yii\filters\auth\HttpBearerAuth;
    
    /**
     * Site controller
     */
    class BranchController extends Controller
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
        
        public function actionBranchCreate()
        {
            
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $postData = $request->post('branch');
            $postData['schoolId'] = $postData['selected']['id'];
            unset($postData['selected']);

            $branch = new BranchCreateForm();
            if ($branch->load($postData, '') && $branch->create()) {
                $response->statusCode = 200;
                $response->data = [
                    'status' => 'success'
                ];
            } else {
                $response->statusCode = 500;
                $response->data = [
                    'error' => 'Branch create fail.'
                ];
            }
        }
        
        public function actionBranchList()
        {
            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $branchListModel = new BranchListTable();
            
            $response->statusCode = 200;
            $response->data = $branchListModel->branchList();
        }
        
        public function actionBranchTerminate()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $branchId = $request->post('branchId');
            $branch = new Branch();
            if ($branch->branchTerminate($branchId)) {
                $response->statusCode = 200;
                $response->data = [
                    'status' => 'success'
                ];
            } else {
                $response->statusCode = 500;
                $response->data = [
                    'error' => 'Branch create fail.'
                ];
            }
        }
        
        public function actionBranchGet()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $branchId = $request->get('branchId');
            $branchModel = new Branch();
            
            if ($branch = $branchModel->branchGet($branchId)) {
                $responseData['id'] = $branch->id;
                $responseData['name'] = $branch->branch_name;
                $responseData['address'] = $branch->address;
                $responseData['postcode'] = $branch->postcode;
                $responseData['phone'] = $branch->phone;
                $responseData['selected'] = $branch->school;
                $response->statusCode = 200;
                $response->data = $responseData;
            } else {
                $response->statusCode = 500;
            }
        }
        
        public function actionBranchUpdate()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $branchId = $request->post('branchId');
            $postData = $request->post('branch');
            $postData['schoolId'] = $postData['selected']['id'];
            unset($postData['selected']);
            $branch = new Branch();
            if ($branch->branchUpdate($branchId, $postData)) {
                $response->statusCode = 200;
                $response->data = ['status' => 'success'];
            } else {
                $response->statusCode = 500;
            }
        }
    }
