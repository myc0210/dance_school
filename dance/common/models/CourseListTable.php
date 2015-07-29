<?php
    namespace common\models;

    use Yii;
    use yii\base\Model;
    use yii\db\Query;

    /**
     * User Login
     */
    class CourseListTable extends Model
    {

        public function rules()
        {
            return [
            ];
        }

        public function courseList()
        {
            $courses = CourseVariation::find()
            ->joinWith('course')
            ->joinWith('branch')
            ->joinWith('teacher')
            ->select([
                'course_variation.id AS id',
                'course_variation.course_id',
                'course_variation.branch_id',
                'course_variation.teacher_id',
                'course.course_name',
                'course.course_type_id',
                'code',
                'start_date',
                'end_date',
                'lessons',
                'capacity',
                'start_time',
                'duration',
                'enrolled_number'
            ])
            ->where(['course_variation.status' => 10])
            ->asArray()
            ->all();

            $types = Coursetype::find()->where(['status' => 10])->asArray()->all();
            $typesAssoc = [];
            foreach ($types as $type) {
                $typesAssoc[$type['id']] = $type['name'];
            }

            for ($i = 0; $i < sizeof($courses); $i++) {
                $courses[$i]['type_name'] = $typesAssoc[$courses[$i]['course_type_id']];
            }

            return $courses;
        }

        public static function courseListGroupByBranch()
        {
            $query = new Query();

//            $courses = $query
//            ->select('distinct(course_id), branch_id, branch_name, showcase_detail')
//            ->from('course_variation')
//            ->leftJoin('course', 'course.id=course_variation.course_id')
//            ->leftJoin('branch', 'branch.id=course_variation.branch_id')
//            ->groupBy('branch_id')
//            ->all();

            $courses = $query
                ->select(['course_variation.course_id', 'course_variation.branch_id', 'course.course_type_id', 'course.course_name', 'course.showcase_detail', 'course.age_min', 'course.age_max', 'course.level', 'course.course_fee', 'course.mall_course_fee', 'course_type.name'])
                ->from('course_variation')
                ->leftJoin('school_branch', 'school_branch.id = course_variation.branch_id')
                ->leftJoin('course', 'course.id = course_variation.course_id')
                ->leftJoin('course_type', 'course_type.id = course.course_type_id')
                ->where(['course_variation.status' => 10])
                ->groupBy(['course_id','branch_id'])
                ->orderBy([
                    'course_variation.branch_id' => SORT_ASC,
                    'course.course_name' => SORT_ASC
                ])
                ->all();

            return $courses;
        }
    }
