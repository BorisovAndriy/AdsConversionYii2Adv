<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
?>
<h1>Conversions completed</h1>

<p>
    <?= Html::a(
        'Open Spreadsheet',
        "https://docs.google.com/spreadsheets/d/{$spreadsheet_id}",
        ['target' => '_blank', 'rel' => 'noopener noreferrer']
    ) ?>
</p>
