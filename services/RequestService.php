<?php
// services/RequestService.php

namespace app\services;

use Yii;
use app\models\Request;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class RequestService
{
    /**
     * Возвращает провайдер данных для списка заявок с фильтрацией
     * 
     * @param array $filterParams
     * @return ActiveDataProvider
     */
    public function getFilteredDataProvider($filterParams)
    {
        $query = Request::find();
        
        // Фильтрация по статусу
        if (!empty($filterParams['status']) && 
            in_array($filterParams['status'], [Request::STATUS_ACTIVE, Request::STATUS_RESOLVED])) {
            $query->andWhere(['status' => $filterParams['status']]);
        }
        
        // Фильтрация по дате
        if (!empty($filterParams['date_from'])) {
            $query->andWhere(['>=', 'created_at', strtotime($filterParams['date_from'])]);
        }
        if (!empty($filterParams['date_to'])) {
            $query->andWhere(['<=', 'created_at', strtotime($filterParams['date_to'] . ' 23:59:59')]);
        }
        
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
    }
    
    /**
     * Обновляет заявку и отправляет email при изменении статуса
     * 
     * @param int $id
     * @param array $data
     * @param EmailService $emailService
     * @return Request
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function updateRequest($id, $data, EmailService $emailService)
    {
        $model = Request::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Request not found");
        }
        
        $oldStatus = $model->status;
        
        $model->load($data, '');
        if (!$model->save()) {
            throw new BadRequestHttpException('Failed to update request: ' . implode(', ', $model->getFirstErrors()));
        }

        if ($oldStatus !== Request::STATUS_RESOLVED && 
            $model->status === Request::STATUS_RESOLVED) {
            $emailService->sendRequestResolvedEmail($model);
        }
        
        return $model;
    }
}