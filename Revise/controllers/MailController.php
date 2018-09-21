<?php

namespace Controllers;

class MailController {

    public $mailer;

    public function __construct()
    {     
        // Create the Transport
        $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))  // 邮件服务器IP地址和端口号
        ->setUsername('supinsheng@126.com')  // 发邮件账号
        ->setPassword('supinsheng123')  // 授权码
        ;

        // 创建发邮件对象
        $this->mailer = new \Swift_Mailer($transport);
    }

    public function index(){

        view('mail.index');
    }

    public function send(){

        $to = $_POST['to'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        $data = explode("@",$to);

        $to = [$to,$data[0]];

        // Create a message
        $message = (new \Swift_Message('Wonderful Subject'))
        ->setSubject($title) //  标题
        ->setFrom(['supinsheng@126.com' => '酥品笙']) // 发件人
        ->setTo([$to[0], $to[0] => $to[1]])    // 收件人
        ->setBody($content, 'text/html') // 邮件内容及邮件内容类型
        ;

        // Send the message
        $result = $this->mailer->send($message);
    }
}