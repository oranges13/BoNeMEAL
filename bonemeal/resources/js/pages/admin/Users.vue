<script setup lang="ts">
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/admin/users';

type Row = {
    id: number;
    name: string;
    email: string;
    role: string;
    active: boolean;
    must_set_password: boolean;
    minecraft_uuid: string | null;
};

const props = defineProps<{
    users: Row[];
    roles: string[];
    inviteUrl?: string | null;
}>();

defineOptions({
    layout: { breadcrumbs: [{ title: 'Users', href: index() }] },
});

const errors = computed(() => usePage().props.errors as Record<string, string>);

function save(user: Row, changes: Partial<Row>) {
    const next = { ...user, ...changes };
    router.patch(
        UserController.update.url(user.id),
        {
            name: next.name,
            role: next.role,
            active: next.active,
            minecraft_uuid: next.minecraft_uuid ?? '',
        },
        { preserveScroll: true },
    );
}

function invite(user: Row) {
    router.post(UserController.invite.url(user.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-8 p-4">
        <Heading title="Users" description="Create staff accounts and manage their roles." />

        <div
            v-if="props.inviteUrl"
            class="rounded-md border border-green-600/40 bg-green-600/10 p-4 text-sm"
            data-test="invite-url"
        >
            <p class="font-medium">Set-password link (share it privately; shown once):</p>
            <code class="mt-1 block break-all">{{ props.inviteUrl }}</code>
        </div>

        <Form
            v-bind="UserController.store.form()"
            class="grid gap-4 sm:grid-cols-4"
            v-slot="{ errors: formErrors, processing }"
            reset-on-success
        >
            <div class="grid gap-2">
                <Label for="new-name">Name</Label>
                <Input id="new-name" name="name" required />
                <InputError :message="formErrors.name" />
            </div>
            <div class="grid gap-2">
                <Label for="new-email">Email</Label>
                <Input id="new-email" type="email" name="email" required />
                <InputError :message="formErrors.email" />
            </div>
            <div class="grid gap-2">
                <Label for="new-role">Role</Label>
                <select id="new-role" name="role" class="h-9 rounded-md border bg-transparent px-2 text-sm">
                    <option v-for="role in props.roles" :key="role" :value="role">{{ role }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <Button :disabled="processing" data-test="create-user-button">Create user</Button>
            </div>
        </Form>

        <InputError :message="errors.role" />

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-muted-foreground">
                    <tr>
                        <th class="p-2">Name</th>
                        <th class="p-2">Email</th>
                        <th class="p-2">Role</th>
                        <th class="p-2">Minecraft UUID</th>
                        <th class="p-2">Status</th>
                        <th class="p-2" />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in props.users" :key="user.id" class="border-t">
                        <td class="p-2">{{ user.name }}</td>
                        <td class="p-2">{{ user.email }}</td>
                        <td class="p-2">
                            <select
                                :value="user.role"
                                class="h-8 rounded-md border bg-transparent px-2"
                                @change="save(user, { role: ($event.target as HTMLSelectElement).value })"
                            >
                                <option v-for="role in props.roles" :key="role" :value="role">{{ role }}</option>
                            </select>
                        </td>
                        <td class="p-2">
                            <input
                                :value="user.minecraft_uuid ?? ''"
                                class="h-8 w-72 rounded-md border bg-transparent px-2 font-mono text-xs"
                                placeholder="not linked"
                                @change="save(user, { minecraft_uuid: ($event.target as HTMLInputElement).value })"
                            />
                        </td>
                        <td class="p-2">
                            <span v-if="!user.active">Deactivated</span>
                            <span v-else-if="user.must_set_password">Awaiting password</span>
                            <span v-else>Active</span>
                        </td>
                        <td class="flex gap-2 p-2">
                            <Button variant="outline" size="sm" @click="save(user, { active: !user.active })">
                                {{ user.active ? 'Deactivate' : 'Activate' }}
                            </Button>
                            <Button variant="outline" size="sm" @click="invite(user)">Set-password link</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
