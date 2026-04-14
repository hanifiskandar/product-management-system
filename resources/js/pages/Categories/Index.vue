<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import * as CategoryController from '@/actions/App/Http/Controllers/CategoryController';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

interface Category {
    id: number;
    name: string;
    products_count: number;
    created_at: string;
}

defineProps<{
    categories: Category[];
}>();

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const editingCategory = ref<Category | null>(null);

const createForm = useForm({ name: '' });
const editForm = useForm({ name: '' });

function openCreate() {
    createForm.reset();
    showCreateDialog.value = true;
}

function openEdit(category: Category) {
    editingCategory.value = category;
    editForm.name = category.name;
    editForm.clearErrors();
    showEditDialog.value = true;
}

function submitCreate() {
    createForm.post(CategoryController.store.url(), {
        onSuccess: () => {
            showCreateDialog.value = false;
            createForm.reset();
        },
    });
}

function submitEdit() {
    if (!editingCategory.value) return;
    editForm.put(CategoryController.update.url(editingCategory.value), {
        onSuccess: () => {
            showEditDialog.value = false;
        },
    });
}
</script>

<template>
    <div>
        <!-- Create Dialog -->
        <Dialog
            v-model:visible="showCreateDialog"
            header="Add Category"
            modal
            :style="{ width: '28rem' }"
        >
            <form @submit.prevent="submitCreate">
                <div class="flex flex-col gap-2 mb-6">
                    <label for="cat-name" class="text-sm font-medium text-surface-700">
                        Category Name
                    </label>
                    <InputText
                        id="cat-name"
                        v-model="createForm.name"
                        placeholder="e.g. Electronics"
                        :invalid="!!createForm.errors.name"
                        autofocus
                    />
                    <small v-if="createForm.errors.name" class="text-red-500 text-xs">
                        {{ createForm.errors.name }}
                    </small>
                </div>

                <div class="flex justify-end gap-3">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        @click="showCreateDialog = false"
                    />
                    <Button
                        type="submit"
                        label="Create Category"
                        :loading="createForm.processing"
                        icon="pi pi-check"
                    />
                </div>
            </form>
        </Dialog>

        <!-- Edit Dialog -->
        <Dialog
            v-model:visible="showEditDialog"
            header="Edit Category"
            modal
            :style="{ width: '28rem' }"
        >
            <form @submit.prevent="submitEdit">
                <div class="flex flex-col gap-2 mb-6">
                    <label for="edit-cat-name" class="text-sm font-medium text-surface-700">
                        Category Name
                    </label>
                    <InputText
                        id="edit-cat-name"
                        v-model="editForm.name"
                        placeholder="e.g. Electronics"
                        :invalid="!!editForm.errors.name"
                        autofocus
                    />
                    <small v-if="editForm.errors.name" class="text-red-500 text-xs">
                        {{ editForm.errors.name }}
                    </small>
                </div>

                <div class="flex justify-end gap-3">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        @click="showEditDialog = false"
                    />
                    <Button
                        type="submit"
                        label="Save Changes"
                        :loading="editForm.processing"
                        icon="pi pi-check"
                    />
                </div>
            </form>
        </Dialog>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-surface-900">Categories</h1>
                <p class="text-surface-500 mt-1">Manage product categories.</p>
            </div>
            <Button label="Add Category" icon="pi pi-plus" @click="openCreate" />
        </div>

        <Card>
            <template #content>
                <DataTable :value="categories" row-hover>
                    <template #empty>
                        <div class="text-center py-12 text-surface-400">
                            <i class="pi pi-tag text-4xl mb-3 block" />
                            <p class="text-lg font-medium">No categories yet</p>
                            <p class="text-sm mt-1">Create your first category to get started.</p>
                        </div>
                    </template>

                    <Column field="name" header="Name" sortable />

                    <Column field="products_count" header="Products" sortable>
                        <template #body="{ data }">
                            <Badge :value="String(data.products_count)" severity="info" />
                        </template>
                    </Column>

                    <Column header="Actions" style="width: 7rem">
                        <template #body="{ data }">
                            <Button
                                icon="pi pi-pencil"
                                severity="secondary"
                                size="small"
                                v-tooltip="'Edit'"
                                @click="openEdit(data)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>
