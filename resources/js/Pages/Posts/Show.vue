<script setup>
import { ref, onMounted } from 'vue';
import AppLayoutUnauth from '@/Layouts/AppLayoutUnauth.vue';

const item = ref([]);
const loading = ref(true);
const error = ref(null);
const error_code = ref(null);
const recommendations = ref(null)
const name = ref('posts')

const fetchRecommendations = async () => {
    const currentUrl = window.location.href;
    axios.get(currentUrl + "?recommendations")
        .then(res => {
            recommendations.value = res.data;
        });
}

const fetchItem = () => {
    const currentUrl = window.location.href;
    axios.get(currentUrl)
        .then(res => {
            item.value = res.data;
            fetchRecommendations()
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
    fetchItem();
});
</script>

<template>
    <AppLayoutUnauth
        :title="`[${trans(name).toUpperCase()}] ${loading ? trans('loading') : item.title ?? trans('not_found')}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <div v-if="loading">{{ trans('loading') }}</div>
                <div v-else-if="error_code" class="error">{{ trans(error_code) }}</div>
                <div v-else>{{ item.title }}</div>
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="loading"></div>
                <div v-else-if="error" class="error">{{ trans(error) }}</div>
                <div v-else>
                    <img v-if="item.image" :src="item.image" class="view-image" />
                    <div v-html="item.html"></div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div v-if="loading"></div>
            <div v-else-if="error" class="error"></div>
            <div v-else>
                <hr />
                <h2 class="font-semibold text-xl text-gray-800 leading-tight py-5">{{ trans('recommendations')
                    }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="item in recommendations" :key="item.id" class="bg-white shadow-lg rounded-lg p-5">
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
            </div>
        </div>
    </AppLayoutUnauth>
</template>
