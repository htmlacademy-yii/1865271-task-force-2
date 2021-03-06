<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel  app\models\Tasksearch */
/* @var $categories [] app\models\Category */

$this->title = 'Новые задания';
$this->params['breadcrumbs'][] = $this->title;
?>
<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task"><?= Html::encode($this->title) ?></h3>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}',
            'itemOptions' => ['class' => 'item'],
            'itemView' => '_item',
            'emptyText' => 'Нет новых задач'
        ]) ?>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <?php
                $form = ActiveForm::begin([
                    'id'      => 'filter-form',
                    'method'  => 'GET',
                    'action' => ['index']
                ]); ?>
                <h4 class="head-card first-head-card">Категории</h4>
                <div class="form-group">
                    <?= $form->field($searchModel, 'category_ids')->checkboxList($categories,  ['unselect' => null])->label('') ?>
                </div>
                <h4 class="head-card">Дополнительно</h4>
                <div class="form-group">
                    <?= $form->field($searchModel, 'without_executor')->checkbox([
                        'label' => 'Без исполнителя',
                    ]) ?>
                </div>
                <h4 class="head-card">Период</h4>
                <div class="form-group">
                    <?= $form->field($searchModel, 'period')->dropDownList(
                        ['' => '', '1' => '1 час', '12' => '12 часов', '24' => '24 часа'],
                        ['prompt' => 'Любой']
                    )->label('') ?>

                </div>
                <?= Html::submitButton('Искать', ['class' => 'button button--blue']) ?>
                <?php
                ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</main>
