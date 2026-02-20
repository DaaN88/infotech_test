<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel
{
	public $name;
	public $email;
	public $subject;
	public $body;
	public $verifyCode;

    private function stripNewlines(string $value): string
    {
        return str_replace(["\r", "\n"], '', $value);
    }

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return [
            ['name, email, subject', 'filter', 'filter' => [$this, 'stripNewlines']],
			// name, email, subject and body are required
			['name, email, subject, body', 'required'],
			// email has to be a valid email address
			['email', 'email'],
			// verifyCode needs to be entered correctly
			['verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()],
		];
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return [
			'verifyCode'=>Yii::t('app', 'contact.label.verifyCode'),
		];
	}
}
