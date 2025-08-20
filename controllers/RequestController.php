<?php
// controllers/RequestController.php

namespace app\controllers;

use Yii;
use app\models\Request;
use app\models\User;
use app\services\RequestService;
use app\services\EmailService;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class RequestController extends ActiveController
{
    public $modelClass = 'app\models\Request';
    
    private $requestService;
    private $emailService;
    
    public function __construct($id, $module, RequestService $requestService, EmailService $emailService, $config = [])
    {
        $this->requestService = $requestService;
        $this->emailService = $emailService;
        parent::__construct($id, $module, $config);
    }
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Удаляем authenticator для создания заявок (публичный доступ)
        unset($behaviors['authenticator']);
        
        // Включаем CORS
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ],
        ];
        
        // Аутентификация через Basic Auth для защищенных методов
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'except' => ['create', 'options'],
            'auth' => function ($username, $password) {
                $user = User::findByUsername($username);
                if ($user && $user->validatePassword($password)) {
                    return $user;
                }
                return null;
            }
        ];
        
        // Права доступа
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['index', 'view', 'update'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'], // Только аутентифицированные пользователи
                ],
            ],
        ];
        
        return $behaviors;
    }
    
    public function actions()
    {
        $actions = parent::actions();
        
        // Настраиваем actionIndex для использования сервиса
        $actions['index']['prepareDataProvider'] = function ($action) {
            $filterParams = Yii::$app->request->get();
            return $this->requestService->getFilteredDataProvider($filterParams);
        };
        
        // Удаляем ненужные действия
        unset($actions['delete'], $actions['update']);
        
        return $actions;
    }
    
    /**
     * Переопределяем actionUpdate для использования сервиса
     */
    public function actionUpdate($id)
    {
        return $this->requestService->updateRequest(
            $id, 
            Yii::$app->getRequest()->getBodyParams(), 
            $this->emailService
        );
    }
}