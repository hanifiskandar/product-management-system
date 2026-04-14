<script setup lang="ts">
import ProductForm from '@/components/ProductForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import * as ProductController from '@/actions/App/Http/Controllers/ProductController';

defineOptions({ layout: AppLayout });

interface Category {
    id: number;
    name: string;
}

defineProps<{
    categories: Category[];
}>();

const form = useForm({
    name: '',
    quantity: null as number | null,
    category_id: null as number | null,
});

function submit() {
    form.post(ProductController.store.url());
}
</script>

<template>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-surface-900">Create Product</h1>
            <p class="text-surface-500 mt-1">Add a new product to your inventory.</p>
        </div>

        <Card>
            <template #content>
                <form @submit.prevent="submit">
                    <ProductForm :form="form" :categories="categories">
                        <template #actions>
                            <div class="flex items-center gap-3 pt-2">
                                <Button
                                    type="submit"
                                    label="Create Product"
                                    :loading="form.processing"
                                    icon="pi pi-check"
                                />
                                <Button
                                    type="button"
                                    label="Cancel"
                                    severity="secondary"
                                    icon="pi pi-times"
                                    @click="router.visit(ProductController.index.url())"
                                />
                            </div>
                        </template>
                    </ProductForm>
                </form>
            </template>
        </Card>
    </div>
</template>
