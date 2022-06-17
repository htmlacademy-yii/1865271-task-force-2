<?php

namespace app\controllers;
use Yii;
use app\models\Task;
use app\models\Category;
use app\models\TaskSearch;
use app\models\Response as TaskResponse;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;


/**
 * TaskController implements the CRUD actions for Task model.
 */
class TasksController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Task models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $categories = Category::find()->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'categories' => ArrayHelper::map($categories, 'id', 'name'),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View one Task models.
     * @param int $id
     * @return string
     */
    public function actionView(int $id)
    {
        $model = $this->findModel($id);

        $provider = new ActiveDataProvider([
            'query' => TaskResponse::find()->where(['task_id' => $id])
        ]);

        return $this->render('view', [
            'model' => $model,
            'provider' => $provider
        ]);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
