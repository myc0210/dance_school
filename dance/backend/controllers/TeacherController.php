<?php
    namespace backend\controllers;
    
    use common\models\TeacherProfile;
    use League\Flysystem\Exception;
    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\User;
    use common\models\TeacherCreateForm;
    use common\models\TeacherListTable;
    use yii\filters\VerbFilter;
    use yii\web\Response;
    use yii\web\HttpException;
    use yii\filters\auth\HttpBearerAuth;
    
    /**
     * Site controller
     */
    class TeacherController extends Controller
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
        
        public function actionTeacherCreate()
        {
            
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $authManager = $app->getAuthManager();
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();
            $postData = $request->post('teacher');
            
            try {
                
                $user = new User();
                $user->uid = $user->generateUid();
                $user->identity_card_number = $postData['identityNo'];

                if ($user->save()) {
                    
                    $userId = $user->id;
                    $teacherProfile = new TeacherCreateForm();
                    $postData['userId'] = $userId;
                    
                    if ($teacherProfile->load($postData, '') && $teacherProfile->create()) {
                        
                        $roleOwner = $authManager->getRole('teacher');
                        $authManager->assign($roleOwner, $userId);
                        
                        $response->statusCode = 200;
                        $response->data = [
                            'status' => 'success'
                        ];
                        
                    } else {
                        
                        throw new Exception('Teacher profile create fail');
                        
                    }
                    
                } else {
                    
                    throw new Exception('User create fail.');
                    
                }
                
                $transaction->commit();
                
            } catch (Exception $e) {
                
                $transaction->rollBack();
                $response->statusCode = 500;
                
            }
        }
        
        public function actionTeacherList()
        {
            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $teacherListModel = new TeacherListTable();
            
            $response->statusCode = 200;
            $response->data = $teacherListModel->teacherList();
        }
        
        public function actionTeacherTerminate()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $teacherId = $request->post('teacherId');
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();
            
            
            try {
                $teacherProfile = new TeacherProfile();
                
                if ($teacherTerminated = $teacherProfile->teacherTerminate($teacherId)) {
                    $user = new User();
                    $userId = $teacherTerminated->user_id;
                    if ($user->terminate($userId)) {
                        $response->statusCode = 200;
                        $response->data = [
                            'status' => 'success'
                        ];
                    } else {
                        throw new Exception('User terminate fail.');
                    }
                } else {
                    throw new Exception('Teacher profile terminate fail.');
                }
                
                $transaction->commit();
            } catch (Exception $e) {
                
                $transaction->rollBack();
                $response->statusCode = 500;
                
            }
            
        }

        public function actionTeacherGet()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $teacherId = $request->get('teacherId');
            $teacherModel = new TeacherProfile();

            if ($teacher = $teacherModel->teacherGet($teacherId)) {
                $user = $teacher->user;
                $responseData['id'] = $teacher->id;
                $responseData['firstName'] = $teacher->first_name;
                $responseData['lastName'] = $teacher->last_name;
                $responseData['identityNo'] = $user->identity_card_number;
                $responseData['description'] = $teacher->description;
                $response->statusCode = 200;
                $response->data = $responseData;
            } else {
                $response->statusCode = 500;
            }
        }

        public function actionTeacherUpdate()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $teacherId = $request->post('teacherId');
            $post = $request->post('teacher');
            $teacherInfo = [
                'firstName' => $post['firstName'],
                'lastName' => $post['lastName'],
                'description' => $post['description']
            ];
            $nric = $post['identityNo'];
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();

            try {
                $teacherModel = new TeacherProfile();
                if ($userId = $teacherModel->teacherUpdate($teacherId, $teacherInfo)) {
                    $user = new User();
                    if ($user->updateNRIC($userId, $nric)) {
                        $transaction->commit();
                        $response->statusCode = 200;
                    } else {
                        throw new Exception('User update fail.');
                    }
                } else {
                    throw new Exception('Teacher profile update fail.');
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $response->statusCode = 500;
            }
        }
    }
