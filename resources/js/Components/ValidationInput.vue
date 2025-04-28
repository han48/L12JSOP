<template>
    <span v-if="form.validating" class="icon-validate icon-validating">
    </span>
    <span v-else-if="form.valid(name)" class="icon-validate icon-validate-success">
    </span>
    <span v-else-if="form.invalid(name)" class="icon-validate icon-validate-fail">
    </span>
</template>

<script>
export default {
    props: {
        name: {
            type: String,
            required: true
        },
        form: {
            type: Object,
            required: true
        }
    }

};
</script>

<style>
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    40% {
        transform: rotate(180deg);
    }

    50% {
        transform: rotate(180deg);
    }

    60% {
        transform: rotate(180deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.icon-validate {
    position: relative;
    width: 1em;
    height: 1em;
    margin-bottom: -1em;
    display: grid;
    top: -2em;
    right: 1.4em;
}

.icon-validating::after {
    content: '⏳';
    animation: spin 1s linear infinite;
}

.icon-validate-success::after {
    content: '✅';
}

.icon-validate-fail::after {
    content: '❌';
}
</style>
