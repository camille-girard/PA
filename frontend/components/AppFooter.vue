<script setup lang="ts">
    const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="w-full py-6 mx-auto bg-gray-100/10 mt-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap gap-10 justify-start items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center flex-col gap-2">
                        <ULogo />
                        <span class="text-3xl font-semibold text-primary">PopnBed</span>
                    </div>
                    <ul class="flex items-center justify-center flex-row gap-6 mt-4">
                        <li>
                            <ULink
                                to="https://www.facebook.com/PopnBed"
                                :show-external-icon="false"
                                class="text-white hover:text-gray-300"
                            >
                                <FacebookIcon class="fill-white" />
                            </ULink>
                        </li>
                        <li>
                            <ULink
                                to="https://www.instagram.com/popnbed"
                                :show-external-icon="false"
                                class="text-white hover:text-gray-300"
                            >
                                <InstagramIcon class="fill-white" />
                            </ULink>
                        </li>
                        <li>
                            <ULink
                                to="https://www.linkedin.com/company/popnbed"
                                :show-external-icon="false"
                                class="text-white hover:text-gray-300"
                            >
                                <TwitterIcon class="fill-white" />
                            </ULink>
                        </li>
                    </ul>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-sm font-medium mb-2">À propos</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <ULink to="/about">A propos de nous</ULink>
                            </li>
                            <li>
                                <ULink to="/">Explorer</ULink>
                            </li>
                            <li>
                                <ULink to="/cgv">Conditions Générales de vente</ULink>
                            </li>
                            <li>
                                <ULink to="/mentions-legales">Mentions légales</ULink>
                            </li>
                            <li>© {{ currentYear }} PopnBed, Inc</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped></style>
