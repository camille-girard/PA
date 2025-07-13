<script setup lang="ts">
    interface Host {
        name: string;
        image: string;
        verified: boolean;
        rating: number;
        evaluations: number;
        membershipDuration: string;
        verifications: string[];
        accommodationCount: number;
        location?: string;
        profession?: string;
        languages?: string;
        responseRate?: number;
        responseTime?: string;
        joinDate: string;
    }

    defineProps<{ host: Host }>();
</script>

<template>
    <div class="space-y-8">
        <div class="bg-orange-100 rounded-2xl shadow-md p-6 flex flex-col items-center text-center">
            <UAvatar 
                :image-src="host.image" 
                :text="host.name.split(' ').map(n => n[0]).join('')" 
                size="2xl" 
                class="mb-4"
            />

            <div>
                <h3 class="text-h3 text-gray-800">{{ host.name }}</h3>
                <p class="text-body-md text-gray-600">{{ host.verified ? 'Hôte vérifié' : 'Non vérifié' }}</p>
            </div>

            <div class="mt-6 text-gray-700 space-y-1">
                <p>
                    Notes : <span class="text-body-md font-semibold">{{ host.rating }}/5</span>
                </p>
                <p>Hôte passionné<br />depuis {{ host.membershipDuration }}</p>
            </div>
        </div>

        <div class="border border-orange-300 rounded-2xl p-6 space-y-4">
            <h4 class="text-h4 text-gray-800">Vérifications effectuées par {{ host.name }}</h4>
            <ul class="space-y-2">
                <li v-for="(verification, index) in host.verifications" :key="index" class="flex items-center gap-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-black"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-body-md font-semibold">{{ verification }}</span>
                </li>
            </ul>
        </div>

        <div class="flex items-center gap-2">
            <img src="/signal.svg" alt="signal" class="w-6 h-6" />
            <a href="#" class="underline text-body-md font-semibold text-black">Signaler ce profil</a>
        </div>
    </div>
</template>
