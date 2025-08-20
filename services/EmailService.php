<?php
// services/EmailService.php

namespace app\services;

use Yii;
use app\models\Request;

class EmailService
{
    /**
     * Отправляет email о разрешении заявки
     * 
     * @param Request $request
     * @return bool
     */
    public function sendRequestResolvedEmail(Request $request)
    {
        try {
            $mailPath = Yii::getAlias('@runtime/mail');
            
            // Создаем директорию, если не существует
            if (!file_exists($mailPath)) {
                mkdir($mailPath, 0777, true);
            }
            
            // Сохраняем email в файл (вместо реальной отправки)
            $filename = $mailPath . '/request_' . $request->id . '_' . time() . '.eml';
            $content = $this->buildEmailContent($request);
            
            file_put_contents($filename, $content);
            
            Yii::info("Email saved to: $filename", 'request');
            
            return true;
        } catch (\Exception $e) {
            Yii::error("Failed to save email: " . $e->getMessage(), 'request');
            return false;
        }
    }
    
    /**
     * Формирует содержимое email
     * 
     * @param Request $request
     * @return string
     */
    protected function buildEmailContent(Request $request)
    {
        $content = "To: " . $request->email . "\n";
        $content .= "Subject: Your request has been resolved\n\n";
        $content .= "Hello " . $request->name . ",\n\n";
        $content .= "Your request has been resolved. Here is our comment:\n\n";
        $content .= $request->comment . "\n\n";
        $content .= "Best regards,\nSupport Team";
        
        return $content;
    }
}