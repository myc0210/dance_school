<?php
    namespace common\models;

    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class Invoice extends ActiveRecord
    {

        public static function tableName()
        {
            return '{{%invoice}}';
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
                [['school_id', 'invoice_no', 'grand_total', 'total', 'payment_type', 'status'], 'required'],
                [['school_id', 'payment_type', 'status'], 'integer'],
                [['grand_total', 'total'], 'number']
            ];
        }

        public function getInvoiceItems()
        {
            return $this->hasMany(InvoiceItem::className(), ['invoice_id' => 'id']);
        }
    }