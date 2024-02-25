<template>
    <div class="register">
        <h1 class="h1 my-5 text-center">{{ $t("editProfil") }}</h1>
        <v-form lazy-validation>
            <v-text-field variant="outlined" :label="$t('enterName')" v-model="user.name"
                :rules="[(v) => !!v || 'This field is required']"></v-text-field>
            <v-text-field variant="outlined" :label="$t('enterEmail')" :rules="[(v) => !!v || 'This field is required']"
                v-model="user.email"></v-text-field>
            <v-text-field variant="outlined" type="number" :label="$t('enterTelephone')"
                :rules="[(v) => !!v || 'This field is required']" v-model="user.phone"></v-text-field>
            <v-btn class="my-3 btn-password"
                @click="editPass = !editPass">{{ !editPass ? $t("changePassword") : $t("noChangePassword") }}</v-btn>
            <v-text-field v-if="editPass" :type="store.passToggle == true ? 'password' : 'text'" :append-inner-icon="store.passToggle ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
                " @click:appendInner="store.passToggle = !store.passToggle" variant="outlined" autocomplete="ON"
                v-model="user.password" :label="$t('enterPassword')"
                :rules="[(v) => !!v || 'This field is required']"></v-text-field>
        </v-form>
        <v-btn color="secondary" class="my-3" @click="editProfil">{{ $t("save") }}</v-btn>
    </div>
    <v-btn color="red" class="my-3" @click="dialog = !dialog" prepend-icon="mdi-delete"> حذف الحساب نهائيا </v-btn>
    <v-dialog v-model="dialog" width="auto">
        <v-card>
            <v-card-text>
                هل تريد حذف الحساب بالفعل
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog = false">
                    لا
                </v-btn>
                <v-btn color="primary" variant="text" @click="dialog2 = !dialog2">
                    نعم
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialog2" width="auto">
        <v-card>
            <v-card-title>
                تاكيد حذف
            </v-card-title>
            <v-card-text color="red">
                <div class="alert alert-danger text-center text-h6"> سيتم حذف جميع البيانات المسجلة نهائيا ولا
                    تستطيع
                    استرجاعها </div>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog2 = false">
                    لا
                </v-btn>
                <v-btn color="primary" variant="text" @click="funDelete()">
                    نعم
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { usemainStore } from "@/store/mainStore";
const store = usemainStore();
import axios from "axios";
import { ref } from "vue";
const dialog = ref(false);
const dialog2 = ref(false);
const user = ref(store.user);
const editPass = ref(false);
function editProfil() {
    axios
        .put(`users/${store.user.id}`, user.value)
        .then(() => {
            store.startSnack("success", "no", "success");
            if (user.value.password) {
                store.logout();
            }
        })
        .catch(() => {
            store.startSnack("error", "no", "danger");
        });
}
function funDelete() {
    axios.delete(`users/${store.user.id}`).then(() => {
        dialog.value = false;
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        store.setAuthHeaderNew();
        store.startSnack("success", "login", "success", false, 200);
        store.auth = false;
    }).catch(() => {
        store.startSnack("error", "no", "danger");
    })
}
</script>
<style>
.btn-password {
    color: white !important;
    background-color: #ff5722 !important;
    /* position: sticky !important;
    right: 20%;
    bottom: 50%; */
}
</style>
