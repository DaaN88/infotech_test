<?php

declare(strict_types=1);

/**
 * Subscription model for author notifications.
 *
 * @property int $id
 * @property int $author_id
 * @property int|null $user_id
 * @property string $phone
 * @property string $name
 * @property string $created_at
 *
 * @property Author $author
 * @property User|null $user
 */
class Subscription extends CActiveRecord
{
    public static function model($className = __CLASS__): self
    {
        return parent::model($className);
    }

    public function tableName(): string
    {
        return 'subscriptions';
    }

    public function relations(): array
    {
        return [
            'author' => [self::BELONGS_TO, 'Author', 'author_id'],
            'user' => [self::BELONGS_TO, 'User', 'user_id'],
        ];
    }

    public function rules(): array
    {
        return [
            ['author_id', 'required', 'message' => Yii::t('app', 'subscription.author.required')],
            ['phone', 'required', 'message' => Yii::t('app', 'subscription.phone.required')],
            ['name', 'required', 'message' => Yii::t('app', 'subscription.name.required')],
            ['author_id, user_id', 'numerical', 'integerOnly' => true],
            ['phone', 'length', 'max' => 32],
            ['name', 'length', 'max' => 255],
            [
                'phone',
                'match',
                'pattern' => '/^\\+?[0-9]{10,15}$/',
                'message' => Yii::t('app', 'subscription.phone.invalid')
            ],
            [
                'phone',
                'unique',
                'criteria' => [
                    'condition' => 'author_id=:author_id AND phone=:phone',
                    'params' => [],
                ],
                'message' => Yii::t('app', 'subscription.phone.unique'),
            ],
        ];
    }

    public function beforeValidate(): bool
    {
        foreach ($this->validatorList as $validator) {
            if ($validator instanceof CUniqueValidator && $validator->attributes === ['phone']) {
                if (is_array($validator->criteria)) {
                    $validator->criteria = new CDbCriteria($validator->criteria);
                }

                $validator->criteria->params = [
                    ':author_id' => $this->author_id,
                    ':phone' => $this->phone,
                ];
            }
        }

        return parent::beforeValidate();
    }

    public function attributeLabels(): array
    {
        return [
            'author_id' => Yii::t('app', 'subscription.label.author'),
            'phone' => Yii::t('app', 'subscription.label.phone'),
            'name' => Yii::t('app', 'subscription.label.name'),
        ];
    }
}
