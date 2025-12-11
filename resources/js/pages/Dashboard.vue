<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
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
import { Badge } from '@/components/ui/badge';
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
const products = ref([
    {
        id: 'PRD001',
        name: 'Wireless Mouse',
        category: 'Electronics',
        status: 'Active',
        price: 29.99,
        stock: 145,
    },
    {
        id: 'PRD002',
        name: 'Mechanical Keyboard',
        category: 'Electronics',
        status: 'Active',
        price: 89.99,
        stock: 67,
    },
    {
        id: 'PRD003',
        name: 'USB-C Hub',
        category: 'Accessories',
        status: 'Active',
        price: 49.99,
        stock: 203,
    },
    {
        id: 'PRD004',
        name: 'Laptop Stand',
        category: 'Accessories',
        status: 'Inactive',
        price: 39.99,
        stock: 0,
    },
    {
        id: 'PRD005',
        name: 'Webcam HD',
        category: 'Electronics',
        status: 'Active',
        price: 79.99,
        stock: 89,
    },
]);
const form = useForm({
  name: '',
  sku: '',
  price: '',
  stock: '',
  image: null,
})


const addProduct = () => {
    form.post(route('products.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset()
            isDialogOpen.value = false
        },
    })
};



</script>

<template>
    <Head title="Dashboard" />

     <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Products</h2>
                    <p class="text-muted-foreground">Manage your product inventory</p>
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
                            <TableHead class="w-[100px]">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Price</TableHead>
                            <TableHead class="text-right">Stock</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="product in products" :key="product.id">
                            <TableCell class="font-medium">{{ product.id }}</TableCell>
                            <TableCell>{{ product.name }}</TableCell>
                            <TableCell>{{ product.category }}</TableCell>
                            <TableCell>
                                <Badge :variant="product.status === 'Active' ? 'default' : 'secondary'">
                                    {{ product.status }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">${{ product.price.toFixed(2) }}</TableCell>
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
                                        <DropdownMenuItem>Duplicate</DropdownMenuItem>
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
            </div>
        </div>
    </AppLayout>
</template>
