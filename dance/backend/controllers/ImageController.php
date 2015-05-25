<?php
namespace backend\controllers;

use Yii;
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
            $fileName = time() . '.' . $image->file->extension;
            $flysystem->writeStream($fileName, $stream);
            fclose($stream);
            $image->name = $image->file->baseName;
            $image->path = '/' . Yii::getAlias('@upload') . '/' . $fileName;
            if(!$image->save()) {
                $response->statusCode = 500;
            }
            return $response;
        }
    }

    public function actionList()
    {
        $app = Yii::$app;
        $response = $app->response;
        $response->format = Response::FORMAT_JSON;

        $images = Image::findAll([]);

        if ($images) {
            $response->data = ['images' => $images];
        } else {
            $response->data = ['images' => []];
        }

        return $response;
    }

    public function actionDelete()
    {

    }
}


