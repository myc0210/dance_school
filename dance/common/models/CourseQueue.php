<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class CourseQueue extends ActiveRecord
    {

        public static function tableName()
        {
            return '{{%course_queue}}';
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
                [['user_id', 'course_id'], 'required']
            ];
        }
    }