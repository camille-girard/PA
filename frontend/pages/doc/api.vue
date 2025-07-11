<template>
    <div class="api-doc-container">
        <ApiDoc :url="apiDocUrl" />
    </div>
</template>

<script setup lang="ts">
    const apiDocUrl = 'http://localhost:8085/api/doc.json';
</script>

<style scoped>
    .api-doc-container {
        width: 100%;
        height: 100vh;
    }
</style>
