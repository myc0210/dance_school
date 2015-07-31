<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\User;
use common\models\StudentProfile;
use common\models\UserLogin;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

/**
 * Site controller
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'bearerAuth' => [
//                'class' => HttpBearerAuth::className(),
//                'except' => ['login']
//            ],
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout', 'index'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'login' => ['post'],
//                    'logout' => ['post'],
//                ],
//            ],
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

    public function actionStudentLogin() {
        $app = Yii::$app;
        $response = $app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $request = $app->getRequest();
        $user = $request->post('user');
        $userModel = new UserLogin();
        $userModel->load($user, '');
        if (isset($user['nric'])) {
            $user = $userModel->getStudentByNRIC();
            if (empty($user)) {
                $response->data = [
                    'status' => 'error',
                    'error'  => 'NRIC/passport no is not accurate.'
                ];
                return;
            }
        } else if ( isset($user['username'])) {
            $user = $userModel->getStudentByUsername();
            if (empty($user)) {
                $response->data = [
                    'status' => 'error',
                    'error'  => 'Username is not accurate.'
                ];
                return;
            }
        } else {
            $response->data = [
                'status' => 'error',
                'error'  => 'You must fill in either NRIC/passport no or username.'
            ];
            return;
        }
        $response->data = [
            'status' => 'success',
            'user' => $user,
            'detail' => $user->student,
        ];
    }

    public function actionSetUsernamePassword()
    {
        $app = Yii::$app;
        $response = $app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $request = $app->getRequest();
        $userPost = $request->post('user');
        $userId = $userPost['id'];
        $existUser = User::findOne(['username' => $userPost['name']]);
        if (empty($existUser)) {
            $user = User::findOne($userId);
            $user->setUsername($userPost['name']);
            $user->setPassword($userPost['password']);
            if (!$user->save()) {
                $response->statusCode = 500;
            } else {
                $response->data = ['status' => 'success'];
            }
        } else {
            $response->data = ['status' => 'error', 'Username exists.'];
        }
    }
}
