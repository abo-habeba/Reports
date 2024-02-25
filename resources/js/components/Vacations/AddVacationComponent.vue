<template>
    <v-row justify="center">
        <v-dialog v-model="dialog">
            <template v-slot:activator="{ props }">
                <v-btn color="success" @click="setField()" v-bind="props"> طلب اجازة جديد </v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <span class="text-h5"> طلب اجازة جديد </span>
                </v-card-title>
                <v-card-text>
                    <div v-if="addRequest.Type != 'Rest allowance'" class="alert alert-danger" role="alert">
                        تنبية ! <br> عدم تحديد فتره تحتوي علي عطلات مثل الجمعة والسبت
                    </div>

                    <v-form class="my-3">
                        <v-select label="نوع الاجازة" :items="typeRequest" item-title="ar" item-value="en"
                            v-model="addRequest.Type" variant="outlined" :rules="[(v) => !!v || 'هذا الحقل مطلوب ']">
                        </v-select>
                        <div v-if="addRequest.Type != 'Rest allowance'">
                            <v-textarea label="الوصف" v-model="addRequest.description" variant="outlined" :rows="1"
                                auto-grow :rules="[(v) => !!v || 'هذا الحقل مطلوب ']">
                            </v-textarea>
                        </div>
                        <div v-if="addRequest.Type == 'Rest allowance'">
                            <v-select label="البدلات" :items="activeRest" item-title="description" item-value="id"
                                v-model="addRequest.rest_id" variant="outlined" :rules="[(v) => !!v || 'هذا الحقل مطلوب ']">
                            </v-select>
                        </div>
                        <v-text-field :label="addRequest.Type == 'Rest allowance' ? ' تاريخ الاجازة' : '  بداية الاجازه '"
                            type="date" v-model="addRequest.start_date" variant="outlined"></v-text-field>
                        <div v-if="addRequest.Type != 'Rest allowance'">
                            <v-text-field label="نهاية الاجازة" type="date" v-model="addRequest.end_date"
                                variant="outlined"></v-text-field>
                        </div>
                        <v-checkbox v-model="dailyFife" label=" نص يوم " color="red"></v-checkbox>
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
    </v-row>
    <v-dialog v-model="dialog2" width="auto">
        <v-card>
            <v-card-title v-if="Array.isArray(absenceEixist)">
                لديك هذه الايام بالفعل
            </v-card-title>
            <v-card-text>
                <!-- <h2>{{ absenceEixist }}</h2> -->
                <div v-if="Array.isArray(absenceEixist)">
                    <h2 v-for="(absence) in absenceEixist">{{ absence }}</h2>
                </div>
                <div v-else>{{ absenceEixist }}</div>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog2 = false">
                    اغلاق
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- <v-dialog v-model="dialog5" width="100%">
        <v-card>
            <v-card-title>
                print Log
            </v-card-title>
            <v-card-text>
                {{ store.printLog }}
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog5 = false">
                    اغلاق
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog> -->
</template>
<script setup>
import axios from "axios";
import { usemainStore } from "../../store/mainStore";
const store = usemainStore();
import { onMounted, ref } from "vue";
const dialog = ref(false);
const activeRest = ref([]);
const dialog2 = ref(false);
// const dialog5 = ref(false);
const absenceEixist = ref('');
const dailyFife = ref(false);
const addRequest = ref({});
const typeRequest = ref([
    { "en": "Regular", "ar": "اعتيادية" },
    { "en": "Rest allowance", "ar": " بدل راحة " },
    { "en": "Casual", "ar": "عارضة" },
    { "en": "Umrah leave", "ar": "إجازة عمرة" },
    { "en": "Recruitment", "ar": "تجنيد" },
    { "en": "Maternity leave", "ar": "إجازة وضع" },
    { "en": "Compensatory leave", "ar": "إجازة تعويضية" },
    { "en": "Other", "ar": "أخرى" }
]
);
onMounted(() => {

    store.getAbsences().then(() => {
        const restallowance = ref(store.restallowance);
        if (restallowance.value) {
            const filteractiveRest = ref(restallowance.value.filter(obj => obj.state == 1 || obj.state == 5));
            activeRest.value = filteractiveRest.value;
        }
    });
});

function saveRequest() {
    addRequest.value.dailyFife = dailyFife.value;
    if (addRequest.value.Type == 'Rest allowance') {
        addRequest.value.end_date = addRequest.value.start_date;
    }
    if (!addRequest.value.description) {
        if (addRequest.value.Type == 'Rest allowance') {
            // البحث عن الكائن الذي يحمل الـ ID المحدد
            let targetObject = store.restallowance.find(obj => obj.id === addRequest.value.rest_id);
            // الوصول إلى وصف الكائن
            addRequest.value.description = targetObject.description;
        } else {
            addRequest.value.description = 'بدون وصف';
        }
    }
    if (!addRequest.value.Type) {
        addRequest.value.Type = typeRequest.value[0].en;
    }
    axios
        .post(`absence`, addRequest.value)
        .then((re) => {
            store.getAbsences();
            addRequest.value = ref({});
            dialog.value = false;
            // store.printLog = re.data;
            // dialog5.value = true;
        })
        .catch((er) => {
            // store.printLog = er.response;
            // dialog5.value = true;
            if (Array.isArray(er.response.data)) {
                absenceEixist.value = e.response.data;
            } else {
                absenceEixist.value = ' حدث خطا اعد المحاولة';
            }
            dialog2.value = true;
        });
}
function setField() {
    addRequest.value.start_date = store.formatDate(new Date());
    addRequest.value.end_date = store.formatDate(new Date());
}

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
<style lang="scss">
.v-messages__message {
    padding: 13px;
    padding-top: 3px;
    color: red;
}

.non-clickable {
    pointer-events: none !important;
    cursor: not-allowed !important;
}
</style>
