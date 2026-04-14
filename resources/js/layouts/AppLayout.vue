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
    <div class="min-h-screen bg-surface-50">
        <Toast position="top-right" />

        <nav class="bg-surface-0 shadow-sm border-b border-surface-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-8">
                        <Link href="/" class="text-xl font-bold text-primary-600">
                            Product Manager
                        </Link>

                        <div class="flex items-center gap-1">
                            <Link
                                v-for="link in navLinks"
                                :key="link.href"
                                :href="link.href"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="
                                    isActive(link.href)
                                        ? 'bg-primary-50 text-primary-700'
                                        : 'text-surface-600 hover:text-surface-900 hover:bg-surface-100'
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
