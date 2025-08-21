<?php
namespace app\services;

use Yii;
use app\models\Request;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class RequestService
{
    /**
     * Возвращает отфильтрованный провайдер данных на основе заданных критериев.
     *
     * @param array $filters Массив фильтров для применения к данным.
     * @return \yii\data\ActiveDataProvider Провайдер данных с применёнными фильтрами.
     */
    public function getFilteredDataProvider($filterParams)
    {
        $query = Request::find();
        
        if (!empty($filterParams['status']) && 
            in_array($filterParams['status'], [Request::STATUS_ACTIVE, Request::STATUS_RESOLVED])) {
            $query->andWhere(['status' => $filterParams['status']]);
        }
        
        if (!empty($filterParams['date_from'])) {
            $query->andWhere(['>=', 'created_at', $filterParams['date_from']]);
        }
        if (!empty($filterParams['date_to'])) {
            $query->andWhere(['<=', 'created_at', $filterParams['date_to']]);
        }
        
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
    }
    
    /**
     * Обновляет существующий запрос с новыми данными.
     *
     * @param int $id Идентификатор запроса, который необходимо обновить.
     * @param array $data Ассоциативный массив с новыми данными для обновления запроса.
     * @return bool Возвращает true в случае успешного обновления, иначе false.
     */
    public function updateRequest($id, $data, EmailService $emailService)
    {
        $model = Request::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Заявка не найдена");
        }
        
        $oldStatus = $model->status;
        
        if ($oldStatus === Request::STATUS_RESOLVED && 
            $model->status === Request::STATUS_RESOLVED) {
                  throw new NotFoundHttpException("Заявка уже обработана ");
           
        }
        $model->load($data, '');
        
        if (!$model->save()) {
            throw new BadRequestHttpException('Ошибка при обновлении заявки: ' . implode(', ', $model->getFirstErrors()));
        }



         $emailService->sendRequestResolvedEmail($model);
        
        return true;
    }
}