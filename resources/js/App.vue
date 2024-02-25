<template>
    <v-app>
        <v-layout class="rounded rounded-md">
            <SnackbarComponent />
            <NavbarComponent v-if="store.auth" />
            <NavigationComponent v-if="store.auth" />
            <v-main>
                <v-container>
                    <router-view />
                </v-container>
            </v-main>
        </v-layout>
    </v-app>
</template>
<script setup>
import axios from "axios";
import { useRouter } from "vue-router";
const router = useRouter();
import { onMounted } from "vue";
import NavbarComponent from "./components/NavbarComponent.vue";
import NavigationComponent from "./components/NavigationComponent.vue";
import SnackbarComponent from "./components/SnackbarComponent.vue";
// import FooterComponent from "./components/FooterComponent.vue";
import { usemainStore } from "./store/mainStore";
const store = usemainStore();
console.log(window.location.origin);
const BASE_URL =
    window.location.origin === "http://localhost:8080" || window.location.origin === "http://127.0.0.1:8000"
        ? "http://127.0.0.1:8000/api/"
        : "https://api.hwnix.com/api/";
console.log(BASE_URL);
axios.defaults.baseURL = BASE_URL;
axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.token}`;
onMounted(() => {
    axios.defaults.baseURL = BASE_URL;
    axios.defaults.withCredentials = true;
    store.setAuthHeaderNew(localStorage.token);
    // router.push({ path: "vacations" });
    store.getUser();
});
function eventOffset() {
    store.getUser();
    store.getReports();
}
window.addEventListener("beforeunload", function (event) {
    if (event.pageYOffset === 0) {
        event.preventDefault();
        event.returnValue = "";
        eventOffset();
    }
});
</script>
<style>
body * {
    font-family: "Noto Kufi Arabic", sans-serif !important;
    font-optical-sizing: auto;
    /* font-weight: 700;
    font-style: normal; */
}

.clickd {
    cursor: pointer !important;
}

.popup {
    display: flex;
    background-color: rgba(122, 168, 30, 0.103);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
}
</style>
