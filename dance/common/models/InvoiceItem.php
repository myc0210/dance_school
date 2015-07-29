<?php
    namespace common\models;

    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class InvoiceItem extends ActiveRecord
    {

        public static function tableName()
        {
            return '{{%invoice_item}}';
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

            ];
        }
    }