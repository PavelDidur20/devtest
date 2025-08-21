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
            
            
            if (!file_exists($mailPath)) {
                mkdir($mailPath, 0777, true);
            }
            
           
            $filename = $mailPath . '/request_' . $request->id . '_' . time() . '.eml';
            $content = $this->buildEmailContent($request);
            
            file_put_contents($filename, $content);
            
            Yii::info("Email сохранен: $filename", 'request');
            
            return true;
        } catch (\Exception $e) {
            Yii::error("Не получилось сохранить емайл: " . $e->getMessage(), 'request');
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
        $content .= "Доброго времени!" . $request->name . ",\n\n";
        $content .= "Ваша заявка была обработана с коментарием: \n\n";
        $content .= $request->comment . "\n\n";
        $content .= "С лучшими пожеланиями,\nООО Рога и Копыта Адвансед Технолоджиес";
        
        return $content;
    }
}