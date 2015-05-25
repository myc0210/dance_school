<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Image extends ActiveRecord
{
    public $file;

    public static function tableName()
    {
        return '{{%image}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'gif, jpg, png'],
        ];
    }
}