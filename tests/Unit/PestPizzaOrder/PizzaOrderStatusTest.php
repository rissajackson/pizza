<?php

use App\Enums\PizzaOrderStatusEnum;

it('returns the correct label for all statuses', function () {
    $expectedLabels = collect([
        PizzaOrderStatusEnum::RECEIVED->value => 'Received',
        PizzaOrderStatusEnum::WORKING->value => 'Working',
        PizzaOrderStatusEnum::IN_OVEN->value => 'In Oven',
        PizzaOrderStatusEnum::READY->value => 'Ready',
    ]);

    foreach (PizzaOrderStatusEnum::cases() as $status) {
        expect($status->label())->toBe($expectedLabels[$status->value]);
    }
});
