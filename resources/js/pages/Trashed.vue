<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import { dashboard} from '@/routes';
    import { router } from '@inertiajs/vue3';
    import { type BreadcrumbItem } from '@/types';
    import { Head } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import * as Inertia from '@inertiajs/core';
    import {
        Table,
        TableBody,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
    } from '@/components/ui/table';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuSeparator,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { Plus, MoreHorizontal } from 'lucide-vue-next';
    import { useForm } from '@inertiajs/vue3'
    import { route } from 'ziggy-js';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
    ];
    const isDialogOpen = ref(false);
    const search = ref('');
    const editingProduct = ref(null);
    const productToDelete = ref(null);
    const isDeleteDialogOpen = ref(false);
    const props = defineProps<{
        products: {
            data: Array<{
                id: string;
                name: string;
                sku: string;
                price: number;
                stock: number;
                image_url: number;
            }>;
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        };
    }>();
    const form = useForm({
    name: '',
    sku: '',
    price: '',
    stock: '',
    image: null,
    })

    const searchProducts = () => {
        router.get(route('products.trashed'), { search: search.value }, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    const restoreProduct = (product) => {
        router.get(route('products.restore', product.id), {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const openDeleteModal = (product) => {
        productToDelete.value = product;
        isDeleteDialogOpen.value = true;
    };

    const confirmDelete = () => {
        if (productToDelete.value) {
             router.delete(route('products.permanent-delete', productToDelete.value.id), {
                forceFormData: true,
                onSuccess: () => {
                    isDeleteDialogOpen.value = false;
                    productToDelete.value = null;
                    Inertia.reload({ only: ['products'] });
                },
            })
        }
    };
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex gap-2">
                    <Input
                        id="search"
                        v-model="search"
                        placeholder="Search by name and SKU"
                        @keyup.enter="searchProducts"
                    />
                    <Button @click="searchProducts" variant="destructive">
                       Search
                    </Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">#</TableHead>
                            <TableHead>Image</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>SKU</TableHead>
                            <TableHead class="text-right">Price</TableHead>
                            <TableHead class="text-right">Stock</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(product, index) in props.products.data" :key="product.id">
                            <TableCell class="font-medium">{{ (props.products.current_page - 1) * props.products.per_page + index + 1 }}</TableCell>
                            <TableCell>
                                <img
                                    :src="product.image_url"
                                    alt="Product Image"
                                    class="h-12 w-12 rounded-md object-cover"
                                />
                            </TableCell>
                            <TableCell>{{ product.name }}</TableCell>
                            <TableCell>{{ product.sku }}</TableCell>
                            <TableCell class="text-right">{{ product.price }}</TableCell>
                            <TableCell class="text-right">{{ product.stock }}</TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem @click="restoreProduct(product)">Restore</DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem class="text-destructive" @click="openDeleteModal(product)">
                                            Permanent Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <div class="mt-4 flex justify-end py-5">
                    <nav class="flex items-center space-x-1">
                        <button
                            v-for="(link, i) in props.products.links"
                            :key="i"
                            @click="link.url && router.visit(link.url)"
                            v-html="link.label"
                            :disabled="!link.url"
                            :class="[
                                'px-3 py-1 rounded border',
                                link.active
                                    ? 'bg-gray-800 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-100',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        />
                    </nav>
                </div>

            </div>
        </div>
        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Are you sure?</DialogTitle>
                    <DialogDescription>
                        This action cannot be undone. This will permanently delete the product
                        <span v-if="productToDelete" class="font-semibold">"{{ productToDelete?.name }}"</span>
                        from your inventory.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="isDeleteDialogOpen = false">
                        Cancel
                    </Button>
                    <Button variant="destructive" @click="confirmDelete">
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
