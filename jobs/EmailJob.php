<?php
namespace app\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\Request;
use app\services\EmailService;
use Yii;

class EmailJob extends BaseObject implements JobInterface
{
    public $requestId;

    public function execute($queue)
    {
        $request = Request::findOne($this->requestId);
        if (!$request) {
            return;
        }
        $emailService = Yii::$container->get(EmailService::class);
        $emailService->sendRequestResolvedEmail($request);
    }
}
