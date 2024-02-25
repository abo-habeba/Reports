<template>
    <!-- <v-btn @click="store.getAbsences()">getAbsences</v-btn> -->
    <div>
        <AddVRestallowanceComponent />
        <div class="div-balance text-center">
            <router-link to="/vacations">
                <div class="box-balance">
                    <div class="item-balance m-1"> الاجازات </div>
                </div>
            </router-link>
            <div class="box-balance">
                <router-link to="/rest">
                    <div class="item-balance"> البدلات </div>
                    <div class="item-balance">{{ store.rest }}</div>
                </router-link>
            </div>

        </div>
        <hr style="margin: auto" />
        <router-view></router-view>
    </div>
    <div style="white-space: nowrap; overflow: auto; width: 100%">
        <table v-if="store.restallowance" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th> الوصف </th>
                    <th> التاريخ </th>
                    <th> الحالة </th>
                    <th> تاريخ الانشاء </th>
                    <th> اجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr class="box-absence" v-for="(absence, index) in store.restallowance" @click="ActiveClass(absence)"
                    @contextmenu="optionsMenu(absence)" :id="'id-' + absence.id">
                    <th>{{ index + 1 }}</th>
                    <td>{{ absence.description }}</td>
                    <td>{{ absence.date }}</td>
                    <td :class="{
                        'bg-suc': absence.state == 1,
                        'bg-warn': absence.state == 5,
                        'bg-dang': absence.state == 0,
                    }">
                        <span v-if="absence.state == 1"> متاح </span>
                        <span v-if="absence.state == 0"> تم التبديل </span>
                        <span v-if="absence.state == 5"> النصف فقط </span>
                    </td>
                    <td>{{ store.formatDate(absence.created_at) }}</td>
                    <td><i @click="optionsMenuDots($event, absence)" id="dots-active"
                            class="mdi mdi-dots-vertical dots-active"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else class="text-center m-5">{{ $t("noData") }}</div>
        <div id="optionsMenu" class="d-f-c">
            <v-list>
                <v-list-item @click="dialog2 = true" prepend-icon="mdi-square-edit-outline">تعديل</v-list-item>
                <v-list-item @click="dialog = !dialog" prepend-icon="mdi-delete">حذف</v-list-item>
            </v-list>
        </div>
        <v-dialog v-model="dialog" width="auto">
            <v-card>
                <v-card-title>
                    تاكيد حذف
                </v-card-title>
                <v-card-text v-if="absenceState == 0">
                    تم تبديل {{ absenceDescription }} لابد من حذفها من الاجازات اولا
                </v-card-text>
                <v-card-text v-else>
                    هل تريد حذف {{ absenceDescription }}
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" variant="text" @click="dialog = false">
                        {{ absenceState == 0 ? 'اغلاق' : 'لا' }}
                    </v-btn>
                    <v-btn v-if="absenceState == 1" color="primary" variant="text" @click="funDelete()">
                        نعم
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialog2" width="auto">
            <v-card>
                <v-card-title>
                    تنبية
                </v-card-title>
                <v-card-text>
                    خاصية التعديل غير متاحه الان <br> بامكانك حذف الاجازة واضافة اجازة اخري
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" variant="text" @click="dialog2 = false">
                        اغلاق
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script setup>
import { usemainStore } from "../../store/mainStore";
import AddVRestallowanceComponent from "../../components/Vacations/AddRestallowanceComponent.vue";
import { onMounted, ref } from "vue";
import axios from "axios";
const store = usemainStore();
const dialog = ref(false)
const dialog2 = ref(false)
const absenceId = ref(null);
const absenceDescription = ref(null);
const absenceState = ref(null);
onMounted(() => {
    store.getAbsences();
});
function optionsMenuDots(event, absence) {
    const optionsMenuEl = document.getElementById('optionsMenu');
    const styleEl = {
        display: 'block',
        top: `${event.pageY + 10}px`,
        left: `${event.pageX >= 150 ? event.pageX - 100 : event.pageX + 30}px`,
    };
    absenceId.value = absence.id;
    absenceDescription.value = absence.description;
    absenceState.value = absence.state;
    Object.assign(optionsMenuEl.style, styleEl);

    // تعريف الدالة المراقبة
    function funAddEvent(event) {
        var optionsMenu = document.getElementById('optionsMenu');
        var dotsActive = document.getElementById('dots-active');
        var target = event.target;

        if (target.id != dotsActive.id && optionsMenu.style.display == 'block') {
            optionsMenu.style.display = 'none';
            // document.removeEventListener('click', funAddEvent);
        }
    }
    // إضافة المراقبة
    if (optionsMenuEl.style.display == 'block') {
        document.addEventListener('click', funAddEvent);
    }
}

function optionsMenu(absence) {
    event.preventDefault();
    const optionsMenuEl = ref(document.getElementById('optionsMenu'));
    const styleEl = ref({
        display: 'block',
        top: `${event.pageY + 10}px`,
        left: `${event.pageX >= 150 ? event.pageX - 100 : event.pageX + 30}px`,
    });
    absenceId.value = absence.id;
    absenceDescription.value = absence.description;
    absenceState.value = absence.state;
    Object.assign(optionsMenuEl.value.style, styleEl.value);

    // تعريف الدالة المراقبة
    function funAddEvent(event) {
        var optionsMenu = document.getElementById('optionsMenu');
        var target = event.target;

        if (!optionsMenu.contains(target) && optionsMenu.style.display === 'block') {
            optionsMenu.style.display = 'none';
            // document.removeEventListener('click', funAddEvent);
        }
    }
    // إضافة المراقبة
    if (optionsMenuEl.value.style.display == 'block') {
        document.addEventListener('click', funAddEvent);
    }

    ActiveClass(absence);
}
function ActiveClass(absence) {

    absenceId.value = absence.id;
    absenceDescription.value = absence.description;

    // إزالة كلاس active من باقي العناصر
    Array.from(document.querySelectorAll(`.box-absence`)).forEach(element => {
        // if (element != document.getElementById(`id-${absence.id}`)) {
        element.classList.remove('active');
        //}
    });
    // إضافة كلاس active إلى العنصر الهدف
    document.getElementById(`id-${absence.id}`).classList.add('active');

}
function funDelete() {
    axios.delete(`restallowance/${absenceId.value}`).then((resp) => {
        store.getAbsences();
        dialog.value = false;
        store.startSnack("success", "no", "success");
    }).catch((e) => {
        // store.startSnack("error", "no", "danger");
    })
    document.getElementById('optionsMenu').style.display = 'none';
}
</script>
<style lang="scss">
.box-fixed {
    position: sticky !important;
    top: 73px;
    z-index: 9999;
    width: 100%;
    height: 100%;
}

.box-absence {
    .bg-suc {
        background-color: darkgreen !important;
    }

    .bg-dang {
        background-color: #dc3545 !important;
    }

    .bg-warn {
        background-color: #5d5e55 !important;
    }
}

.box-fixed * {
    position: unset !important;
}

html {
    overflow-y: auto !important;
}
</style>
