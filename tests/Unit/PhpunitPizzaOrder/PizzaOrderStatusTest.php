<?php

namespace Tests\Unit\PhpunitPizzaOrder;

use App\Enums\PizzaOrderStatusEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PizzaOrderStatusTest extends TestCase
{
    #[Test]
    public function it_returns_the_correct_label_for_all_statuses()
    {
        $expectedLabels = [
            PizzaOrderStatusEnum::RECEIVED->value => 'Received',
            PizzaOrderStatusEnum::WORKING->value => 'Working',
            PizzaOrderStatusEnum::IN_OVEN->value => 'In Oven',
            PizzaOrderStatusEnum::READY->value => 'Ready',
        ];

        foreach (PizzaOrderStatusEnum::cases() as $status) {
            $this->assertSame(
                $expectedLabels[$status->value],
                $status->label(),
                "The label for status '{$status->value}' does not match the expected value."
            );
        }
    }
}
