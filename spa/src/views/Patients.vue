<template>
    <div class="card">
        <DataTable :value="store.items" class="[&_td]:cursor-pointer" tableStyle="min-width: 50rem" removable-sort
            row-hover @row-click="showPatientDetails">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('patients') }}
                    </span>
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" @click="popover.show" />
                    <Button icon="pi pi-plus" :label="$t('new-patient')" severity="success" @click="create()" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <template #footer>
                <Paginator v-if="store.pagiantor.totalRecords" v-bind="store.pagiantor" @page="store.paginate" />
            </template>
            <Column field="id" :header="$t('id')" />
            <Column field="firstname" :header="$t('firstname')" />
            <Column field="lastname" :header="$t('lastname')" />
            <Column :field="({ mobiles }) => mobiles.map(({ number }) => number).join(' | ')" :header="$t('mobile')"
                body-class="ltr !text-left" />
            <Column field="birthday" :header="$t('birthday')" body-class="ltr !text-left" />
            <Column :field="({ gender }) => $t(gender)" :header="$t('gender')" />
            <Column field="province.title" :header="$t('province')" />
            <Column field="city.title" :header="$t('city')" />
            <Column field="lead_source.title" :header="$t('lead_source')" />
            <Column field="status" :header="$t('status')">
                <template #body="{ data: { status } }">
                    <Tag v-bind="status" />
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
            <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />

            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-pencil" rounded text severity="secondary" @click="edit(data)" />
                        <Button icon="pi pi-trash" rounded text severity="danger" :loading="data.loading"
                            @click="destroy(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { usePatientsStore } from '@/stores/patients';
import { defineAsyncComponent, inject, watch } from 'vue';

const { dialog, confirm, popover, toast, router, t } = inject('service')

popover.value.component = defineAsyncComponent(() => import('@/components/PatientFilters.vue'));

const store = usePatientsStore()

if (store.items.length === 0)
    store.index()

const PatientForm = defineAsyncComponent(() => import('@/components/PatientForm.vue'));

const create = async () => {
    dialog.open(PatientForm, {
        props: {
            header: t('createNewPatient'), modal: true
        },
    })
}

const edit = async (data) => {
    const patient = Object.assign({}, data)
    patient.mobiles = patient.mobiles.map(({ number }) => number)
    patient.province = patient.province.id
    patient.city = patient.city.id
    patient.status = patient.status.id
    patient.lead_source = patient.lead_source.id

    dialog.open(PatientForm, {
        props: { header: t('editPatient'), modal: true },
        data: { patient }
    })
}

const destroy = (patient) => {
    confirm.require({
        message: t('delete-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('delete'),
            icon: 'pi pi-trash',
            severity: 'danger',
        },
        accept: async () => {
            patient.loading = true

            const { statusText, data } = await store.destroy(patient.id);

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                patient.loading = false
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}

const showPatientDetails = ({ data: { id } }) => router.push({ name: 'PatientDetails', params: { id } })

let timer;
watch(() => store.filters.query, (v) => {
    if (v != undefined) {
        clearTimeout(timer)
        timer = setTimeout(() => {
            if (v) store.filters = { query: v }
            else delete store.filters.query
            store.index()
        }, 300);
    }
})
</script>

<style lang="scss"></style>