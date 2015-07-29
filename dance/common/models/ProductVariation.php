<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ProductVariation extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%product_variation}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'color', 'level', 'size'], 'string'],
            [['product_id', 'quantity'], 'integer'],
            ['quantity', 'default', 'value' => 0],
            [['cost', 'price'], 'number'],
            [['cost', 'price'], 'default', 'value' => 0.00],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public static function productVariationGet($id)
    {
        return self::find()
            ->select([
                'product_variation.id',
                'product_variation.product_id',
                'product.category_id',
                'product.subcategory_id',
                'product.name',
                'code',
                'color',
                'level',
                'size',
                'cost',
                'price',
                'quantity',
                'product.description',
                'product.gallery_detail'
            ])
            ->leftJoin('product', '`product`.`id` = `product_variation`.`product_id`')
            ->where(['product_variation.id' => $id , 'product_variation.status' => self::STATUS_ACTIVE])
            ->asArray()
            ->one();
    }

    public static function productVariationUpdate($updatedProductVariation)
    {
        $variationProduct = self::findOne($updatedProductVariation['id']);
        $variationProduct->code = $updatedProductVariation['code'];
        $variationProduct->level = $updatedProductVariation['level'];
        $variationProduct->color = $updatedProductVariation['color'];
        $variationProduct->size = $updatedProductVariation['size'];
        $variationProduct->cost = $updatedProductVariation['cost'];
        $variationProduct->price = $updatedProductVariation['price'];
        $variationProduct->quantity = $updatedProductVariation['quantity'];

        return $variationProduct->save();
    }

    public static function productVariationTerminate($id)
    {
        $variationProduct = self::findOne($id);
        $variationProducts = self::findAll(['product_id' => $variationProduct->product_id, 'status' => self::STATUS_DELETED]);

        if  (sizeof($variationProducts) == 1) {
            $parentProduct = Product::findOne($variationProduct->product_id);
            $parentProduct->status = self::STATUS_DELETED;
            $parentProduct->save();
        }

        $variationProduct->status = self::STATUS_DELETED;
        return $variationProduct->save();
    }
}