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
        return array(
            array('username, password_hash, role, created_at, updated_at', 'required'),
            array('username', 'length', 'max' => 64),
            array('password_hash', 'length', 'max' => 255),
            array('role', 'length', 'max' => 32),
        );
    }
}
