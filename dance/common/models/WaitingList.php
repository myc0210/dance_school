<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class WaitingList extends ActiveRecord
    {

        public static function tableName()
        {
            return '{{%waiting_list}}';
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
                [['course_variation_id', 'user_id', 'course_variation_detail'], 'required'],
                [['course_variation_id', 'user_id'], 'integer'],
                [['course_variation_detail'], 'string']
            ];
        }
    }