<?php

class MailTest extends \plainview\sdk_pvam\tests\TestCase
{
	public function test_mail()
	{
		$mail = new \plainview\sdk_pvam\mail\mail;
		$this->assertTrue( is_a( $mail, '\\PHPMailer' ) );
	}
}
