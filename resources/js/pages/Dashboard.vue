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
    console.log(props.products)
    const form = useForm({
    name: '',
    sku: '',
    price: '',
    stock: '',
    image: null,
    })

    const searchProducts = () => {
        router.get(route('dashboard'), { search: search.value }, {
            preserveState: true,
            preserveScroll: true,
        });
    };


    const addProduct = () => {
        form.post(route('products.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset()
                isDialogOpen.value = false
                Inertia.reload({ only: ['products'] });
            },
        })
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
                    <!-- <h2 class="text-2xl font-bold tracking-tight">Products</h2> -->
                </div>
               <Dialog v-model:open="isDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Product
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[525px]">
                        <DialogHeader>
                            <DialogTitle>Add New Product</DialogTitle>
                            <DialogDescription>
                                Create a new product for your inventory. Click save when you're done.
                            </DialogDescription>
                        </DialogHeader>
                        <div class="grid gap-4 py-4">
                            <div class="grid gap-2">
                                <Label for="name">Product Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Enter product name"
                                />
                                <p v-if="form.errors.name" class="text-red-600 text-sm">
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="sku">SKU</Label>
                                <Input
                                    id="sku"
                                    v-model="form.sku"
                                    placeholder="Enter unique sku here"
                                />
                                <p v-if="form.errors.sku" class="text-red-600 text-sm">
                                    {{ form.errors.sku }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="price">Price</Label>
                                    <Input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        placeholder="0.00"
                                    />
                                    <p v-if="form.errors.price" class="text-red-600 text-sm">
                                        {{ form.errors.price }}
                                    </p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="stock">Stock</Label>
                                    <Input
                                        id="stock"
                                        v-model="form.stock"
                                        type="number"
                                        placeholder="0"
                                    />
                                    <p v-if="form.errors.stock" class="text-red-600 text-sm">
                                        {{ form.errors.stock }}
                                    </p>
                                </div>
                            </div>
                            <div class="grid gap-2">
                                <Label for="image">Image</Label>
                               <Input
                                    id="image"
                                    type="file"
                                    @change="form.image = $event.target.files[0]"
                                    accept="image/*"
                                />
                                <p v-if="form.errors.image" class="text-red-600 text-sm">
                                    {{ form.errors.image }}
                                </p>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button variant="outline" @click="isDialogOpen = false">
                                Cancel
                            </Button>
                            <Button @click="addProduct">
                                Add Product
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
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
                                        <DropdownMenuItem>Edit</DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem class="text-destructive">
                                            Delete
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
    </AppLayout>
</template>
