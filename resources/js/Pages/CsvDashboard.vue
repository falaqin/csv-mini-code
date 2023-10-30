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

const fetchUploadedFilesApi = async () => {
    await axios.get(route('api.file.index')).then(response => {
        files.value = response.data.files;
    })
};

let pollingTimer;
const startPolling = () => {
    const pollingInterval = 1000;

    // Call the API initially
    fetchUploadedFilesApi();

    // Set up the polling interval
    pollingTimer = setInterval(() => {
        fetchUploadedFilesApi();
    }, pollingInterval);
};

onMounted(() => {
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
