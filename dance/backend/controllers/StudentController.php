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
                $user->generateEmailToken();

                if ($user->save()) {

                    $userId = $user->id;
                    $studentProfile = new StudentCreateForm();
                    $postData['userId'] = $userId;

                    if ($studentProfile->load($postData, '') && $studentProfile->create()) {
                        if (!$studentProfile->sendEmail()) {
                            throw new Exception('Email send failed.');
                        }
                        $roleOwner = $authManager->getRole('student');
                        $authManager->assign($roleOwner, $userId);

                        $response->statusCode = 200;
                        $response->data = [
                            'status' => 'success'
                        ];

                    } else {

                        throw new Exception('Student profile create failed.');

                    }

                } else {

                    throw new Exception('User create failed.');

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

        public function actionVerifyEmailToken()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $token = $request->post('token');
            $matchedUser = User::findOne(['email_token' => $token]);
            if (!empty($matchedUser)) {
                $response->data = ['userId' => $matchedUser->id];
            } else {
                $response->statusCode = 401;
            }
        }

        public function actionGetProfile()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $userId = $request->post('userId');
            $matchedUser = User::findOne($userId);
            $student = $matchedUser->student;
            $extra = [
                'nric' => $matchedUser->identity_card_number,
                'email' => $matchedUser->email
            ];
            $father = $student->father;
            $mother = $student->mother;
            $contact1 = $student->contact1;
            $contact2 = $student->contact2;

            if (!empty($student)) {
                $response->data = [
                    'student' => $student,
                    'extra' => $extra,
                    'father' => $father,
                    'mother' => $mother,
                    'contact1' => $contact1,
                    'contact2' => $contact2
                ];
            } else {
                $response->statusCode = 500;
            }
        }

        public function actionUpdateProfile()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $profile = $request->post('profile');

        }
    }
