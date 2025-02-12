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
        $conversions = new Conversions($alias);
        return $this->render('start', ['spreadsheet_id' => $conversions->getSpreadsheetId()]);
    }

}
