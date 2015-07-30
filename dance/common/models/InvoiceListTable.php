<?php
    namespace common\models;

    use Yii;
    use yii\base\Model;

    /**
     * User Login
     */
    class InvoiceListTable extends Model
    {

        public function rules()
        {
            return [
            ];
        }

        public function invoiceList()
        {

        }

        public static function invoiceListByUser($userId)
        {
            return Invoice::find()->select(['id', 'invoice_no', 'FROM_UNIXTIME(created_at) AS time', 'total', 'status'])->with('frontendInvoiceItems')->where('user_id = :user_id AND status != 0', ['user_id'=>$userId])->orderBy(['status' => SORT_ASC, 'created_at' => SORT_ASC])->asArray()->all();
        }
    }
