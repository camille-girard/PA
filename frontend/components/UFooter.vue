<script setup lang="ts">
    const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="w-full bg-gray-50 mt-20 py-10">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-start gap-8">
            <div class="flex flex-col items-start gap-4">
                <div>
                    <NuxtLink to="/" class="flex items-center gap-2">
                        <ULogo />
                        <span class="text-body-sm font-semibold">PopnBed</span>
                    </NuxtLink>
                </div>
                <div class="flex gap-4">
                    <a href="#" class="w-9 h-9 flex items-center justify-center">
                        <img src="/public/Logo_Facebook.svg" alt="Facebook" class="w-5 h-5" />
                    </a>
                    <a href="#" class="w-9 h-9 flex items-center justify-center">
                        <img src="/public/Insta.svg" alt="Instagram" class="w-5 h-5" />
                    </a>
                    <a href="#" class="w-9 h-9 flex items-center justify-center">
                        <img src="/public/Twitter.svg" alt="X / Twitter" class="w-5 h-5" />
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2 text-sm text-gray-700">
                <ULink to="/thematiques">Thématiques</ULink>
                <ULink to="/explorer">Explorer</ULink>
                <ULink to="#">Conditions Générales de vente</ULink>
                <ULink to="#">Plan du site</ULink>
                <p class="text-body-sm text-gray-500 mt-4">© {{ currentYear }} PopnBed, Inc</p>
            </div>
        </div>
    </footer>
</template>
