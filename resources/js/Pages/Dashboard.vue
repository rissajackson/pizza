<script setup>
import { reactive, onMounted, onBeforeUnmount } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Echo from 'laravel-echo'; // Assuming Echo is globally set up

/**
 * Props provided by the server
 * - Orders are passed as props from the server.
 */
defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

/**
 * Reactive state for the orders list
 */
const reactiveOrders = reactive([...orders]);

/**
 * Function to Subscribe to Order Broadcasts
 * @param {number} orderId - The ID of the pizza order to subscribe to
 */
function subscribeToOrderUpdates(orderId) {
    Echo.channel(`pizza-order.${orderId}`)
        .listen('PizzaOrderStatusUpdated', (event) => {
            console.log(`Broadcast received for Order #${orderId}:`, event);

            // Find the corresponding order and update the status in the reactive array
            const orderToUpdate = reactiveOrders.find((order) => order.id === orderId);
            if (orderToUpdate) {
                orderToUpdate.status = event.status;
            }
        });
}

/**
 * Setup WebSocket listeners on component mount
 */
onMounted(() => {
    if (!orders || orders.length === 0) {
        console.warn('No orders available to track.');
        return;
    }

    // Loop through each order and subscribe to updates
    reactiveOrders.forEach((order) => {
        subscribeToOrderUpdates(order.id);
    });
});

/**
 * Clean up WebSocket listeners on component unmount
 */
onBeforeUnmount(() => {
    reactiveOrders.forEach((order) => {
        Echo.leave(`pizza-order.${order.id}`);
    });
});
</script>

<template>
    <!-- Set the page title -->
    <Head title="Dashboard" />

    <!-- Authenticated Laravel Layout -->
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pizza Order Dashboard
            </h2>
        </template>

        <!-- Main Dashboard Content -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!-- Display Orders -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">Orders</h3>
                            <ul class="list-disc pl-6">
                                <li
                                    v-for="order in reactiveOrders"
                                    :key="order.id"
                                    class="mb-2"
                                >
                                    <span class="font-semibold">Order #{{ order.id }}</span> –
                                    Status: <span class="italic">{{ order.status }}</span>
                                </li>
                            </ul>

                            <!-- Display Message if No Orders Exist -->
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
