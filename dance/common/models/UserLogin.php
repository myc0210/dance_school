<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * User Login
 */
class UserLogin extends Model
{
    public $username;
    public $nric;
    public $password;

    private $_user = FALSE;
    private $_accessToken = FALSE;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'nric'], 'safe'],
        ];
    }

    public function getStudentByNRIC()
    {
        if ($this->_user === false) {
            $this->_user = User::findByNRIC($this->nric);
            if (!empty($this->_user)) {
//                var_dump($this->_user);
                $userRole = $this->getRole();
                if ($userRole == 'relative') {
                    $relative = RelativeProfile::find()->with('student')->where(['user_id' => $this->_user->id, 'status' => 10])->one();
                    return $relative->student;
                } else if ($userRole == 'student') {
                    return $this->_user;
                }
            }
        }
        return $this->_user;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username|email and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->generateAccessToken();
            if ($user->save()) {
                $this->_accessToken = $user->access_token;
                return true;
            }
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
            if ($this->_user === null) {
                $this->_user = User::findByEmail($this->username);
            }
        }
        return $this->_user;
    }

    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    public function getRole()
    {
        $user = $this->getUser();
        return current(Yii::$app->authManager->getRolesByUser($user->id))->name;
    }
}
