<script setup>
import { reactive, onMounted, onBeforeUnmount, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const { orders } = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

const reactiveOrders = reactive(orders);

const getStatusWithEmoji = computed(() => {
    return (status) => {
        switch (status) {
            case 'received':
                return '📥 Received';
            case 'working':
                return '⚒️ Working';
            case 'in_oven':
                return '🔥 In Oven';
            case 'ready':
                return '✅ Ready';
            default:
                return status;
        }
    };
});

onMounted(() => {
    if (reactiveOrders.length === 0) {
        console.warn('No orders available to track.');
        return;
    }

    reactiveOrders.forEach((order) => {
        subscribeToOrderUpdates(order.id);
    });
});

function subscribeToOrderUpdates(orderId) {
    window.Echo.channel(`pizza-order.${orderId}`)
        .listen('PizzaOrderStatusUpdatedEvent', (event) => {
            console.log(`Broadcast received for Order #${orderId}:`, event);

            const orderToUpdate = reactiveOrders.find((order) => order.id === orderId);
            if (orderToUpdate) {
                orderToUpdate.status = event.status;
            }
        });
}

function updateOrderStatus(orderId, newStatus) {
    axios
        .patch(`/pizza-orders/${orderId}/status`, {status: newStatus})
        .then((response) => {
            console.log(`Order #${orderId} status updated to ${newStatus}`);

            const orderToUpdate = reactiveOrders.find((order) => order.id === orderId);
            if (orderToUpdate) {
                orderToUpdate.status = newStatus;
            }
        })
        .catch((error) => {
            console.error('Failed to update order status:', error.response?.data || error.message);
            alert('Failed to update order status. Please try again.');
        });
}

onBeforeUnmount(() => {
    reactiveOrders.forEach((order) => {
        window.Echo.leave(`pizza-order.${order.id}`);
    });
});
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-red-500">
                Cheesy Does It Pizza Order Dashboard
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
                                            <span class="font-semibold font-mono text-gray-800">Order #{{
                                                    order.id
                                                }}</span> –
                                            <span :class="{
                                            'italic text-red-500 font-bold font-mono': order.status === 'received',
                                            'italic text-blue-500 font-bold font-mono': order.status === 'working',
                                            'italic text-orange-500 font-bold font-mono': order.status === 'in_oven',
                                            'italic text-green-500 font-bold font-mono': order.status === 'ready'
                                            }">
                                                {{ getStatusWithEmoji(order.status) }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2">
                                            <PrimaryButton
                                                @click="updateOrderStatus(order.id, 'working')"
                                                class="text-white font-bold py-2 px-4 rounded"
                                                bgColorClass="bg-blue-400"
                                                bgColorHoverClass="hover:bg-blue-600"
                                                :disabled="order.status === 'working'"
                                            >
                                                Start
                                            </PrimaryButton>
                                            <PrimaryButton
                                                @click="updateOrderStatus(order.id, 'in_oven')"
                                                class="text-white font-bold py-2 px-4 rounded"
                                                bgColorClass="bg-orange-400"
                                                bgColorHoverClass="hover:bg-orange-600"
                                                :disabled="order.status === 'in_oven'"
                                            >
                                                Oven
                                            </PrimaryButton>
                                            <PrimaryButton
                                                @click="updateOrderStatus(order.id, 'ready')"
                                                class="text-white font-bold py-2 px-4 rounded"
                                                bgColorClass="bg-green-400"
                                                bgColorHoverClass="hover:bg-green-600"
                                                :disabled="order.status === 'ready'"
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
