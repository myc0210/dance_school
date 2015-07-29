<?php
    namespace common\models;

    use Yii;
    use yii\base\Model;

    /**
     * User Login
     */
    class SchoolListTable extends Model
    {

        public function rules()
        {
            return [
            ];
        }

        public function schoolList()
        {
            return School::find()->where(['status' => 10])->all();
        }
    }
