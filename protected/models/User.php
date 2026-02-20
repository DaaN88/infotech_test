<?php

declare(strict_types=1);

/**
 * User model for table "users".
 */
class User extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            ['username, password_hash, role, created_at, updated_at', 'required'],
            ['username', 'length', 'max' => 64],
            ['password_hash', 'length', 'max' => 255],
            ['role', 'length', 'max' => 32],
        ];
    }
}
