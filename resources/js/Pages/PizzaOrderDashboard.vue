<script setup>
import { reactive, onMounted, onBeforeUnmount } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const { orders } = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

const reactiveOrders = reactive(orders);

/**
 * Subscribe to WebSocket updates for a specific order ID.
 */
function subscribeToOrderUpdates(orderId) {
    window.Echo.channel(`pizza-order.${orderId}`)
        .listen('PizzaOrderStatusUpdated', (event) => {
            console.log(`Broadcast received for Order #${orderId}:`, event);

            const orderToUpdate = reactiveOrders.find((order) => order.id === orderId);
            if (orderToUpdate) {
                orderToUpdate.status = event.status;
            }
        });
}

onMounted(() => {
    if (reactiveOrders.length === 0) {
        console.warn('No orders available to track.');
        return;
    }

    reactiveOrders.forEach((order) => {
        subscribeToOrderUpdates(order.id);
    });
});

onBeforeUnmount(() => {
    reactiveOrders.forEach((order) => {
        window.Echo.leave(`pizza-order.${order.id}`);
    });
});

async function updateOrderStatus(orderId, newStatus) {
    console.log('hey you clicked a button', orderId, newStatus);
    const csrfToken = usePage().props.csrfToken;

    try {
        const response = await fetch(`/pizza-orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ status: newStatus }),
        });

        if (!response.ok) {
            throw new Error(`Failed to update order status: ${response.status}`);
        }

        console.log(`Order ${orderId} status updated to ${newStatus}`);
     } catch (error) {
        console.error('Error updating order status:', error);
        alert('Failed to update order status. Please try again.');
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pizza Order Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div>
                            <h3 class="text-lg font-medium mb-4 text-gray-700">Orders</h3>
                            <ul class="list-disc pl-6">
                                <li v-for="order in reactiveOrders" :key="order.id" class="mb-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-semibold text-gray-800">Order #{{ order.id }}</span> –
                                            <span :class="{
                                            'italic text-black-500 font-bold': order.status === 'Received',
                                            'italic text-blue-500 font-bold': order.status === 'Working',
                                            'italic text-orange-500 font-bold': order.status === 'In Oven',
                                            'italic text-green-500 font-bold': order.status === 'Ready'
                                            }">
                                                {{ order.status }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2">
                                            <PrimaryButton
                                                @click="updateOrderStatus(order.id, 'working')"
                                                class="text-white font-bold py-2 px-4 rounded"
                                                bgColorClass="bg-blue-500 hover:bg-blue-700"
                                                :disabled="order.status === 'Working'"
                                            >
                                                Start
                                            </PrimaryButton>
                                            <PrimaryButton
                                                @click="updateOrderStatus(order.id, 'in_oven')"
                                                class="text-white font-bold py-2 px-4 rounded"
                                                bgColorClass="bg-orange-500 hover:bg-orange-700"
                                                :disabled="order.status === 'In Oven'"
                                            >
                                                Oven
                                            </PrimaryButton>
                                            <PrimaryButton
                                                @click="updateOrderStatus(order.id, 'ready')"
                                                class="text-white font-bold py-2 px-4 rounded"
                                                bgColorClass="bg-green-500 hover:bg-green-700"
                                                :disabled="order.status === 'Ready'"
                                            >
                                                Ready
                                            </PrimaryButton>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div v-if="reactiveOrders.length === 0" class="text-gray-500">
                                No orders available.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
h3 {
    color: #2c3e50;
}
</style>
