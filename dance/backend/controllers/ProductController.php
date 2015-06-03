<?php
    namespace backend\controllers;

    use Yii;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\Product;
    use common\models\CategoryProduct;
    use yii\filters\VerbFilter;
    use yii\web\Response;
    use yii\filters\auth\HttpBearerAuth;


    /**
     * Site controller
     */
    class ProductController extends Controller
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

        public function actionProductSave()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $post = $request->post();
            $product = $post['product'];

            $importData = [];
            $importData['name'] = $product['name'];
            $importData['description'] = $product['description'];
            $gallery_detail = [];
            $errorMsg = [];

            if (isset($product['category']['id'])) {
                $importData['category_id'] = $product['category']['id'];
            } else {
                $errorMsg[] = 'You forget to select category for this product';
            }

            if (isset($product['subcategory']['id'])) {
                $importData['subcategory_id'] = $product['subcategory']['id'];
            } else {
                $importData['subcategory_id'] = 0;
            }

            if (isset($product['attachedImages']) && sizeof($product['attachedImages']) > 0) {
                foreach ($product['attachedImages'] as $attachedImage) {
                    if (isset($attachedImage['default']) && $attachedImage['default'] == true) {
                        $defaultImage = $attachedImage['path'];
                        array_unshift($gallery_detail, $attachedImage);
                    } else {
                        $gallery_detail[] = $attachedImage;
                    }
                }
                $importData['gallery_detail'] = json_encode($gallery_detail);
            } else {
                $errorMsg[] = 'You forget to select image for this product';
            }

            if (isset($product['variationProducts']) && sizeof($product['variationProducts']) > 0) {
                $importData['variation_detail'] = json_encode($product['variationProducts']);
                if (!function_exists("array_column")) {
                    function array_column($array,$column_name)
                    {
                        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
                    }
                }

                $priceList = array_column($product['variationProducts'], 'price');
                $priceRange = $this->getPriceRange($priceList);

            } else {
                $errorMsg[] = 'You forget to create product details.';
            }

            if (sizeof($errorMsg) == 0) {
                $importData['showcase_detail'] = json_encode(array('defaultImage' => $defaultImage,
                                                                   'priceRange' => $priceRange));
            }

            $product = new Product();
            if ($product->load($importData, '') && $product->save()) {
                $response->data = ['product' => $importData];
            } else {
                $response->statusCode = 500;
                $response->data = ['error' => $errorMsg];
            }
        }

        public function actionCategoryUpdate()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $post = $request->post();
            $categoryHierarchyList = $post['categories'];
            $categoryModel = new CategoryProduct();
            $categoryFlatList = $categoryModel->updateCategoryList($categoryHierarchyList);
            $response->data = ['categories' => $categoryFlatList];
        }

        public function actionCategoryList()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $categoryModel = new CategoryProduct();
            $categoryHierarchyList = $categoryModel->getCategoryList();
            $response->data = ['categories' => $categoryHierarchyList['categories'], 'maxId' =>
            $categoryHierarchyList['maxId']];
        }

        public function getPriceRange(array $priceList)
        {
            $min = $priceList[0];
            $max = $min;

            foreach ($priceList as $price) {
                if ($price > $max) {
                    $max = $price;
                } else if ($price < $min) {
                    $min = $price;
                }
            }

            return array($min, $max);
        }
    }
