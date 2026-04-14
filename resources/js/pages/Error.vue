<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import * as ProductController from '@/actions/App/Http/Controllers/ProductController';
import Button from 'primevue/button';

const props = defineProps<{
    status: number;
}>();

const messages: Record<number, { title: string; description: string }> = {
    404: {
        title: 'Page Not Found',
        description: "The page you're looking for doesn't exist or has been moved.",
    },
    500: {
        title: 'Server Error',
        description: 'Something went wrong on our end. Please try again later.',
    },
    503: {
        title: 'Service Unavailable',
        description: 'We are down for maintenance. Please check back soon.',
    },
};

const { title, description } = messages[props.status] ?? {
    title: 'An Error Occurred',
    description: 'Something unexpected happened.',
};
</script>

<template>
    <div class="min-h-screen bg-surface-50 flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            <p class="text-8xl font-bold text-primary-400 mb-4">{{ status }}</p>
            <h1 class="text-2xl font-bold text-surface-900 mb-2">{{ title }}</h1>
            <p class="text-surface-500 mb-8">{{ description }}</p>
            <Link :href="ProductController.index.url()">
                <Button label="Go to Products" icon="pi pi-arrow-left" />
            </Link>
        </div>
    </div>
</template>
