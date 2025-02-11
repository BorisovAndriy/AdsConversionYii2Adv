<?php


namespace frontend\controllers;

use common\services\conversions\Conversions;

class ConversionsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionStart($alias)
    {
        echo '<br>actionCoversion<br><pre>';
        $conversions = new Conversions($alias);
        die('<br>actionConversion complete<br>');
        return $this->render('start');
    }

}
