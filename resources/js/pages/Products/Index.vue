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
    total_products: number;
    total_categories: number;
    low_stock_count: number;
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

        <!-- Page header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                <p class="text-gray-500 mt-1">Manage your product inventory.</p>
            </div>
            <Link :href="ProductController.create.url()">
                <Button label="Add Product" icon="pi pi-plus" />
            </Link>
        </div>

        <!-- Stats bar -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <Card class="!shadow-sm">
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="pi pi-box text-blue-500 text-lg" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ total_products }}</p>
                        </div>
                    </div>
                </template>
            </Card>

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

            <Card class="!shadow-sm">
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                            <i class="pi pi-exclamation-triangle text-red-500 text-lg" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Low Stock</p>
                            <p class="text-2xl font-bold text-red-600">{{ low_stock_count }}</p>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Table card -->
        <Card class="!shadow-sm">
            <template #content>
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3 mb-3">
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

                <!-- Showing count -->
                <p class="text-sm text-gray-500 mb-3">
                    <template v-if="products.total > 0">
                        Showing {{ products.from }}–{{ products.to }} of {{ products.total }} products
                    </template>
                    <template v-else>No products found</template>
                </p>

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
                        <div class="text-center py-16 text-gray-400">
                            <i class="pi pi-search text-4xl mb-3 block" />
                            <p class="text-lg font-medium text-gray-600">No products found</p>
                            <p class="text-sm mt-1">Try adjusting your search or filter.</p>
                        </div>
                    </template>

                    <Column field="name" header="Name" sortable />

                    <Column field="category.name" header="Category">
                        <template #body="{ data }">
                            <span class="text-gray-700">{{ data.category?.name ?? '—' }}</span>
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
                                        severity="info"
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
