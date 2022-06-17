<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Html;
use Taskforce\services\TaskInterface;

/**
 * RegistrationForm is the model behind the registration form.
 *
 * @property-read User|null $user
 *
 */
class RegistrationForm extends Model
{
    public $name;
    public $email;
    public $city_id;
    public $password_field;
    public $password_repeat;
    public $responsible;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'password_field', 'password_repeat', 'city_id', 'email'], 'required'],
            ['responsible', 'boolean'],
            [['password_field'], 'string', 'max' => 30, 'min' => 8],
            [
                'password_repeat',
                'compare',
                'compareAttribute' => 'password_field',
                'message' => "Пароли должны совпадать"
            ],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],

        ];
    }

    /**
     * Create new user
     * @return bool whether the user is saved in successfully
     */
    public function register(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = new User;
            $user->type = $this->responsible ? TaskInterface::TYPE_EXECUTOR : TaskInterface::TYPE_CUSTOMER;
            $user->email = $this->email;
            $user->name = $this->name;
            $user->password_field = $this->password_field;
            $user->city_id = $this->city_id;
            $user->load($this->attributes, '');
            if ($user->save()) {
                $profile = new Profile(['user_id' => $user->id]);
                $profile->save();
            }
        } catch (\Exception $ex) {
            $transaction->rollback();
            return false;
        }

        $transaction->commit();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Ваше имя',
            'surname' => 'Фамилия',
            'password_field' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'city_id' => 'Город',
            'description' => 'Описание',
            'created' => 'Created',
            'last_visited' => 'Last Visited',
            'type' => 'Type',
            'responsible' => 'я собираюсь откликаться на заказы'
        ];
    }
}
