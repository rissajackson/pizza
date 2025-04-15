<?php

use App\Enums\PizzaOrderStatus;

it('returns the correct label for all statuses', function () {
    $expectedLabels = collect([
        PizzaOrderStatus::RECEIVED->value => 'Received',
        PizzaOrderStatus::WORKING->value => 'Working',
        PizzaOrderStatus::IN_OVEN->value => 'In Oven',
        PizzaOrderStatus::READY->value => 'Ready',
    ]);

    foreach (PizzaOrderStatus::cases() as $status) {
        expect($status->label())->toBe($expectedLabels[$status->value]);
    }
});
