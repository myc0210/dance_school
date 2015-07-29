<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class RelativeProfile extends ActiveRecord
    {
        const STATUS_DELETED = 0;
        const STATUS_ACTIVE = 10;

        public static function tableName()
        {
            return '{{%relative_profile}}';
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
                ['status', 'default', 'value' => self::STATUS_ACTIVE],
                ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ];
        }

        public function getUser()
        {
            return $this->hasOne(User::className(), ['id' => 'user_id']);
        }

        public function getStudent()
        {
            return $this->hasOne(StudentProfile::className(), ['id' => 'student_id']);
        }

        public static function terminate($relativeId)
        {
            $relative = static::findOne($relativeId);
            $relative->status = self::STATUS_DELETED;

            if ($relative->save()) {
                return $relative;
            }

            return false;
        }
    }
