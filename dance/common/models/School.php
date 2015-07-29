<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class School extends ActiveRecord
    {
        const STATUS_DELETED = 0;
        const STATUS_ACTIVE = 10;

        public static function tableName()
        {
            return '{{%school}}';
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
                ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]]
            ];
        }

        public function schoolGet($schoolId)
        {
            $school = static::findOne($schoolId);

            if ($school) {
                return $school;
            }

            return false;
        }

        public function schoolTerminate($schoolId)
        {
            $school = static::findOne($schoolId);
            $school->status = self::STATUS_DELETED;

            if ($school->save()) {
                return $school;
            }

            return false;
        }

        public function schoolUpdate($schoolId, $schoolInfo)
        {
            $school = static::findOne($schoolId);
            $school->school_name = $schoolInfo['name'];
            $school->shopping_cart_display = $schoolInfo['display'];

            if ($school->save()) {
                return true;
            }

            return false;
        }
    }
