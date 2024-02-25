<template>
    <v-row class="div-balance text-center">
        <v-col cols="12" sm="4">
            <router-link to="/reports">
                <div class=" box-balance">
                    <div class="item-balance"> التقارير </div>
                    <div class="item-balance">
                        {{ store.reports == 'getData' || store.reports == 'noData' ? 0 : store.reports.length }}
                    </div>
                </div>
            </router-link>
        </v-col>
        <v-col cols="12" sm="4">
            <router-link to="/vacations">
                <div class=" box-balance">
                    <div class="item-balance"> الاعتيادية </div>
                    <div class="item-balance">{{ store.regular }}</div>
                </div>
            </router-link>
        </v-col>
        <v-col cols="12" sm="4">
            <router-link to="/rest">
                <div class=" box-balance">
                    <div class="item-balance"> بدل راحة </div>
                    <div class="item-balance">{{ store.rest }}</div>
                </div>
            </router-link>
        </v-col>
    </v-row>
</template>
<script setup>
import { usemainStore } from "@/store/mainStore";
const store = usemainStore();
import { onMounted, ref } from "vue";
import axios from "axios";
const dialog = ref();
onMounted(() => {
    store.getAbsences();
    store.getUser();
    store.getReports();
});
</script>
<style></style>
