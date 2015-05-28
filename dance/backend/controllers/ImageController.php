<?php
namespace backend\controllers;

use Yii;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use common\models\Image;
use common\models\ProductImage;

class ImageController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
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
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    public function actionUpload()
    {
        $app = Yii::$app;
        $response = $app->response;
        $response->format = Response::FORMAT_JSON;

        $flysystem = $app->flysystem;
        $image = new Image();
        $image->file = UploadedFile::getInstanceByName('file');

        if ($image->file && $image->validate()) {
            $stream = fopen($image->file->tempName, 'r+');
            $fileName = $app->security->generateRandomString() . time() . '.' . $image->file->extension;
            $filePath = Yii::getAlias('@upload') . '/' . $fileName;
            $flysystem->writeStream($filePath, $stream);
            fclose($stream);
            $image->name = $image->file->baseName;
            $image->path = $filePath;
            if(!$image->save()) {
                $response->statusCode = 500;
            } else {
                $response->data = ['image' => $image];
            }
            return $response;
        }
    }

    public function actionList()
    {
        $app = Yii::$app;
        $response = $app->response;
        $response->format = Response::FORMAT_JSON;
        $images = Image::find()->all();

        if ($images) {
            $response->data = ['images' => $images];
        } else {
            $response->data = ['images' => []];
        }

        return $response;
    }

    public function actionDelete()
    {
        $app = Yii::$app;
        $request = $app->request;
        $response = $app->response;
        $flysystem = $app->flysystem;
        $post = $request->post();
        $id = $post['id'];
        $image = Image::findOne($id);
        $path = $image->path;

        $response->format = Response::FORMAT_JSON;
        try {
            if (false !== $flysystem->has($path)) {
                if (false !== $flysystem->delete($path)) {
                    if (false !== $image->delete()) {
                        $response->data = ['status' => 'success'];
                        return $response;
                    }
                }
            }
            $response->statusCode = 500;
            return $response;
        } catch (ErrorException $e) {
            $response->statusCode = 500;
            $response->data = ['status' => 'fail', 'error' => $e];
            return $response;
        }
    }
}


