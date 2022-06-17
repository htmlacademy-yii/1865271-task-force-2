<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string|null $surname
 * @property string $password
 * @property int|null $city_id
 * @property string|null $description
 * @property string $created
 * @property string|null $last_visited
 * @property string $type
 * @property string $auth_key
 * @property Category[] $Categories
 * @property ExecutorPhoto[] $executorPhotos
 * @property Response[] $responses
 * @property Task[] $tasks
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $password_field;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'name', 'type'], 'required'],
            [['city_id'], 'integer'],
            [['city_id'], 'exist', 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['description', 'type'], 'string'],
            [['created', 'last_visited'], 'safe'],
            [['score'], 'double'],
            ['email', 'email'],
            ['email', 'unique'],
            [['name', 'surname'], 'string', 'max' => 100],
            [['password_field'], 'string', 'max' => 30, 'min' => 8],
        ];
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
            'password' => 'Пароль',
            'city_id' => 'Город',
            'description' => 'Описание',
            'created' => 'Created',
            'last_visited' => 'Last Visited',
            'type' => 'Type'
        ];
    }

    /**
     * Gets query for Profile
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Gets user rating
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatings()
    {
        return $this->hasMany(Rating::className(), ['response_id' => 'id'])->viaTable('responses', ['user_id' => 'id']);
    }

    /**
     * Gets user rating count string
     *
     * @return string
     */
    public function getRatingsCountLabel()
    {
        return Yii::t(
            'app',
            '{delta, plural, 0 {нет отзывов} one{# отзыв} few{# отзыва} many{# отзывов} other{# отзывов}}',
            ['delta' => count($this->ratings)]
        );
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Gets score
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScoreCalculation()
    {
        return $this->hasMany(Rating::className(), ['response_id' => 'id'])->viaTable('responses', ['user_id' => 'id'])->average('rating');
    }

    /**
     * Gets place
     *
     * @return integer
     */
    public  function getPlace()
    {
        $users = User::find()->orderBy(['score' => SORT_DESC])->all();
        foreach ($users as $k => $user) {
            if ($this->id == $user->id) {
                return $k;
            }
        }
        return count($users);
    }

    /**
     * Gets full name
     *
     * @return string
     */
    public  function getFullname()
    {
       return $this->name . " " . $this->surname;
    }

    /**
     * Gets count succesfull tasks
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountSuccessfullTasks()
    {
        return $this->hasMany(Rating::className(), ['response_id' => 'id'])->viaTable('responses', ['user_id' => 'id'])->andWhere(['>','rating', '0'])->count();
    }

    /**
     * Gets count fail tasks
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountFailTasks()
    {
        return $this->hasMany(Rating::className(),  ['response_id' => 'id'])->viaTable('responses', ['user_id' => 'id'])->andWhere(['rating' => '0'])->count();
    }

    /**
     * Gets query for [[ExecutorCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable('executor_categories', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ExecutorPhotos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorPhotos()
    {
        return $this->hasMany(ExecutorPhoto::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Response::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['user_id' => 'id']);
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email)
    {
        return User::find()->where(['email' => $email])->one();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function beforeSave($insert)
    {
        if ($this->password_field)
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password_field);
        if (!$this->auth_key) {
            $this->auth_key = time();
        }
        return parent::beforeSave($insert);
    }
}
