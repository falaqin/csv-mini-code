<script setup>
import moment from 'moment';

const props = defineProps({
    items: {
        default: []
    }
})
</script>
<template>
    <table class="border-collapse table-auto w-3/4 text-sm">
        <thead>
            <tr>
                <th
                    class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Time
                </th>
                <th
                    class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Filename
                </th>
                <th
                    class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Status
                </th>
                <th
                    class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Completion (%)
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-slate-800">
            <tr v-for="item in items" :key="item">
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                    {{ moment(item.created_at).format('MMMM Do YYYY, h:mm:ss a') }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                    {{ item.original_filename }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                    {{ item.status }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                    {{ item.completion_percentage }} %
                </td>
            </tr>
        </tbody>
    </table>
</template>
