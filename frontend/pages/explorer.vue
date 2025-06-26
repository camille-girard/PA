<script setup lang="ts"></script>
<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Explorez votre prochaine destination</h1>
                        <p class="mt-4">Découvrez le lieu de vos prochaines vacances</p>
                    </div>
                </div>
            </section>

            <section class="w-full pt-12">
                <div class="h-[500px] w-full overflow-hidden rounded-xl shadow-lg">
                    <ClientOnly>
                        <MapBox />
                    </ClientOnly>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
