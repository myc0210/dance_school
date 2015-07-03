<?php
    namespace common\models;

    use Yii;
    use yii\base\Model;

    /**
     * User Login
     */
    class StudentCreateForm extends Model
    {

        public $userId;
        public $firstName;
        public $lastName;
        public $mobilePhone;
        public $homePhone;

        public function rules()
        {
            return [
                [['userId', 'firstName', 'lastName', 'mobilePhone', 'homePhone'], 'required']
            ];
        }

        public function create()
        {
            $studentProfile = new StudentProfile();
            $studentProfile->user_id = $this->userId;
            $studentProfile->first_name = $this->firstName;
            $studentProfile->last_name = $this->lastName;
            $studentProfile->mobile_phone = $this->mobilePhone;
            $studentProfile->home_phone = $this->homePhone;
            if ($studentProfile->save()) {
                return true;
            }
            return false;
        }
    }
