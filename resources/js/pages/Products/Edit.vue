<script setup lang="ts">
import ProductForm from '@/components/ProductForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import * as ProductController from '@/actions/App/Http/Controllers/ProductController';
import Button from 'primevue/button';
import Card from 'primevue/card';

defineOptions({ layout: AppLayout });

interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    quantity: number;
    category_id: number;
}

const props = defineProps<{
    product: Product;
    categories: Category[];
}>();

const form = useForm({
    name: props.product.name,
    quantity: props.product.quantity,
    category_id: props.product.category_id,
});

function submit() {
    form.put(ProductController.update.url(props.product));
}
</script>

<template>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-surface-900">Edit Product</h1>
            <p class="text-surface-500 mt-1">Update the product details below.</p>
        </div>

        <Card>
            <template #content>
                <form @submit.prevent="submit">
                    <ProductForm :form="form" :categories="categories">
                        <template #actions>
                            <div class="flex items-center gap-3 pt-2">
                                <Button
                                    type="submit"
                                    label="Save Changes"
                                    :loading="form.processing"
                                    icon="pi pi-check"
                                />
                                <Button
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
