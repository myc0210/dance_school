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
        public $schoolId;
        public $branchId;
        public $firstName;
        public $lastName;
        public $mobilePhone;
        public $homePhone;
        public $email;

        public function rules()
        {
            return [
                [['userId', 'schoolId', 'branchId', 'firstName', 'lastName', 'mobilePhone', 'homePhone', 'email'], 'required']
            ];
        }

        public function create()
        {
            $studentProfile = new StudentProfile();
            $studentProfile->user_id = $this->userId;
            $studentProfile->school_id = $this->schoolId;
            $studentProfile->branch_id = $this->branchId;
            $studentProfile->first_name = $this->firstName;
            $studentProfile->last_name = $this->lastName;
            $studentProfile->mobile_phone = $this->mobilePhone;
            $studentProfile->home_phone = $this->homePhone;
            if ($studentProfile->save()) {
                return true;
            }
            return false;
        }

        public function sendEmail()
        {
            $school = School::findOne($this->schoolId);
            $user = User::findOne($this->userId);
            $schoolEmail = $school->school_email;
            $schoolName = $school->school_name;
            return Yii::$app->mailer->compose()
                ->setTo($this->email)
                ->setFrom([$schoolEmail => $schoolName])
                ->setSubject('Create Your New Account')
                ->setHtmlBody('Click following url to create your account. <a
href="http://localhost/school_cart/app/init?token=' . $user->email_token . '">
Create</a>')
                ->send();
        }
    }
