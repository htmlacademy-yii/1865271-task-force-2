<?php

 use yii\widgets\ActiveForm;
 use yii\helpers\Html;

/* @var $model app/models/LoginForm;*/
?>
<section class="modal enter-form form-modal" id="enter-form">
    <h2>Вход на сайт</h2>
    <?php $form = ActiveForm::begin(['action' => ['login'], 'id' => 'signup-form', 'options' => ['class' => 'login__form'], 'enableAjaxValidation' => true]); ?>
        <p>
            <?= $form->field($model, 'email')->textInput() ?>
        </p>
        <p>
            <?= $form->field($model, 'password')->passwordInput() ?>
        </p>
    <?= Html::submitButton('Войти', ['class' => 'button']) ?>
    <?php $form::end();?>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>
