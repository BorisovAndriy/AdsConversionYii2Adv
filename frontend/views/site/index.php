<?php

/** @var yii\web\View $this */

$this->title = 'Ласкаво просимо до PeachSails!';
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-transparent rounded-3">
        <div class="container-fluid py-5 text-center">
            <h1 class="display-4">Вітаємо!</h1>
            <p class="fs-5 fw-light">Ваша система моніторингу конверсій успішно налаштована.</p>
        </div>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 d-flex flex-column">
                <h2>Конверсії</h2>
                <p>Система автоматично отримує дані про конверсії з Admitad API, аналізує їх та готує звіти.</p>
                <p>Всі дані оновлюються в реальному часі, що дозволяє своєчасно коригувати рекламні стратегії.</p>
                <p class="mt-auto"><a class="btn btn-outline-secondary" href="/conversions/index">Переглянути конверсії &raquo;</a></p>
            </div>
            <div class="col-lg-4 d-flex flex-column">
                <h2>Аналітика та звіти</h2>
                <p>Конверсії обробляються автоматично та зберігаються в Google Таблицях для подальшого аналізу.</p>
                <p>Дані дозволяють:
                <ul>
                    <li>Оцінювати ефективність рекламних кампаній.</li>
                    <li>Визначати прибутковість трафіку.</li>
                    <li>Оптимізувати бюджет та коригувати ставки.</li>
                    <li>Аналізувати географію кліків та джерела трафіку.</li>
                </ul>
                </p>
                <p class="mt-auto"><a class="btn btn-outline-secondary" href="/conversions/index">Переглянути аналітику &raquo;</a></p>
            </div>
            <div class="col-lg-4 d-flex flex-column">
                <h2>Інтеграції</h2>
                <p>Система підтримує інтеграцію з різними рекламними платформами для точного відстеження конверсій.</p>
                <p>Дані автоматично передаються в Google Ads для коригування стратегій у режимі реального часу.</p>
                <p class="mt-auto"><a class="btn btn-outline-secondary" href="/integrations">Налаштувати інтеграції &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
