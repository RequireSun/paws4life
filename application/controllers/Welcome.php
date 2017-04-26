<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function config() {
		var_dump($this->config->item('mail_username'));
	}

	public function mail() {
		require_once APPPATH . 'libraries/phpMailer/PHPMailerAutoload.php';
		require_once APPPATH . 'libraries/phpMailer/class.phpmailer.php';

		$mail = new PHPMailer();

		$body= "发送邮件成功";
		//采用SMTP发送邮件
		$mail->IsSMTP();

		//邮件服务器
		$mail->Host       = "smtp.126.com";
		$mail->SMTPDebug  = 0;

		//使用SMPT验证
		$mail->SMTPAuth   = true;

		//SMTP验证的用户名称
		$mail->Username   = $this->config->item('mail_username');

		//SMTP验证的秘密
		$mail->Password   = $this->config->item('mail_password');//密码

		//设置编码格式
		$mail->CharSet  = "utf-8";

		//设置主题
		$mail->Subject    = "测试";

		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";

		//设置发送者
		$mail->SetFrom('paws4life@126.com', 'test');

		//采用html格式发送邮件
		$mail->MsgHTML($body);

		//接受者邮件名称
		$mail->AddAddress("862683427@qq.com", "test");//发送邮件
		if(!$mail->Send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
		}
	}
}
