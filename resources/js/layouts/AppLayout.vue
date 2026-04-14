<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { computed, watch } from 'vue';

const page = usePage();
const toast = useToast();

const flash = computed(() => page.props.flash as { success: string | null; error: string | null });

watch(
    flash,
    (value) => {
        if (value.success) {
            toast.add({ severity: 'success', summary: 'Success', detail: value.success, life: 3000 });
        }
        if (value.error) {
            toast.add({ severity: 'error', summary: 'Error', detail: value.error, life: 4000 });
        }
    },
    { deep: true },
);

const navLinks = [
    { label: 'Products', href: '/products' },
    { label: 'Categories', href: '/categories' },
];

function isActive(href: string): boolean {
    return page.url.startsWith(href);
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Toast position="top-right" />

        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-8">
                        <Link href="/" class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-md bg-green-500 flex items-center justify-center">
                                <i class="pi pi-box text-white text-sm" />
                            </span>
                            <span class="text-lg font-bold text-gray-900">Product Manager</span>
                        </Link>

                        <div class="flex items-center gap-1">
                            <Link
                                v-for="link in navLinks"
                                :key="link.href"
                                :href="link.href"
                                class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
                                :class="
                                    isActive(link.href)
                                        ? 'border-green-500 text-green-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300'
                                "
                            >
                                {{ link.label }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
