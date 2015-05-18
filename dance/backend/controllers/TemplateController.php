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
class TemplateController extends Controller
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
        $templatePath = Yii::getAlias('@webroot') . '/angular/tpl/' . $template;

        if ($request instanceof Request && $request->enableCsrfValidation) {
            $element = '<csrf-token parent-scope="csrf" value="' .
                $request->getCsrfToken() .
                '">';
        } else {
            return '';
        }

        require $templatePath;
        echo $element . '</div>';
    }
}