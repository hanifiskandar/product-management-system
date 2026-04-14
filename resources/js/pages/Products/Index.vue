<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import * as ProductController from '@/actions/App/Http/Controllers/ProductController';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { useConfirm } from 'primevue/useconfirm';
import { ref, watch } from 'vue';

defineOptions({ layout: AppLayout });

interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    quantity: number;
    created_at: string;
    category: Category;
}

interface PaginatedProducts {
    data: Product[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

interface Filters {
    search?: string;
    category_id?: number | null;
    sort_field?: string;
    sort_order?: string;
}

const props = defineProps<{
    products: PaginatedProducts;
    categories: Category[];
    filters: Filters;
}>();

const confirm = useConfirm();

const search = ref(props.filters.search ?? '');
const selectedCategoryId = ref<number | null>(props.filters.category_id ?? null);
const sortField = ref(props.filters.sort_field ?? 'created_at');
const sortOrder = ref<1 | -1>(props.filters.sort_order === 'asc' ? 1 : -1);

let searchTimeout: ReturnType<typeof setTimeout>;

function buildParams(overrides: Record<string, unknown> = {}) {
    return {
        search: search.value || undefined,
        category_id: selectedCategoryId.value || undefined,
        sort_field: sortField.value !== 'created_at' ? sortField.value : undefined,
        sort_order: sortOrder.value === 1 ? 'asc' : undefined,
        ...overrides,
    };
}

function applyFilters() {
    router.get(ProductController.index.url(), buildParams(), {
        preserveState: true,
        replace: true,
    });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch(selectedCategoryId, applyFilters);

function onPage(event: { page: number; rows: number }) {
    router.get(ProductController.index.url(), buildParams({ page: event.page + 1 }), {
        preserveState: true,
    });
}

function onSort(event: { sortField: string; sortOrder: 1 | -1 }) {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    router.get(
        ProductController.index.url(),
        buildParams({
            sort_field: event.sortField !== 'created_at' ? event.sortField : undefined,
            sort_order: event.sortOrder === 1 ? 'asc' : undefined,
        }),
        { preserveState: true, replace: true },
    );
}

function quantitySeverity(qty: number): 'success' | 'warn' | 'danger' {
    if (qty > 10) return 'success';
    if (qty >= 5) return 'warn';
    return 'danger';
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function confirmDelete(product: Product) {
    confirm.require({
        message: `Are you sure you want to delete "${product.name}"? This action cannot be undone.`,
        header: 'Delete Product',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        acceptSeverity: 'danger',
        accept() {
            router.delete(ProductController.destroy.url(product));
        },
    });
}
</script>

<template>
    <div>
        <ConfirmDialog />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-surface-900">Products</h1>
                <p class="text-surface-500 mt-1">Manage your product inventory.</p>
            </div>
            <Link :href="ProductController.create.url()">
                <Button label="Add Product" icon="pi pi-plus" />
            </Link>
        </div>

        <Card>
            <template #content>
                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <InputText
                        v-model="search"
                        placeholder="Search products..."
                        class="flex-1"
                    />
                    <Select
                        v-model="selectedCategoryId"
                        :options="[{ id: null, name: 'All Categories' }, ...categories]"
                        option-label="name"
                        option-value="id"
                        placeholder="All Categories"
                        class="w-full sm:w-56"
                    />
                </div>

                <DataTable
                    :value="products.data"
                    :rows="products.per_page"
                    :total-records="products.total"
                    :first="(products.current_page - 1) * products.per_page"
                    :sort-field="sortField"
                    :sort-order="sortOrder"
                    paginator
                    lazy
                    sort-mode="single"
                    removable-sort
                    row-hover
                    @page="onPage"
                    @sort="onSort"
                >
                    <template #empty>
                        <div class="text-center py-12 text-surface-400">
                            <i class="pi pi-inbox text-4xl mb-3 block" />
                            <p class="text-lg font-medium">No products found</p>
                            <p class="text-sm mt-1">Try adjusting your search or filters.</p>
                        </div>
                    </template>

                    <Column field="name" header="Name" sortable />

                    <Column field="category.name" header="Category">
                        <template #body="{ data }">
                            <span class="text-surface-700">{{ data.category?.name ?? '—' }}</span>
                        </template>
                    </Column>

                    <Column field="quantity" header="Quantity" sortable>
                        <template #body="{ data }">
                            <Badge
                                :value="String(data.quantity)"
                                :severity="quantitySeverity(data.quantity)"
                            />
                        </template>
                    </Column>

                    <Column field="created_at" header="Created At" sortable>
                        <template #body="{ data }">
                            {{ formatDate(data.created_at) }}
                        </template>
                    </Column>

                    <Column header="Actions" style="width: 10rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Link :href="ProductController.edit.url(data)">
                                    <Button
                                        icon="pi pi-pencil"
                                        severity="secondary"
                                        size="small"
                                        v-tooltip="'Edit'"
                                    />
                                </Link>
                                <Button
                                    icon="pi pi-trash"
                                    severity="danger"
                                    size="small"
                                    v-tooltip="'Delete'"
                                    @click="confirmDelete(data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>
