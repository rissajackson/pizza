<?php

namespace Tests\Unit\PhpunitPizzaOrder;

use App\Enums\PizzaOrderStatus;
use PHPUnit\Framework\TestCase;

class PizzaOrderStatusTest extends TestCase
{
    /** @test */
    public function it_returns_the_correct_label_for_all_statuses()
    {
        // Define the expected labels for each status
        $expectedLabels = [
            PizzaOrderStatus::RECEIVED->value => 'Received',
            PizzaOrderStatus::WORKING->value => 'Working',
            PizzaOrderStatus::IN_OVEN->value => 'In Oven',
            PizzaOrderStatus::READY->value => 'Ready',
        ];

        // Iterate through all cases of PizzaOrderStatus
        foreach (PizzaOrderStatus::cases() as $status) {
            // Assert that the label matches the expected value
            $this->assertSame(
                $expectedLabels[$status->value],
                $status->label(),
                "The label for status '{$status->value}' does not match the expected value."
            );
        }
    }
}
