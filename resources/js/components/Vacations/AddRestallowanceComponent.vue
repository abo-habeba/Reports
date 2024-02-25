<template>
    <v-row justify="center">
        <v-dialog v-model="dialog">
            <template v-slot:activator="{ props }">
                <v-btn color="success" @click="setField()" v-bind="props"> اضافة بدل راحة </v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <span class="text-h5">اضافة بدل راحة</span>
                </v-card-title>
                <v-card-text>
                    <v-form class="my-3">
                        <v-textarea label="الوصف" v-model="addRequest.description" variant="outlined" :rows="1" auto-grow
                            :rules="[(v) => !!v || ' هذا الحقل مطلوب']">
                        </v-textarea>
                        <v-text-field label="حدد التاريخ" type="date" v-model="addRequest.date"
                            variant="outlined"></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="dialog = false">
                        اغلاق
                    </v-btn>
                    <v-btn color="blue-darken-1" variant="text" @click="saveRequest">
                        حفظ
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialog2" width="auto">
            <v-card>
                <v-card-title>
                    لديك هذه اليوم بالفعل
                </v-card-title>
                <v-card-text>
                    <div v-if="Array.isArray(restEixist)">
                        <h2 v-for="(rest) in restEixist">{{ rest }}</h2>
                    </div>
                    <div v-else>{{ restEixist }}</div>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" variant="text" @click="dialog2 = false">
                        اغلاق
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
const dialog2 = ref(false);
const addRequest = ref({});
const restEixist = ref({});
function saveRequest() {
    if (!addRequest.value.description) {
        addRequest.value.description = ' بدل راحه '
    }
    axios
        .post(`restallowance`, addRequest.value)
        .then(() => {
            // console.log(res.data)
            store.getAbsences();
            addRequest.value = ref({});
            dialog.value = false;
        })
        .catch((e) => {
            // console.log(e);
            if (Array.isArray(e.response.data)) {
                restEixist.value = e.response.data;
            } else {
                restEixist.value = ' حدث خطا اعد المحاولة';
            }
            dialog2.value = true;
        });
}
function setField() {
    addRequest.value.date = store.formatDate(new Date());
}
const typeRequest = ref([
    "Regular",
    "Rest allowance",
    "Casual",
    "Umrah leave",
    "Recruitment",
    "Maternity leave",
    "Compensatory leave",
    "Other",
]);
// const typeRequest = ref({
//     "Regular": "Regular",
//     "Rest allowance": "Rest allowance",
//     "Casual": "Casual",
//     "Umrah leave": "Umrah leave",
//     "Recruitment": "Recruitment",
//     "Maternity leave": "Maternity leave",
//     "Compensatory leave": "Compensatory leave",
//     "Other": "Other",
// });

// اعتيادية	Regular
//  بدل راحة	Rest allowance
//  عارضة	Casual
//  اجازة عمرة	Umrah leave
//  تجنيد	Recruitment
//  اجازة وضع	Maternity leave
//  اجازة تعويضية	Compensatory leave
//  اخري	Other
// const location = ref(null);

</script>
