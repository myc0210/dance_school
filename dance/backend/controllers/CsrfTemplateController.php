<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Request;

/**
 * Template controller
 */
class CsrftemplateController extends Controller
{
    public $defaultAction = 'index';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->getRequest();
        $template = $request->get('url');
        $templatePath = Yii::getAlias('@webroot') . '/tpl/' . $template;

        if ($request instanceof Request && $request->enableCsrfValidation) {
            $element = '<myc-csrf-token parent-scope="csrf" value="' .
                $request->getCsrfToken() .
                '"></myc-csrf-token>';
        } else {
            return '';
        }

        require $templatePath;
        echo $element . '</div>';
    }
}