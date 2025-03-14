<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <h1 class="text-xl font-bold">Book copies</h1>
                <div class="bg-white">
                    <ResponsiveTable :data="book_copies.data" :headers="headers">
                        <template #actions="actionProps">
                            <DropdownLink
                                method="post"
                                :href="route('borrow-requests.store', actionProps.row.id)"
                                class="flex items-center space-x-2"
                                :data="{
                                    book_copy_id: actionProps.row.id
                                }"
                            >
                                <span>Request</span>
                            </DropdownLink>
                        </template>
                    </ResponsiveTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ResponsiveTable from "@/Components/ResponsiveTable.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
</script>

<script>
import {collect} from "collect.js";

export default {
    props: ['book_copies'],
    computed: {
        headers() {
            return {
                id: 'ID',
                name: {label: 'Name', displayAs: (bookCopy) => bookCopy.book.name},
                authors: {label: 'Authors', displayAs: (bookCopy) => collect(bookCopy.book.authors).pluck('name').join(', ')},
            };
        }
    }
}
</script>
