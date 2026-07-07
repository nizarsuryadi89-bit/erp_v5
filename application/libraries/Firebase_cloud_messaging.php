<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Firebase_cloud_messaging {
    protected $messaging;

    public function __construct() {
        $config_path = FCPATH . 'application/config/firebase_credentials.json';
        
        // Cek apakah file ada dan berisi kredensial valid (pengukuran kasar > 100 bytes)
        if (file_exists($config_path) && filesize($config_path) > 100) {
            try {
                $factory = (new Factory)
                    ->withServiceAccount($config_path);
                $this->messaging = $factory->createMessaging();
            } catch (\Exception $e) {
                log_message('error', 'FCM Library Init Error: ' . $e->getMessage());
                $this->messaging = null;
            }
        } else {
            log_message('debug', 'FCM Config missing or invalid. Notification skipped.');
            $this->messaging = null;
        }
    }

    public function send_notification($token, $title, $body, $data = []) {
        if (!$this->messaging) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }
}
