<?php
require('../includes/class.phpmailer.php');
Class HRM_Mail
{
    public function send_mail($to,$subject,$content)
    {
        
        $mail = new PHPMailer();
        $ret ='';
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port     = 587;
        $mail->Username = "daxishrms@gmail.com";
        $mail->Password = "Easypos123#";
        $mail->Host     = "smtp.gmail.com";
        $mail->Mailer   = "smtp";
        $mail->SetFrom("daxishrms@gmail.com", "HRMS");
        $mail->AddReplyTo($to, "HRMS");
        $mail->AddAddress($to);
        $mail->Subject = $subject;
        $mail->WordWrap   = 80;
        $mail->MsgHTML($content);
        $mail->IsHTML(true);

        if(!$mail->Send())
        {
            $result=['status'=>'FAIL','msg'=>'Mail sending failed'];
        }
        else
        {
            $result=['status'=>'OK','msg'=>$to];
        }
        return $result;
    }


}