<script setup>
import AlertCircleIcon from '~/components/atoms/icons/AlertCircleIcon.vue'
import ArrowDownIcon from '~/components/atoms/icons/ArrowDownIcon.vue'
import ArrowLeftIcon from '~/components/atoms/icons/ArrowLeftIcon.vue'
import ArrowUpIcon from '~/components/atoms/icons/ArrowUpIcon.vue'
import BookOpenIcon from '~/components/atoms/icons/BookOpenIcon.vue'
import BriefcaseIcon from '~/components/atoms/icons/BriefcaseIcon.vue'
import BuildingIcon from '~/components/atoms/icons/BuildingIcon.vue'
import CalendarIcon from '~/components/atoms/icons/CalendarIcon.vue'
import ChartIcon from '~/components/atoms/icons/ChartIcon.vue'
import CheckCircleIcon from '~/components/atoms/icons/CheckCircleIcon.vue'
import CheckFillIcon from '~/components/atoms/icons/CheckFillIcon.vue'
import ChevronLeftIcon from '~/components/atoms/icons/ChevronLeftIcon.vue'
import ChevronRightIcon from '~/components/atoms/icons/ChevronRightIcon.vue'
import ChevronSelectorIcon from '~/components/atoms/icons/ChevronSelectorIcon.vue'
import ChevronUpIcon from '~/components/atoms/icons/ChevronUpIcon.vue'
import ChevronDownIcon from '~/components/atoms/icons/ChrevronDownIcon.vue'
import ClipboardCheckIcon from '~/components/atoms/icons/ClipboardCheckIcon.vue'
import CoinsHandIcon from '~/components/atoms/icons/CoinsHandIcon.vue'
import DiamondIcon from '~/components/atoms/icons/DiamondIcon.vue'
import FileIcon from '~/components/atoms/icons/FileIcon.vue'
import FolderIcon from '~/components/atoms/icons/FolderIcon.vue'
import HeartIcon from '~/components/atoms/icons/HeartIcon.vue'
import HelpCircleIcon from '~/components/atoms/icons/HelpCircleIcon.vue'
import HomeIcon from '~/components/atoms/icons/HomeIcon.vue'
import IMGIcon from '~/components/atoms/icons/IMGIcon.vue'
import InfoCircleIcon from '~/components/atoms/icons/InfoCircleIcon.vue'
import JPEGIcon from '~/components/atoms/icons/JPEGIcon.vue'
import JPGIcon from '~/components/atoms/icons/JPGIcon.vue'
import KeyIcon from '~/components/atoms/icons/KeyIcon.vue'
import LightningIcon from '~/components/atoms/icons/LightningIcon.vue'
import LockIcon from '~/components/atoms/icons/LockIcon.vue'
import LogoutIcon from '~/components/atoms/icons/LogoutIcon.vue'
import MailIcon from '~/components/atoms/icons/MailIcon.vue'
import MenuIcon from '~/components/atoms/icons/MenuIcon.vue'
import MessageChatCircleIcon from '~/components/atoms/icons/MessageChatCircleIcon.vue'
import MessageIcon from '~/components/atoms/icons/MessageIcon.vue'
import MinusIcon from '~/components/atoms/icons/MinusIcon.vue'
import PDFIcon from '~/components/atoms/icons/PDFIcon.vue'
import PNGIcon from '~/components/atoms/icons/PNGIcon.vue'
import PasscodeIcon from '~/components/atoms/icons/PasscodeIcon.vue'
import PhoneIcon from '~/components/atoms/icons/PhoneIcon.vue'
import PinIcon from '~/components/atoms/icons/PinIcon.vue'
import PlayCircleIcon from '~/components/atoms/icons/PlayCircleIcon.vue'
import PlusIcon from '~/components/atoms/icons/PlusIcon.vue'
import ReceiptCheckIcon from '~/components/atoms/icons/ReceiptCheckIcon.vue'
import RepeatIcon from '~/components/atoms/icons/RepeatIcon.vue'
import SVGIcon from '~/components/atoms/icons/SVGIcon.vue'
import SaveIcon from '~/components/atoms/icons/SaveIcon.vue'
import SearchIcon from '~/components/atoms/icons/SearchIcon.vue'
import SettingsIcon from '~/components/atoms/icons/SettingsIcon.vue'
import TicketIcon from '~/components/atoms/icons/TicketIcon.vue'
import UploadCloudIcon from '~/components/atoms/icons/UploadCloudIcon.vue'
import UserIcon from '~/components/atoms/icons/UserIcon.vue'
import VerifiedTickIcon from '~/components/atoms/icons/VerifiedTickIcon.vue'
import XIcon from '~/components/atoms/icons/XIcon.vue'
import ZapFastIcon from '~/components/atoms/icons/ZapFastIcon.vue'

const icons = [
  { name: 'AlertCircleIcon', component: AlertCircleIcon },
  { name: 'ArrowDownIcon', component: ArrowDownIcon },
  { name: 'ArrowLeftIcon', component: ArrowLeftIcon },
  { name: 'ArrowUpIcon', component: ArrowUpIcon },
  { name: 'BookOpenIcon', component: BookOpenIcon },
  { name: 'BriefcaseIcon', component: BriefcaseIcon },
  { name: 'BuildingIcon', component: BuildingIcon },
  { name: 'CalendarIcon', component: CalendarIcon },
  { name: 'ChartIcon', component: ChartIcon },
  { name: 'CheckCircleIcon', component: CheckCircleIcon },
  { name: 'CheckFillIcon', component: CheckFillIcon },
  { name: 'ChevronLeftIcon', component: ChevronLeftIcon },
  { name: 'ChevronRightIcon', component: ChevronRightIcon },
  { name: 'ChevronSelectorIcon', component: ChevronSelectorIcon },
  { name: 'ChevronUpIcon', component: ChevronUpIcon },
  { name: 'ChevronDownIcon', component: ChevronDownIcon },
  { name: 'ClipboardCheckIcon', component: ClipboardCheckIcon },
  { name: 'CoinsHandIcon', component: CoinsHandIcon },
  { name: 'DiamondIcon', component: DiamondIcon },
  { name: 'FileIcon', component: FileIcon },
  { name: 'FolderIcon', component: FolderIcon },
  { name: 'HeartIcon', component: HeartIcon },
  { name: 'HelpCircleIcon', component: HelpCircleIcon },
  { name: 'HomeIcon', component: HomeIcon },
  { name: 'IMGIcon', component: IMGIcon },
  { name: 'InfoCircleIcon', component: InfoCircleIcon },
  { name: 'JPEGIcon', component: JPEGIcon },
  { name: 'JPGIcon', component: JPGIcon },
  { name: 'KeyIcon', component: KeyIcon },
  { name: 'LightningIcon', component: LightningIcon },
  { name: 'LockIcon', component: LockIcon },
  { name: 'LogoutIcon', component: LogoutIcon },
  { name: 'MailIcon', component: MailIcon },
  { name: 'MenuIcon', component: MenuIcon },
  { name: 'MessageChatCircleIcon', component: MessageChatCircleIcon },
  { name: 'MessageIcon', component: MessageIcon },
  { name: 'MinusIcon', component: MinusIcon },
  { name: 'PDFIcon', component: PDFIcon },
  { name: 'PNGIcon', component: PNGIcon },
  { name: 'PasscodeIcon', component: PasscodeIcon },
  { name: 'PhoneIcon', component: PhoneIcon },
  { name: 'PinIcon', component: PinIcon },
  { name: 'PlayCircleIcon', component: PlayCircleIcon },
  { name: 'PlusIcon', component: PlusIcon },
  { name: 'ReceiptCheckIcon', component: ReceiptCheckIcon },
  { name: 'RepeatIcon', component: RepeatIcon },
  { name: 'SVGIcon', component: SVGIcon },
  { name: 'SaveIcon', component: SaveIcon },
  { name: 'SearchIcon', component: SearchIcon },
  { name: 'SettingsIcon', component: SettingsIcon },
  { name: 'TicketIcon', component: TicketIcon },
  { name: 'UploadCloudIcon', component: UploadCloudIcon },
  { name: 'UserIcon', component: UserIcon },
  { name: 'VerifiedTickIcon', component: VerifiedTickIcon },
  { name: 'XIcon', component: XIcon },
  { name: 'ZapFastIcon', component: ZapFastIcon },
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
