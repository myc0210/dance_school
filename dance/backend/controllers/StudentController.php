<?php
    namespace backend\controllers;

    use common\models\StudentProfile;
    use League\Flysystem\Exception;
    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\User;
    use common\models\StudentCreateForm;
    use common\models\StudentListTable;
    use yii\filters\VerbFilter;
    use yii\web\Response;
    use yii\web\HttpException;
    use yii\filters\auth\HttpBearerAuth;

    /**
     * Site controller
     */
    class StudentController extends Controller
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

        public function actionStudentCreate()
        {

            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $authManager = $app->getAuthManager();
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();
            $postData = $request->post('student');

            try {

                $user = new User();
                $user->uid = $user->generateUid();
                $user->identity_card_number = $postData['identityNo'];
                $user->email = $postData['email'];

                if ($user->save()) {

                    $userId = $user->id;
                    $studentProfile = new StudentCreateForm();
                    $postData['userId'] = $userId;

                    if ($studentProfile->load($postData, '') && $studentProfile->create()) {

                        $roleOwner = $authManager->getRole('student');
                        $authManager->assign($roleOwner, $userId);

                        $response->statusCode = 200;
                        $response->data = [
                            'status' => 'success'
                        ];

                    } else {

                        throw new Exception('Student profile create fail');

                    }

                } else {

                    throw new Exception('User create fail.');

                }

                $transaction->commit();

            } catch (Exception $e) {

                $transaction->rollBack();
                $response->statusCode = 403;

            }
        }

        public function actionStudentList()
        {
            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $studentListModel = new StudentListTable();

            $response->statusCode = 200;
            $response->data = $studentListModel->studentList();
        }

        public function actionStudentTerminate()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $studentId = $request->post('studentId');
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();


            try {
                $studentProfile = new StudentProfile();

                if ($studentTerminated = $studentProfile->terminate($studentId)) {
                    $user = new User();
                    $userId = $studentTerminated->user_id;
                    if ($user->terminate($userId)) {
                        $response->statusCode = 200;
                        $response->data = [
                            'status' => 'success'
                        ];
                    } else {
                        throw new Exception('User terminate fail.');
                    }
                } else {
                    throw new Exception('Student profile terminate fail.');
                }

                $transaction->commit();
            } catch (Exception $e) {

                $transaction->rollBack();
                $response->statusCode = 403;

            }

        }
    }
