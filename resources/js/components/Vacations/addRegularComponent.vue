<template>
    <v-row justify="center">
        <v-dialog v-model="dialog">
            <template v-slot:activator="{ props }">
                <v-btn color="green" variant="text" size="x-large" prepend-icon="mdi-plus" v-bind="props"> اضافة
                    رصيد</v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <span class="text-h5"> اضافة رصيد اعتيادي </span>
                </v-card-title>
                <v-card-text>
                    <p class="alert alert-warning text-center text-h6"> تنبه !! <br> سيتم تعديل الرصيد الحالي الي القيمة
                        التي ستكتبها </p>
                    <v-form class="my-3">
                        <v-text-field type="number" label="الرصيد" v-model="addRegular.balance" variant="outlined" :rows="1"
                            auto-grow :rules="[(v) => !!v || ' هذا الحقل مطلوب ']">
                        </v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="dialog = false">
                        اغلاق
                    </v-btn>
                    <v-btn color="blue-darken-1" variant="text" @click="saveRegular">
                        حفظ
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<script setup>
import axios from "axios";
import { usemainStore } from "../../store/mainStore";
const store = usemainStore();
import { ref } from "vue";
const dialog = ref(false);
const addRegular = ref({});

function saveRegular() {
    axios
        .post(`regular`, addRegular.value)
        .then((res) => {
            store.getAbsences();
            addRegular.value = ref({});
            dialog.value = false;
        })
        .catch((e) => {
            // console.log(e);
        });
}

</script>
