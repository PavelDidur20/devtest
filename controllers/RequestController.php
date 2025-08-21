<?php
namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Request;
use app\services\RequestService;
use app\services\EmailService;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

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

        unset($behaviors['authenticator']);

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

        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'except' => ['create', 'options'],
            'auth' => function ($username, $password) {
                $user = User::findByUsername($username);
                if (
                    $user && $user->validatePassword(
                        $password
                    )
                ) {
                    return $user;
                }
                return null;
            }
        ];

        $behaviors['access'] = [
        'class' => AccessControl::class,
        'only' => ['index', 'view', 'update', 'create'],
        'rules' => [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'update'],
                'roles' => ['@'],
                'matchCallback' => function ($rule, $action) {
                    $user = Yii::$app->user->identity;
                    return $user->isManager();
                },
            ],
            [
                'allow' => true,
                'actions' => ['create'],
                'roles' => ['@'],
                'matchCallback' => function ($rule, $action) {
                    $user = Yii::$app->user->identity;
                    return $user->isClient();
                },
            ],
            
            [
                'allow' => true,
                'actions' => ['create'],
                'roles' => ['?'],
            ],
        ],
        'denyCallback' => function ($rule, $action) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }
    ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = function ($action) {
            $filterParams = Yii::$app->request->get();
            return $this->requestService->getFilteredDataProvider($filterParams);
        };

        unset($actions['delete'], $actions['update'], $actions['create'], $actions['view']);

        return $actions;
    }

    public function actionCreate()
    {
        $model = new Request();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->save()) {
            Yii::$app->response->setStatusCode(201);
            return [
                'success' => true,
                'message' => 'Заявка успешно создана',
            ];
        } else {
            Yii::$app->response->setStatusCode(422);
            return [
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $model->getErrors()
            ];
        }
    }

    public function actionUpdate($id)
    {
     
        try {
            $result = $this->requestService->updateRequest(
                $id,
                Yii::$app->getRequest()->getBodyParams(),
                $this->emailService
            );

            return [
                'success' => true,
                'message' => 'Заявка успешно обновлена',
            ];
        } catch (NotFoundHttpException $e) {
            Yii::$app->response->setStatusCode(422);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(422);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id)
    {
        try {
            $model = Request::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException('Заявка не найдена');
            }

            return [
                'success' => true,
                'data' => $model
            ];
        } catch (NotFoundHttpException $e) {
            Yii::$app->response->setStatusCode(422);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionIndex()
    {
        try {
            $dataProvider = $this->requestService->getFilteredDataProvider(Yii::$app->request->get());

            return [
                'success' => true,
                'data' => $dataProvider->getModels(),
                'pagination' => [
                    'totalCount' => $dataProvider->getTotalCount(),
                    'pageCount' => $dataProvider->getPagination()->getPageCount(),
                    'currentPage' => $dataProvider->getPagination()->getPage() + 1,
                    'perPage' => $dataProvider->getPagination()->getPageSize(),
                ]
            ];
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(422);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}