<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import * as CategoryController from '@/actions/App/Http/Controllers/CategoryController';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import { ref, watch } from 'vue';

defineOptions({ layout: AppLayout });

interface Category {
    id: number;
    name: string;
    products_count: number;
    created_at: string;
}

interface PaginatedCategories {
    data: Category[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

interface Filters {
    search?: string;
}

const props = defineProps<{
    categories: PaginatedCategories;
    filters: Filters;
    total_categories: number;
}>();

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const editingCategory = ref<Category | null>(null);

const createForm = useForm({ name: '' });
const editForm = useForm({ name: '' });

const search = ref(props.filters.search ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            CategoryController.index.url(),
            { search: search.value || undefined },
            { preserveState: true, replace: true },
        );
    }, 400);
});

function onPage(event: { page: number; rows: number }) {
    router.get(
        CategoryController.index.url(),
        { search: search.value || undefined, page: event.page + 1 },
        { preserveState: true },
    );
}

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
        <Dialog v-model:visible="showCreateDialog" header="Add Category" modal :style="{ width: '28rem' }">
            <form @submit.prevent="submitCreate">
                <div class="flex flex-col gap-2 mb-6">
                    <label for="cat-name" class="text-sm font-medium text-gray-700">Category Name</label>
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
                    <Button type="button" label="Cancel" severity="secondary" @click="showCreateDialog = false" />
                    <Button type="submit" label="Create Category" :loading="createForm.processing" icon="pi pi-check" />
                </div>
            </form>
        </Dialog>

        <!-- Edit Dialog -->
        <Dialog v-model:visible="showEditDialog" header="Edit Category" modal :style="{ width: '28rem' }">
            <form @submit.prevent="submitEdit">
                <div class="flex flex-col gap-2 mb-6">
                    <label for="edit-cat-name" class="text-sm font-medium text-gray-700">Category Name</label>
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
                    <Button type="button" label="Cancel" severity="secondary" @click="showEditDialog = false" />
                    <Button type="submit" label="Save Changes" :loading="editForm.processing" icon="pi pi-check" />
                </div>
            </form>
        </Dialog>

        <!-- Page header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                <p class="text-gray-500 mt-1">Manage product categories.</p>
            </div>
            <Button label="Add Category" icon="pi pi-plus" @click="openCreate" />
        </div>

        <!-- Stat card -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <Card class="!shadow-sm">
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                            <i class="pi pi-tag text-green-500 text-lg" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Categories</p>
                            <p class="text-2xl font-bold text-gray-900">{{ total_categories }}</p>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Table card -->
        <Card class="!shadow-sm">
            <template #content>
                <!-- Search -->
                <div class="mb-3">
                    <InputText
                        v-model="search"
                        placeholder="Search categories..."
                        class="w-full"
                    />
                </div>

                <!-- Showing count -->
                <p class="text-sm text-gray-500 mb-3">
                    <template v-if="categories.total > 0">
                        Showing {{ categories.from }}–{{ categories.to }} of {{ categories.total }} categories
                    </template>
                    <template v-else>No categories found</template>
                </p>

                <DataTable
                    :value="categories.data"
                    :rows="categories.per_page"
                    :total-records="categories.total"
                    :first="(categories.current_page - 1) * categories.per_page"
                    paginator
                    lazy
                    row-hover
                    @page="onPage"
                >
                    <template #empty>
                        <div class="text-center py-16 text-gray-400">
                            <i class="pi pi-tag text-4xl mb-3 block" />
                            <p class="text-lg font-medium text-gray-600">No categories found</p>
                            <p class="text-sm mt-1">Try adjusting your search or create a new category.</p>
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
                                severity="info"
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
