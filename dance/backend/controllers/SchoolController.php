<?php
    namespace backend\controllers;
    
    use common\models\School;
    use League\Flysystem\Exception;
    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\SchoolCreateForm;
    use common\models\SchoolListTable;
    use yii\filters\VerbFilter;
    use yii\web\Response;
    use yii\web\HttpException;
    use yii\filters\auth\HttpBearerAuth;
    
    /**
     * Site controller
     */
    class SchoolController extends Controller
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
        
        public function actionSchoolCreate()
        {
            
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $postData = $request->post('school');

            $school = new SchoolCreateForm();
            if ($school->load($postData, '') && $school->create()) {
                $response->statusCode = 200;
                $response->data = [
                    'status' => 'success'
                ];
            } else {
                $response->statusCode = 500;
                $response->data = [
                    'error' => 'School create fail.'
                ];
            }
        }
        
        public function actionSchoolList()
        {
            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $schoolListModel = new SchoolListTable();
            
            $response->statusCode = 200;
            $response->data = $schoolListModel->schoolList();
        }
        
        public function actionSchoolTerminate()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $schoolId = $request->post('schoolId');
            $school = new School();
            if ($school->schoolTerminate($schoolId)) {
                $response->statusCode = 200;
                $response->data = [
                    'status' => 'success'
                ];
            } else {
                $response->statusCode = 500;
                $response->data = [
                    'error' => 'School create fail.'
                ];
            }
        }
        
        public function actionSchoolGet()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $schoolId = $request->get('schoolId');
            $schoolModel = new School();
            
            if ($school = $schoolModel->schoolGet($schoolId)) {
                $responseData['id'] = $school->id;
                $responseData['name'] = $school->school_name;
                $responseData['email'] = $school->school_email;
                $response->statusCode = 200;
                $response->data = $responseData;
            } else {
                $response->statusCode = 500;
            }
        }
        
        public function actionSchoolUpdate()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $schoolId = $request->post('schoolId');
            $postData = $request->post('school');
            $school = new School();
            if ($school->schoolUpdate($schoolId, $postData)) {
                $response->statusCode = 200;
                $response->data = ['status' => 'success'];
            } else {
                $response->statusCode = 500;
            }
        }
    }
