<script setup>
import { ref, onMounted } from 'vue';
import AppLayoutUnauth from '@/Layouts/AppLayoutUnauth.vue';

const items = ref([]);
const loading = ref(true);
const error = ref(null);
const error_code = ref(null);
const urlNextItems = ref(null);
const urlPrevItems = ref(null);
const pathname = ref(window.location.pathname)
const name = ref('posts')

const fetchItems = () => {
    const currentUrl = window.location.href;
    axios.get(currentUrl)
        .then(res => {
            urlNextItems.value = res.data.next_page_url;
            urlPrevItems.value = res.data.prev_page_url;
            items.value = res.data.data;
        })
        .catch(err => {
            if (err.response) {
                error.value = err.response.statusText;
                error_code.value = err.response.status;
            } else {
                error.value = err.message;
                error_code.value = err.code;
            }
        })
        .finally(() => {
            loading.value = false;
        });
};

onMounted(() => {
    fetchItems();
});
</script>

<template>
    <AppLayoutUnauth :title="`[${ptrans(name).toUpperCase()}] ${loading ? ptrans('loading') : ''}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <div v-if="loading">{{ ptrans('loading') }}</div>
                <div v-else-if="error_code" class="error">{{ trans(error_code) }}</div>
                <div v-else>{{ ptrans(name) }}</div>
            </h2>
        </template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div v-if="loading"></div>
            <div v-else-if="error" class="error">{{ trans(error) }}</div>
            <div v-else>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="item in items" :key="item.id" class="bg-white shadow-lg rounded-lg p-5">
                        <h2 class="text-xl font-bold mt-3">
                            <div>{{ item.title }}</div>
                        </h2>
                        <div :style="{ backgroundImage: `url(${item.image ?? '/images/no_image.jpg'})` }"
                            class="view-thumbnail">
                        </div>
                        <p class="text-gray-600 view-summary">{{ item.description }}</p>
                        <a :href="`${pathname}/${item.id}`" class="text-blue-500 mt-3 inline-block">{{
                            trans('read_more') }}</a>
                    </div>
                </div>
                <div class="flex justify-between mt-5">
                    <a v-if="urlPrevItems" :href="urlPrevItems" class="px-4 py-2 bg-gray-200 rounded">{{
                        trans('prev_page')
                        }}</a>
                    <a v-if="urlNextItems" :href="urlNextItems" class="px-4 py-2 bg-gray-200 rounded">{{
                        trans('next_page')
                        }}</a>
                </div>
            </div>
        </div>
    </AppLayoutUnauth>
</template>
