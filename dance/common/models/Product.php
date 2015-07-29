<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%product}}';
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
            [['name', 'variation_detail', 'gallery_detail', 'showcase_detail'], 'required'],
            [['name', 'variation_detail', 'gallery_detail', 'showcase_detail'], 'string'],
            [['category_id', 'subcategory_id'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public function getVariations()
    {
        return $this->hasMany(ProductVariation::className(), ['product_id' => 'id']);
    }
}