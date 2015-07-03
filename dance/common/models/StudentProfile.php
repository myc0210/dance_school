<?php
    namespace common\models;

    use Yii;
    use yii\base\NotSupportedException;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;
    use yii\web\IdentityInterface;

    class StudentProfile extends ActiveRecord
    {
        public static function tableName()
        {
            return '{{%student_profile}}';
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
