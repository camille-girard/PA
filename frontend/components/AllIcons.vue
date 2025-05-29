<script setup>
// Importation manuelle de toutes les icônes
import AlertCircleIcon from '~/components/atoms/icons/AlertCircleIcon.vue'
import ArrowDownIcon from '~/components/atoms/icons/ArrowDownIcon.vue'
import ArrowLeftIcon from '~/components/atoms/icons/ArrowLeftIcon.vue'
import ArrowUpIcon from '~/components/atoms/icons/ArrowUpIcon.vue'
import BuildingIcon from '~/components/atoms/icons/BuildingIcon.vue'
import CalendarIcon from '~/components/atoms/icons/CalendarIcon.vue'
import ChartIcon from '~/components/atoms/icons/ChartIcon.vue'
import CheckCircleIcon from '~/components/atoms/icons/CheckCircleIcon.vue'
import ChevronLeftIcon from '~/components/atoms/icons/ChevronLeftIcon.vue'
import ChevronRightIcon from '~/components/atoms/icons/ChevronRightIcon.vue'
import ChevronSelectorIcon from '~/components/atoms/icons/ChevronSelectorIcon.vue'
import ChevronUpIcon from '~/components/atoms/icons/ChevronUpIcon.vue'
import CoinsHandIcon from '~/components/atoms/icons/CoinsHandIcon.vue'
import DiamondIcon from '~/components/atoms/icons/DiamondIcon.vue'
import HeartIcon from '~/components/atoms/icons/HeartIcon.vue'
import HomeIcon from '~/components/atoms/icons/HomeIcon.vue'
import InfoCircleIcon from '~/components/atoms/icons/InfoCircleIcon.vue'
import LogoutIcon from '~/components/atoms/icons/LogoutIcon.vue'
import MenuIcon from '~/components/atoms/icons/MenuIcon.vue'
import MessageIcon from '~/components/atoms/icons/MessageIcon.vue'
import MinusIcon from '~/components/atoms/icons/MinusIcon.vue'
import PinIcon from '~/components/atoms/icons/PinIcon.vue'
import PlusIcon from '~/components/atoms/icons/PlusIcon.vue'
import SaveIcon from '~/components/atoms/icons/SaveIcon.vue'
import SearchIcon from '~/components/atoms/icons/SearchIcon.vue'
import SettingsIcon from '~/components/atoms/icons/SettingsIcon.vue'
import TicketIcon from '~/components/atoms/icons/TicketIcon.vue'
import UserIcon from '~/components/atoms/icons/UserIcon.vue'
import VerifiedTickIcon from '~/components/atoms/icons/VerifiedTickIcon.vue'
import XIcon from '~/components/atoms/icons/XIcon.vue'
import ChrevronDownIcon from "~/components/atoms/icons/ChrevronDownIcon.vue";

const icons = [
  { name: 'AlertCircleIcon', component: AlertCircleIcon },
  { name: 'ArrowDownIcon', component: ArrowDownIcon },
  { name: 'ArrowLeftIcon', component: ArrowLeftIcon },
  { name: 'ArrowUpIcon', component: ArrowUpIcon },
  { name: 'BuildingIcon', component: BuildingIcon },
  { name: 'CalendarIcon', component: CalendarIcon },
  { name: 'ChartIcon', component: ChartIcon },
  { name: 'CheckCircleIcon', component: CheckCircleIcon },
  { name: 'ChevronLeftIcon', component: ChevronLeftIcon },
  { name: 'ChevronRightIcon', component: ChevronRightIcon },
  { name: 'ChevronSelectorIcon', component: ChevronSelectorIcon },
  { name: 'ChevronUpIcon', component: ChevronUpIcon },
  { name: 'ChrevronDownIcon', component: ChrevronDownIcon },
  { name: 'CoinsHandIcon', component: CoinsHandIcon },
  { name: 'DiamondIcon', component: DiamondIcon },
  { name: 'HeartIcon', component: HeartIcon },
  { name: 'HomeIcon', component: HomeIcon },
  { name: 'InfoCircleIcon', component: InfoCircleIcon },
  { name: 'LogoutIcon', component: LogoutIcon },
  { name: 'MenuIcon', component: MenuIcon },
  { name: 'MessageIcon', component: MessageIcon },
  { name: 'MinusIcon', component: MinusIcon },
  { name: 'PinIcon', component: PinIcon },
  { name: 'PlusIcon', component: PlusIcon },
  { name: 'SaveIcon', component: SaveIcon },
  { name: 'SearchIcon', component: SearchIcon },
  { name: 'SettingsIcon', component: SettingsIcon },
  { name: 'TicketIcon', component: TicketIcon },
  { name: 'UserIcon', component: UserIcon },
  { name: 'VerifiedTickIcon', component: VerifiedTickIcon },
  { name: 'XIcon', component: XIcon },
]
</script>

<template>
  <div class="grid grid-cols-4 gap-6 p-6">
    <div
        v-for="icon in icons"
        :key="icon.name"
        class="flex flex-col items-center justify-center text-center"
    >
      <component :is="icon.component" class="w-8 h-8 text-gray-700" />
      <span class="mt-2 text-xs text-gray-500">{{ icon.name }}</span>
    </div>
  </div>
</template>
