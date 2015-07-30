<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class CourseStudent extends ActiveRecord
    {

        public static function tableName()
        {
            return '{{%course_student}}';
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
                [['course_variation_id', 'user_id'], 'required']
            ];
        }
    }