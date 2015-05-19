<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\UserLogin;
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
            'bearerAuth' => [
                'class' => HttpBearerAuth::className(),
                'except' => ['login']
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    'logout' => ['post'],
                ],
            ],
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

    public function actionLogin()
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $authManager = Yii::$app->authManager;

        $model = new UserLogin();
        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            $response->statusCode = 200;
            $response->data = [
                'status' => 'success',
                'accessToken' => $model->getAccessToken(),
                'user' => [
                    'username' => $model->getUser()->username,
                    'role' => $model->getRole()
                ]
            ];
        } else {
            $response->statusCode = 403;
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
