<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

interface Category {
    id: number;
    name: string;
}

interface ProductFormData {
    name: string;
    quantity: number | null;
    category_id: number | null;
}

const props = defineProps<{
    form: InertiaForm<ProductFormData>;
    categories: Category[];
}>();

const emit = defineEmits<{
    submit: [];
}>();
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-2">
            <label for="name" class="text-sm font-medium text-surface-700">Product Name</label>
            <InputText
                id="name"
                v-model="props.form.name"
                placeholder="Enter product name"
                :invalid="!!props.form.errors.name"
                class="w-full"
            />
            <small v-if="props.form.errors.name" class="text-red-500 text-xs">
                {{ props.form.errors.name }}
            </small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="quantity" class="text-sm font-medium text-surface-700">Quantity</label>
            <InputNumber
                id="quantity"
                v-model="props.form.quantity"
                placeholder="Enter quantity"
                :min="1"
                :invalid="!!props.form.errors.quantity"
                class="w-full"
            />
            <small v-if="props.form.errors.quantity" class="text-red-500 text-xs">
                {{ props.form.errors.quantity }}
            </small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="category" class="text-sm font-medium text-surface-700">Category</label>
            <Select
                id="category"
                v-model="props.form.category_id"
                :options="props.categories"
                option-label="name"
                option-value="id"
                placeholder="Select a category"
                :invalid="!!props.form.errors.category_id"
                class="w-full"
            />
            <small v-if="props.form.errors.category_id" class="text-red-500 text-xs">
                {{ props.form.errors.category_id }}
            </small>
        </div>

        <slot name="actions" />
    </div>
</template>
