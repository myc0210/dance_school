<?php
    namespace backend\controllers;

    use Yii;
    use yii\base\Exception;
    use yii\filters\AccessControl;
    use yii\web\Controller;
    use common\models\Product;
    use common\models\ProductVariation;
    use common\models\CategoryProduct;
    use common\models\ProductListTable;
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

        public function actionProductList() {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;

            $productListModel = new ProductListTable();
            $response->data = $productListModel->productList();
        }

        public function actionProductSave()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();
            $post = $request->post();
            $product = $post['product'];
            $importData = [];
            $importData['name'] = $product['name'];
            $importData['description'] = $product['description'];
            $gallery_detail = [];
            $errorMsg = [];

            try {
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
                        if (isset($attachedImage['default']) && $attachedImage['default'] == TRUE) {
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
                        function array_column($array, $column_name)
                        {
                            return array_map(function ($element) use ($column_name) {
                                return $element[$column_name];
                            }, $array);
                        }
                    }

                    $priceList = array_column($product['variationProducts'], 'price');
                    $priceRange = $this->getPriceRange($priceList);

                } else {
                    $errorMsg[] = 'You forget to create product details.';
                }

                if (sizeof($errorMsg) == 0) {
                    $importData['showcase_detail'] = json_encode(['defaultImage' => $defaultImage,
                                                                  'priceRange'   => $priceRange]);
                }

                $productModel = new Product();
                if ($productModel->load($importData, '') && $productModel->save()) {
                    $productId = $productModel->id;
                } else {
                    throw new Exception('Product create fail.');
                }

                foreach ($product['variationProducts'] as $variationProduct) {
                    $variationProductModel = new ProductVariation();
                    $variationData = $variationProduct;
                    $variationData['product_id'] = $productId;
                    if (!($variationProductModel->load($variationData, '') && $variationProductModel->save())) {
                        throw new Exception('Product variation create fail.');
                    }
                }

                $transaction->commit();

            } catch (Exception $e) {

                $transaction->rollBack();
                $response->statusCode = 500;
                $response->data = $e->getMessage();

            }
        }

        public function actionProductGet()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $variationProductId = $request->post('variationProductId');
            if ($product = ProductVariation::productVariationGet($variationProductId)) {
                $response->data = $product;
            } else {
                $response->statusCode = 500;
            }
        }

        public function actionProductUpdate()
        {
            $app = Yii::$app;
            $request = $app->getRequest();
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $product = $request->post('product');
            $response->data = $product;
            $connection = $app->getDb();
            $transaction = $connection->beginTransaction();
            $importData = [];
            $importData['name'] = $product['name'];
            $importData['description'] = $product['description'];
            $gallery_detail = [];
            $errorMsg = [];

            try {
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
                        if (isset($attachedImage['default']) && $attachedImage['default'] == TRUE) {
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
                        function array_column($array, $column_name)
                        {
                            return array_map(function ($element) use ($column_name) {
                                return $element[$column_name];
                            }, $array);
                        }
                    }

                    $priceList = array_column($product['variationProducts'], 'price');
                    $priceRange = $this->getPriceRange($priceList);

                } else {
                    $errorMsg[] = 'You forget to create product details.';
                }

                if (sizeof($errorMsg) == 0) {
                    $importData['showcase_detail'] = json_encode(['defaultImage' => $defaultImage,
                                                                  'priceRange'   => $priceRange]);
                }

                $productModel = Product::findOne($product['id']);
                if ($productModel->load($importData, '') && $productModel->save()) {
                    $productId = $product['id'];
                } else {
                    throw new Exception('Product create fail.');
                }

                $updatedVariationProduct = $product['variationProducts'][0];
                unset($product['variationProducts'][0]);

                if (!ProductVariation::productVariationUpdate($updatedVariationProduct)) {
                    throw new Exception('Variation product update fail.');
                }

                foreach ($product['variationProducts'] as $variationProduct) {
                    $variationProductModel = new ProductVariation();
                    $variationData = $variationProduct;
                    $variationData['product_id'] = $productId;
                    if (!($variationProductModel->load($variationData, '') && $variationProductModel->save())) {
                        throw new Exception('Product variation create fail.');
                    }
                }

                $transaction->commit();

            } catch (Exception $e) {
                $transaction->rollBack();
                $response->statusCode = 500;
                $response->data = $e->getMessage();
            }
        }

        public function actionProductTerminate()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $variationProductId = $request->post('variationProductId');
            if (ProductVariation::productVariationTerminate($variationProductId)) {
                $response->data = ['state' => 'success'];
            } else {
                $response->statusCode = 500;
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

        public function actionProductsDisplay()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $response->data = ['products' => ProductListTable::productListDisplay()];
        }

        public function actionProductDetail()
        {
            $app = Yii::$app;
            $response = $app->getResponse();
            $response->format = Response::FORMAT_JSON;
            $request = $app->getRequest();
            $productId = $request->post('productId');
            $product = Product::findOne($productId);
            $productVariations = $product->variations;

            $response->data = ['product' => $product, 'variations' => $productVariations];
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

            return [$min, $max];
        }
    }
