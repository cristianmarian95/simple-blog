<?php


namespace App\Modules;

use \App\Main;

class Mail extends Main
{
    /**
     * @param $request
     * @param $email
     * @return bool
     */
    public function sendActivation($request, $email){
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: no-replay@'. $request->getUri()->getHost() . "\r\n";
        $headers .= 'Cc: admin@'. $request->getUri()->getHost() . "\r\n";
        $subject = $this->config->get('blogTitle') . ' | Confirm your account';
        $message = '<h4>Hello there!</h4>';
        $message .= 'Please confirm your account from <a href="' . $request->getUri()->getBaseUrl() . '" target="_blank">' . $this->config->get('blogTitle') . '</a><br />';
        $message .= 'To confirm your account click on the following link <a href="' . $request->getUri()->getBaseUrl() . $this->router->pathFor('active') . '/' . md5($email) .'">' . $request->getUri()->getBaseUrl() . $this->router->pathFor('active') . '/' . md5($email) .'</a><br />';
        $message .= '<b>If you did not register on our platform please ignore this message</b><br />';
        return mail($email,$subject,$message,$headers);
    }

    /**
     * @param $from
     * @param $subject
     * @param $message
     * @return bool
     */
    public function sendContactMail($from, $subject, $message){
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $from . "\r\n";
        $contact_email = $this->config->get('contactEmail');
        return mail($contact_email,$subject,$message,$headers);
    }

    /**
     * @param $request
     * @param $title
     * @param $post_id
     * @return bool
     */
    public function subscribeMail($request, $title, $post_id){
        $subscribers = $this->db->table('subscribers')->get();
        $subs_list = [];
        foreach($subscribers as $subscriber){
            $subs_list = $subs_list . $subscriber->email;
        }
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: no-replay@'. $request->getUri()->getHost() . "\r\n";
        $headers .= 'Cc: admin@'. $request->getUri()->getHost() . "\r\n";
        $subject = $this->config->get('blogTitle') . ' | ' . $title;
        $message = '<h4>Hello there!</h4>';
        $message .= 'Check our new content here <a href="' . $request->getUri()->getBaseUrl() . $this->router->pathFor('read') . '/' . $post_id .'">'. $title .'</a><br />';
        $message .= '<b>If you do not want to receive any more emails <a href="' . $request->getUri()->getBaseUrl() . $this->router->pathFor('unsubscribe') . '">Click here</a></b><br />';
        return mail($subs_list,$subject,$message,$headers);
    }

    /**
     * @param $request
     * @param $hash
     * @param $email
     * @return bool
     */
    public function sendRecoverMail($request, $hash, $email){
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: no-replay@'. $request->getUri()->getHost() . "\r\n";
        $headers .= 'Cc: admin@'. $request->getUri()->getHost() . "\r\n";
        $subject = $this->config->get('blogTitle') . ' | Lost password';

        $message = '<h4>Hello there!</h4>';
        $message .= 'Please click on the following link to change your password <br />';
        $message .= '<a href="' . $request->getUri()->getBaseUrl() . $this->router->pathFor('recoverPassword') . '/' . $hash .'">'. $request->getUri()->getBaseUrl() . $this->router->pathFor('recoverPassword') . '/' . $hash .'</a><br />';
        $message .= '<b>If you did not require to changer password please ignore this message</b><br />';
        return mail($email,$subject,$message,$headers);
    }
}