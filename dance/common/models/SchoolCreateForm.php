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
        public $display;

        public function rules()
        {
            return [
                [['name', 'display'], 'required'],
            ];
        }

        public function create()
        {
            $school = new School();
            $school->school_name = $this->name;
            $school->shopping_cart_display = $this->display;

            if ($school->save()) {
                return true;
            }
            return false;
        }
    }
