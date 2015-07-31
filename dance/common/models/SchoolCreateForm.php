<?php
    namespace common\models;

    use Yii;
    use yii\base\Model;

    /**
     * User Login
     */
    class SchoolCreateForm extends Model
    {

        public $name;
        public $email;

        public function rules()
        {
            return [
                [['name', 'email'], 'required'],
            ];
        }

        public function create()
        {
            $school = new School();
            $school->school_name = $this->name;
            $school->school_email = $this->email;

            if ($school->save()) {
                return true;
            }
            return false;
        }
    }
