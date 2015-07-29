<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;
    use yii\db\Query;

    class CourseVariation extends ActiveRecord
    {
        const STATUS_DELETED = 0;
        const STATUS_ACTIVE = 10;

        public static function tableName()
        {
            return '{{%course_variation}}';
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
                [['code', 'start_date', 'end_date', 'start_time', 'duration'], 'string'],
                [['course_id', 'branch_id', 'teacher_id', 'lessons', 'capacity', 'enrolled_number'], 'integer'],
                ['status', 'default', 'value' => self::STATUS_ACTIVE],
                ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ];
        }

        public function getCourse()
        {
            return $this->hasOne(Course::className(), ['id' => 'course_id']);
        }

        public function getBranch()
        {
            return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
        }

        public function getTeacher()
        {
            return $this->hasOne(TeacherProfile::className(), ['id' => 'teacher_id']);
        }

        public static function courseVariationGet($id)
        {
            return self::find()
                ->select([
                    'course_variation.id',
                    'course_id',
                    'course.course_type_id',
                    'course.course_name',
                    'course.gallery_detail',
                    'course.showcase_detail',
                    'course.age_min',
                    'course.age_max',
                    'course.level',
                    'course.course_fee',
                    'course.mall_course_fee',
                    'course.description',
                    'branch_id',
                    'teacher_id',
                    'code',
                    'start_date',
                    'end_date',
                    'lessons',
                    'capacity',
                    'start_time',
                    'duration',
                ])
                ->leftJoin('course', '`course`.`id` = `course_variation`.`course_id`')
                ->where(['course_variation.id' => $id , 'course_variation.status' => self::STATUS_ACTIVE])
                ->asArray()
                ->one();
        }

        public static function courseVariationUpdate($updatedCourseVariation)
        {
            $variationCourse = self::findOne($updatedCourseVariation['id']);

            return $variationCourse->save();
        }

        public static function courseVariationTerminate($id)
        {
            $variationCourse = self::findOne($id);
            $variationCourses = self::findAll(['course_id' => $variationCourse->course_id, 'status' => self::STATUS_DELETED]);

            if  (sizeof($variationCourses) == 1) {
                $parentCourse = Course::findOne($variationCourse->course_id);
                $parentCourse->status = self::STATUS_DELETED;
                $parentCourse->save();
            }

            $variationCourse->status = self::STATUS_DELETED;
            return $variationCourse->save();
        }

        public static function courseVariationGetByCourseAndBranch($courseId, $branchId)
        {
            $query = new Query();
            $courseVariations = $query->select(['course_variation.id as course_variation_id', 'course_id', 'branch_id', 'teacher_id', 'start_date', 'end_date', 'lessons', 'capacity', 'start_time', 'duration', 'CONCAT(teacher_profile.first_name, teacher_profile.last_name) AS teacher_name'])
            ->from('course_variation')
            ->leftJoin('teacher_profile', '`teacher_profile`.`id` = `course_variation`.`teacher_id`')
            ->where(['course_id' => $courseId, 'branch_id' => $branchId, 'course_variation.status' => 10])
            ->all();
            return $courseVariations;
        }
    }