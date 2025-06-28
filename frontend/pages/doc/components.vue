<script setup lang="ts">
    import AlertCircleIcon from '~/components/atoms/icons/AlertCircleIcon.vue';
    import SaveIcon from '~/components/atoms/icons/SaveIcon.vue';
    import SettingsIcon from '~/components/atoms/icons/SettingsIcon.vue';
    import UserIcon from '~/components/atoms/icons/UserIcon.vue';
    import UCalendar from '~/components/organisms/UCalendar.vue';
    import type { Column } from '~/components/organisms/UTable.vue';
    import type { Toast } from '~/types/toast';

    const authStore = useAuthStore();

    type CheckboxIndeterminateProp = boolean | 'indeterminate';

    const checkbox = ref<CheckboxIndeterminateProp>('indeterminate');
    const switchValue = ref<boolean>(false);
    const isModalOpen = ref(false);
    const isSecondModalOpen = ref(false);
    const numberValue = ref(0);
    const selectedDate = ref(new Date());

    const toast = useToast();

    function showSuccessToast() {
        toast.success('Well done!', 'Operation completed successfully!');
    }

    function showErrorToast() {
        toast.error('Oups !', 'Une erreur est survenue.');
    }

    function showWarningToast() {
        toast.warning('Attention !', 'Tu vas te faire mal si tu tombes du lit');
    }

    function showInfoToast() {
        toast.info('Petite inforamtion', 'Tu vas devenir riche très bientôt.');
    }

    const toastInfo: Toast = {
        id: 'idTest',
        title: 'Title',
        message: 'message',
        type: 'info',
        createdAt: Date.now(),
    };

    const toastSuccess: Toast = {
        id: 'idTest',
        title: 'Title',
        message: 'message',
        type: 'success',
        createdAt: Date.now(),
    };

    const toastError: Toast = {
        id: 'idTest',
        title: 'Title',
        message: 'message',
        type: 'error',
        createdAt: Date.now(),
    };

    const toastWarning: Toast = {
        id: 'idTest',
        title: 'Title',
        message: 'message',
        type: 'warning',
        createdAt: Date.now(),
    };

    const columns: Column[] = [
        { key: 'name', label: 'Name', sortable: true },
        { key: 'email', label: 'Email' },
        { key: 'role', label: 'Role' },
        { key: 'status', label: 'Status' },
    ];

    type User = {
        avatar: string;
        name: string;
        email: string;
        role: string;
        status: string;
    };

    const users: User[] = [
        {
            avatar: '/avatar-test.png',
            name: 'John Doe',
            email: 'john@example.com',
            role: 'Admin',
            status: 'Active',
        },
        {
            avatar: '/avatar-test.png',
            name: 'Jane Doe',
            email: 'jane@example.com',
            role: 'Student',
            status: 'Disabled',
        },
    ];

    function handleRowClick(row: Record<string, unknown>) {
        alert(`Row clicked: ${row.name}`);
    }
</script>

<template>
    <main class="h-full w-full flex flex-col items-center gap-8 p-8">
        <!-- Section title -->
        <h1 class="text-2xl font-bold mb-8 text-primary">Composants UI</h1>

        <!-- Buttons section -->
        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Boutons</h2>

            <div class="flex flex-col gap-6 p-4 bg-gray-100/10 rounded-lg">
                <div class="flex flex-col gap-2">
                    <h3 class="text-primary font-medium">Primary</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <UButton size="xl">Button xl</UButton>
                        <UButton size="lg">Button lg</UButton>
                        <UButton>Button md</UButton>
                        <UButton size="sm">Button sm</UButton>
                        <UButton size="sm" :disabled="true">Button sm disabled</UButton>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="text-secondary font-medium">Secondary</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <UButton variant="secondary" size="xl">Button xl</UButton>
                        <UButton variant="secondary" size="lg">Button lg</UButton>
                        <UButton variant="secondary">Button md</UButton>
                        <UButton variant="secondary" size="sm">Button sm</UButton>
                        <UButton variant="secondary" size="sm" :disabled="true">Button sm disabled</UButton>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="text-tertiary font-medium">Tertiary</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <UButton variant="tertiary" size="xl">Button xl</UButton>
                        <UButton variant="tertiary" size="lg">Button lg</UButton>
                        <UButton variant="tertiary">Button md</UButton>
                        <UButton variant="tertiary" size="sm">Button sm</UButton>
                        <UButton variant="tertiary" size="sm" :disabled="true">Button sm disabled</UButton>
                    </div>
                </div>
            </div>
        </section>

        <!-- Links section -->
        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Liens</h2>
            <div class="flex flex-wrap items-center gap-4 p-4 bg-gray-100/10 rounded-lg">
                <ULink to="https://google.com/">Google</ULink>
                <ULink to="/login" size="sm">Login</ULink>
                <ULink to="#" size="sm">Ancre</ULink>
                <ULink to="#" :disabled="true" size="sm">Login disabled</ULink>
            </div>
        </section>

        <!-- Dropdown section -->
        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Dropdown</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg">
                <UDropdown
                    label="Account"
                    :menu-items="[
                        {
                            label: 'View profile',
                            icon: UserIcon,
                        },
                        {
                            label: 'Settings',
                            icon: SettingsIcon,
                        },
                    ]"
                />
            </div>
        </section>

        <!-- Badges section -->
        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Badges</h2>
            <div class="flex flex-col gap-4 p-4 bg-gray-100/10 rounded-lg">
                <div class="flex flex-col gap-2">
                    <h3 class="font-medium text-primary">Tailles</h3>
                    <div class="flex items-center gap-2">
                        <UBadge size="sm">Small</UBadge>
                        <UBadge>Medium</UBadge>
                        <UBadge size="lg">Large</UBadge>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <h3 class="font-medium text-primary">Couleurs</h3>
                    <div class="flex items-center gap-2">
                        <UBadge variant="badge" color="error">Error</UBadge>
                        <UBadge variant="badge" color="success">Success</UBadge>
                        <UBadge variant="badge" color="warning">Warning</UBadge>
                        <UBadge variant="badge" color="brand">Brand</UBadge>
                    </div>
                </div>
            </div>
        </section>

        <!-- Input section -->
        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Inputs</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UInput
                    type="email"
                    placeholder="Email"
                    label="Email"
                    name="email"
                    hint-text="This is a hint text to help user."
                    required
                />
                <UInput
                    type="email"
                    placeholder="Email"
                    label="Email"
                    name="email"
                    hint-text="This is a hint text to help user."
                    destructive
                    required
                />
                <UInputNumber
                    v-model="numberValue"
                    :min="0"
                    :max="100"
                    :step="5"
                    label="Quantity"
                    hint-text="Enter a value between 0 and 100"
                />
            </div>
        </section>

        <!-- Authentication section -->
        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Authentication</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg">
                <LogoutButton v-if="authStore.isAuthenticated" />
                <p v-else class="text-gray-500">Utilisateur non connecté</p>
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Tabs</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UTabs
                    :full-width="true"
                    :items="[
                        { name: 'account', label: 'Account' },
                        { name: 'security', label: 'Security' },
                        { name: 'notifications', label: 'Notifications' },
                    ]"
                >
                    <template #account>Account content</template>
                    <template #security>Security content</template>
                    <template #notifications>Notifications content</template>
                </UTabs>
                <UTabs
                    variant="gray"
                    :full-width="true"
                    :items="[
                        { name: 'account', label: 'Account' },
                        { name: 'security', label: 'Security' },
                        { name: 'notifications', label: 'Notifications' },
                    ]"
                >
                    <template #account>Account content</template>
                    <template #security>Security content</template>
                    <template #notifications>Notifications content</template>
                </UTabs>
                <UTabs
                    variant="underline"
                    :full-width="true"
                    :items="[
                        { name: 'account', label: 'Account' },
                        { name: 'security', label: 'Security' },
                        { name: 'notifications', label: 'Notifications' },
                    ]"
                >
                    <template #account>Account content</template>
                    <template #security>Security content</template>
                    <template #notifications>Notifications content</template>
                </UTabs>
                <UTabs
                    variant="border"
                    :full-width="true"
                    :items="[
                        { name: 'account', label: 'Account' },
                        { name: 'security', label: 'Security' },
                        { name: 'notifications', label: 'Notifications' },
                    ]"
                >
                    <template #account>Account content</template>
                    <template #security>Security content</template>
                    <template #notifications>Notifications content</template>
                </UTabs>
                <UTabs
                    variant="minimal"
                    :full-width="false"
                    :items="[
                        { name: 'account', label: 'Account' },
                        { name: 'security', label: 'Security' },
                        { name: 'notifications', label: 'Notifications' },
                    ]"
                >
                    <template #account>Account content</template>
                    <template #security>Security content</template>
                    <template #notifications>Notifications content</template>
                </UTabs>
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Checkbox</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UCheckbox v-model="checkbox" name="test" />
                <UCheckbox v-model="checkbox" name="test" />
                <UCheckbox v-model="checkbox" name="test" disabled />
                <UCheckbox v-model="checkbox" name="test" label="Test Label" supporting-label="Test supporting label" />
                <UCheckbox
                    v-model="checkbox"
                    size="md"
                    name="test"
                    label="Test Label"
                    supporting-label="Test supporting label"
                />
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Loading spinner</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <ULoading />
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Switch</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <h3 class="text-primary font-medium">Size: SM</h3>
                <USwitch v-model="switchValue" />
                <USwitch v-model="switchValue" label="Remember me" />
                <USwitch
                    v-model="switchValue"
                    label="Remember me"
                    supporting-label="Save my login details for next time."
                />
            </div>

            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <h3 class="font-medium text-primary">Size: MD</h3>
                <USwitch v-model="switchValue" size="md" />
                <USwitch v-model="switchValue" size="md" label="Remember me" />
                <USwitch
                    v-model="switchValue"
                    size="md"
                    label="Remember me"
                    supporting-label="Save my login details for next time."
                />
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Avatar</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <h3 class="font-medium text-primary">Avec image</h3>
                        <div class="flex items-center gap-4">
                            <UAvatar size="xs" image-src="/avatar-test.png" />
                            <UAvatar size="sm" image-src="/avatar-test.png" />
                            <UAvatar size="md" image-src="/avatar-test.png" />
                            <UAvatar size="lg" image-src="/avatar-test.png" />
                            <UAvatar size="xl" image-src="/avatar-test.png" />
                            <UAvatar size="2xl" image-src="/avatar-test.png" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-medium text-primary">Sans image</h3>
                        <div class="flex items-center gap-4">
                            <UAvatar size="xs" />
                            <UAvatar size="sm" />
                            <UAvatar size="md" />
                            <UAvatar size="lg" />
                            <UAvatar size="xl" />
                            <UAvatar size="2xl" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-medium text-primary">Avec texte</h3>
                        <div class="flex items-center gap-4">
                            <UAvatar size="xs" text="JD" />
                            <UAvatar size="sm" text="AB" />
                            <UAvatar size="md" text="NL" />
                            <UAvatar size="lg" text="JD" />
                            <UAvatar size="xl" text="AB" />
                            <UAvatar size="2xl" text="NL" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-medium text-primary">Avec badge vérifié</h3>
                        <div class="flex items-center gap-4">
                            <UAvatar size="xs" text="JD" status-icon="verified" />
                            <UAvatar size="sm" text="AB" status-icon="verified" />
                            <UAvatar size="md" text="NL" status-icon="verified" />
                            <UAvatar size="lg" text="JD" status-icon="verified" />
                            <UAvatar size="xl" text="AB" status-icon="verified" />
                            <UAvatar size="2xl" text="NL" status-icon="verified" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Table</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <div class="flex flex-col gap-2">
                    <h3 class="font-medium text-primary">Avec données</h3>
                    <UTable selectable :columns="columns" :data="users" @row-click="handleRowClick">
                        <template #cell-name="{ row }">
                            <div class="flex items-center gap-2">
                                <img :src="row.avatar as string" class="w-8 h-8 rounded-full" />
                                <div>
                                    <p class="text-primary font-medium">{{ row.name }}</p>
                                    <p>@{{ (row.name as string).split(' ')[0].toLowerCase() }}</p>
                                </div>
                            </div>
                        </template>
                        <template #cell-role="{ value }">
                            <UBadge class="w-fit" size="sm" color="success">{{ value }}</UBadge>
                        </template>
                    </UTable>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="font-medium text-primary">Sans données</h3>
                    <UTable selectable :columns="columns" :data="[]" @row-click="handleRowClick">
                        <template #cell-name="{ row }">
                            <div class="flex items-center gap-2">
                                <img :src="row.avatar as string" class="w-8 h-8 rounded-full" />
                                <div>
                                    <p class="text-primary font-medium">{{ row.name }}</p>
                                    <p>@{{ (row.name as string).split(' ')[0].toLowerCase() }}</p>
                                </div>
                            </div>
                        </template>
                        <template #cell-role="{ value }">
                            <UBadge class="w-fit" size="sm" color="success">{{ value }}</UBadge>
                        </template>
                    </UTable>
                </div>
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Modal</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UButton @click="isModalOpen = true">Open Modal</UButton>
                <UBaseModal :is-open="isModalOpen" @close="isModalOpen = false">
                    <div class="px-6 pt-6">
                        <UFeaturedIcon :icon="AlertCircleIcon" size="lg" color="success" />
                        <div class="space-y-0.5 mt-4">
                            <h3 class="font-semibold text-primary">Blog post published</h3>
                            <p class="text-tertiary text-sm">
                                This blog post has been published. Team members will be able to edit this post and
                                republish changes.
                            </p>
                        </div>
                    </div>
                    <div class="pt-8">
                        <div class="flex items-center justify-between gap-3 px-6 pb-6">
                            <UButton variant="secondary" class="w-full" @click="isModalOpen = false">Cancel</UButton>
                            <UButton class="w-full" @click="isModalOpen = false">Confirm</UButton>
                        </div>
                    </div>
                </UBaseModal>

                <UButton @click="isSecondModalOpen = true">Open Modal</UButton>
                <UBaseModal background="grid" :is-open="isSecondModalOpen" @close="isSecondModalOpen = false">
                    <div class="px-6 pt-6">
                        <UFeaturedIcon :icon="SaveIcon" size="lg" color="warning" />
                        <div class="space-y-0.5 mt-4">
                            <h3 class="font-semibold text-primary">Unsaved changes</h3>
                            <p class="text-tertiary text-sm">Do you want to save or discard changes?</p>
                        </div>
                    </div>
                    <div class="pt-8">
                        <div class="flex items-center justify-between gap-3 px-6 pb-6">
                            <UButton variant="secondary" class="w-full" @click="isSecondModalOpen = false"
                                >Discard</UButton
                            >
                            <UButton class="w-full" @click="isSecondModalOpen = false">Save changes</UButton>
                        </div>
                    </div>
                </UBaseModal>
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Logo</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <ULogo class="size-12" />
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Calendar</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UCalendar v-model="selectedDate" />
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Date picker</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UDatePicker v-model="selectedDate" label="Date de réservation" placeholder="Sélectionner une date" />
            </div>
        </section>

        <section class="w-full max-w-3xl">
            <h2 class="text-xl font-semibold mb-4 text-primary">Toasts</h2>
            <div class="p-4 bg-gray-100/10 rounded-lg space-y-3">
                <UToastItem :toast="toastInfo" />
                <UToastItem :toast="toastError" />
                <UToastItem :toast="toastSuccess" />
                <UToastItem :toast="toastWarning" />

                <UButton @click="showSuccessToast"> Show Success Toast </UButton>
                <UButton @click="showErrorToast"> Show Error Toast </UButton>
                <UButton @click="showWarningToast"> Show Warning Toast </UButton>
                <UButton @click="showInfoToast"> Show Info Toast </UButton>
            </div>
        </section>
    </main>
</template>
