<script setup>
import { reactive, onMounted, onBeforeUnmount } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3'; // Import usePage
import PrimaryButton from "@/Components/PrimaryButton.vue"; // Import the Button component

/**
 * Props provided by the server
 * - Orders are passed as props from the server.
 */
const { orders } = defineProps({
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
    window.Echo.channel(`pizza-order.${orderId}`)
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
        window.Echo.leave(`pizza-order.${order.id}`);
    });
});

/**
 * Function to handle status updates.
 * This function will send a request to the server to update the order status.
 */
async function updateOrderStatus(orderId, newStatus) {
    try {
        // Send a request to the server to update the order status
        const response = await fetch(`/pizza-orders/${orderId}/status`, { // Use fetch
            method: 'PATCH', // Use PATCH for updates
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': usePage().props.csrfToken, // Get CSRF token from props
            },
            body: JSON.stringify({ status: newStatus }),
        });

        if (!response.ok) {
            throw new Error(`Failed to update order status: ${response.status}`);
        }

        // Optionally, update the local state optimistically.  The WebSocket update
        // should still be the source of truth, but this makes the UI feel faster.
        const orderToUpdate = reactiveOrders.find((order) => order.id === orderId);
        if (orderToUpdate) {
            orderToUpdate.status = newStatus;
        }

        console.log(`Order ${orderId} status updated to ${newStatus}`);
    } catch (error) {
        console.error('Error updating order status:', error);
        // Handle the error (e.g., show a message to the user)
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
                            <h3 class="text-lg font-medium mb-4">Orders</h3>
                            <ul class="list-disc pl-6">
                                <li v-for="order in reactiveOrders" :key="order.id" class="mb-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-semibold">Order #{{ order.id }}</span> –
                                            Status: <span :class="[
                                                order.status === 'received' ? 'italic text-gray-500' :
                                                order.status === 'working' ? 'italic text-blue-500' :
                                                order.status === 'in_oven' ? 'italic text-orange-500' :
                                                'italic text-green-500'
                                            ]">{{ order.status }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <Primary-Button
                                                @click="updateOrderStatus(order.id, 'working')"
                                                :disabled="order.status !== 'received'"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                            >
                                                Start
                                            </Primary-Button>
                                            <Primary-Button
                                                @click="updateOrderStatus(order.id, 'in_oven')"
                                                :disabled="order.status !== 'working'"
                                                class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded"
                                            >
                                                Oven
                                            </Primary-Button>
                                            <Primary-Button
                                                @click="updateOrderStatus(order.id, 'ready')"
                                                :disabled="order.status !== 'in_oven'"
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                            >
                                                Ready
                                            </Primary-Button>
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
