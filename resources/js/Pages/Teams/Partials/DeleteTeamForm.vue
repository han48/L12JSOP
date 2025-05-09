<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    team: Object,
});

const confirmingTeamDeletion = ref(false);
const form = useForm({});

const confirmTeamDeletion = () => {
    confirmingTeamDeletion.value = true;
};

const deleteTeam = () => {
    form.delete(route('teams.destroy', props.team), {
        errorBag: 'deleteTeam',
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            {{ trans('delete_team') }}
        </template>

        <template #description>
            {{ trans('permanently_delete_this_team') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                {{ trans('once_a_team_is_deleted_all_of_its_resources_and_data_will_be_permanently_deleted_before_deleting_this_team_please_download_any_data_or_information_regarding_this_team_that_you_wish_to_retain') }}
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmTeamDeletion">
                    {{ trans('delete_team') }}
                </DangerButton>
            </div>

            <!-- Delete Team Confirmation Modal -->
            <ConfirmationModal :show="confirmingTeamDeletion" @close="confirmingTeamDeletion = false">
                <template #title>
                    {{ trans('delete_team') }}
                </template>

                <template #content>
                    {{ trans('are_you_sure_you_want_to_delete_this_team_once_a_team_is_deleted_all_of_its_resources_and_data_will_be_permanently_deleted') }}

                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingTeamDeletion = false">
                        {{ trans('cancel') }}
                    </SecondaryButton>

                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteTeam">
                        {{ trans('delete_team') }}
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>
    </ActionSection>
</template>
