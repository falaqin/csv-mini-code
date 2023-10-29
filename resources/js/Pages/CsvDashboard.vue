<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import NoAuthLayout from '@/Layouts/NoAuthLayout.vue';
import DropFile from '@/Components/DropFile.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    uploaded_files: {
        default: []
    }
})

let files = ref(props.uploaded_files);
const newFilesIncoming = (newFile) => {
    uploadFile(newFile);
}

const uploadFile = (file) => {
    let formData = new FormData();

    // since we want to upload files one by one, just iterate the first index of the object
    if (typeof file == 'object' && file !== null) {
        formData.append('file', file[0]);
    } else {
        throw new Error('File is invalid');
    }

    axios.post(route('api.file.upload'), formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        }

    }).then(response => {
        // TODO Show toast or indicator as the file has been uploaded
        console.log(response);
    })
}

// TODO Polling actual data
let data = ref(null);
let pollingTimer;
const fetchUploadedFilesApi = () => {
    // Make your API request here (use async/await or Promises as needed)
    // For simplicity, this example uses a mock API request
    setTimeout(() => {
        data.value = "Updated data from the API";
    }, 2000); // Simulate a 2-second API request
};

const startPolling = () => {
    // Set the polling interval (e.g., every 5 seconds)
    const pollingInterval = 3000; // 5 seconds in milliseconds

    // Call the API initially
    fetchUploadedFilesApi();
    console.log(data.value);

    // Set up the polling interval
    pollingTimer = setInterval(() => {
        fetchUploadedFilesApi();
        console.log(data.value);
    }, pollingInterval);
};

onMounted(() => {
    // Start polling when the component is mounted
    startPolling();
});

onBeforeUnmount(() => {
    // Clean up the polling interval when the component is unmounted
    if (pollingTimer) {
        clearInterval(pollingTimer);
    }
});
</script>

<template>
    <NoAuthLayout>
        <DropFile @update:files="newFilesIncoming"/>
        <div class="grid place-items-center">
            <Table :items="files" class="mt-6"/>
        </div>
    </NoAuthLayout>
</template>
