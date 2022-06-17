<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profiles".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $photo
 * @property string $address
 * @property string $bd
 * @property string $about
 * @property string $phone
 * @property string $skype
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['bd', 'address', 'bd', 'about', 'phone', 'skype'], 'safe'],
            [['about'], 'string'],
            [['photo', 'address', 'skype'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'photo' => 'Photo',
            'address' => 'Address',
            'bd' => 'Bd',
            'about' => 'About',
            'phone' => 'Phone',
            'skype' => 'Skype',
        ];
    }

    /**
     * calculate age
     * @return integer
     */
    public function getAge()
    {
        $diff = substr(date( 'Ymd' ) - date( 'Ymd', strtotime($this->bd) ), 0, -4);

        return Yii::t('app', '{delta, plural, one{# год} few{# года} many{# лет} other{# года}}', ['delta' => $diff]);
    }

    /**
     * Prepare thumbnail for profile
     * @return string
     */
    public function getThumb()
    {
        if ($this->photo) {
            return $this->photo;
        } else {
            return '/img/man-glasses.png';
        }
    }
}
