<?php
    namespace backend\controllers;

    use Yii;
    use yii\web\Controller;
    use common\models\WaitingList;
    use yii\web\Response;
    use yii\filters\auth\HttpBearerAuth;

    /**
     * Site controller
     */
    class WaitingListController extends Controller
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

        public function actionWaitingListAdd()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $post = $request->post();
            $postData = [];
            $postData['course_variation_id'] = $post['course']['courseId'];
            $postData['user_id'] = $post['userId'];
            $postData['course_variation_detail'] = $post['course']['detail'];
            $waitingList = new WaitingList();
            if ($waitingList->load($postData, '') && $waitingList->save()) {
                $response->statusCode = 200;
                $response->data = [
                    'status' => 'success'
                ];
            } else {
                $response->statusCode = 500;
                $response->data = [
                    'error' => 'Waiting list add failed.'
                ];
            }
        }

        public function actionWaitingListList()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $userId = $request->post('userId');
            $waitingList = WaitingList::findAll(['user_id' => $userId]);

            $response->data = [
                'waitingList' => $waitingList
            ];
        }
    }
