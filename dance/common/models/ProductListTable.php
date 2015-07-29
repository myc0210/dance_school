<?php
    namespace common\models;

    use Yii;
    use yii\base\Model;

    /**
     * User Login
     */
    class ProductListTable extends Model
    {

        public function rules()
        {
            return [
            ];
        }

        public function productList()
        {
            $products = ProductVariation::find()->joinWith('product')->select(['product_variation.id AS id', 'product_variation.product_id AS product_id', 'name', 'product.category_id AS category_id', 'product.subcategory_id AS subcategory_id', 'color', 'level', 'code', 'size', 'cost', 'price', 'quantity'])->where(['product_variation.status' => 10])->asArray()->all();
            $categories = CategoryProduct::find()->where(['status' => 10])->asArray()->all();
            $categoriesAssoc = [];
            foreach ($categories as $category) {
                $categoriesAssoc[$category['id']] = $category['name'];
            }

            for ($i = 0; $i < sizeof($products); $i++) {
                $products[$i]['category_name'] = $categoriesAssoc[$products[$i]['category_id']];
                $products[$i]['subcategory_name'] = ($products[$i]['subcategory_id'] > 0 ? $categoriesAssoc[$products[$i]['subcategory_id']] : 'N/A');
            }

            return $products;
        }

        public static function productListDisplay()
        {
            $products = Product::find()->select(['id', 'category_id', 'name', 'showcase_detail'])->where(['status' => 10])->orderBy(['name' => SORT_ASC])->all();
            return $products;
        }
    }
