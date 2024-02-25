<template>
    <v-btn color="primary" size="60px" variant="text" @click="dialog3 = true" prepend-icon="mdi-cog"></v-btn>
    <v-dialog v-model="dialog3" width="80%">
        <v-card>
            <v-card-title>
                الاعدادات
            </v-card-title>
            <v-card-text @click="dialog3 = false">
                <addRegularComponent />
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog3 = false">
                    اغلاق
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <div>
        <AddVacationComponent />
        <v-row class="div-balance text-center">
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
                        <div class="item-balance"> بدل راحة + </div>
                        <div class="item-balance">{{ store.rest }}</div>
                    </div>
                </router-link>
            </v-col>
        </v-row>

        <hr style="margin: auto" />
        <!-- <router-view></router-view> -->
    </div>
    <div style="white-space: nowrap; overflow: auto; width: 100%">
        <div v-if="store.absences.length == ''" class="text-center  m-5">{{ $t("noData") }}</div>
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th> نوع الاجازة </th>
                    <th>تاريخ الاجازة</th>
                    <th>الوصف</th>
                    <th>تاريخ الانشاء</th>
                    <th> اجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr class="box-absence" v-for="(absence, index) in store.absences" @click="ActiveClass(absence)"
                    @contextmenu="optionsMenu(absence)" :id="'id-' + absence.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ $t(absence.Type) }}</td>
                    <td>{{ absence.date }}</td>
                    <td>{{ absence.description }}</td>
                    <td>{{ store.formatDate(absence.created_at) }}</td>
                    <td><i @click="optionsMenuDots($event, absence)" id="dots-active"
                            class="mdi mdi-dots-vertical dots-active"></i>
                    </td>
                </tr>
            </tbody>
        </table>
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
                <v-card-text>
                    هل تريد حذف {{ absenceDescription }}
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" variant="text" @click="dialog = false">
                        لا
                    </v-btn>
                    <v-btn color="primary" variant="text" @click="funDelete()">
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
import addRegularComponent from "../../components/Vacations/addRegularComponent.vue";
import { usemainStore } from "../../store/mainStore";
import AddVacationComponent from "../../components/Vacations/AddVacationComponent.vue";
import { onMounted, ref } from "vue";
import axios from "axios";
const store = usemainStore();
const absenceId = ref(0);
const absenceDescription = ref(0);
const dialog = ref(false)
const dialog2 = ref(false)
const dialog3 = ref(false)
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

    // إضافة كلاس active إلى العنصر الهدف
    document.getElementById(`id-${absence.id}`).classList.add('active');
    // إزالة كلاس active من باقي العناصر
    Array.from(document.querySelectorAll(`.box-absence`)).forEach(element => {
        if (element !== document.getElementById(`id-${absence.id}`)) {
            element.classList.remove('active');
        }
    });

}
function funDelete() {
    axios.delete(`absence/${absenceId.value}`).then(() => {
        store.getAbsences();
        dialog.value = false;
        store.startSnack("success", "no", "success");
    }).catch(() => {
        store.startSnack("error", "no", "danger");;
    })
    document.getElementById('optionsMenu').style.display = 'none';
}
// function funEdit() {
//     document.getElementById('optionsMenu').style.display = 'none';
// }
</script>
<style lang="scss">
.active {
    td {
        background-color: #0d6dfd52 !important;
        color: #4fce06 !important;
    }
}

.box-fixed {
    position: sticky !important;
    top: 73px;
    z-index: 9999;
    width: 100%;
    height: 100%;
}

.box-fixed * {
    position: unset !important;
}

td {
    user-select: none;
}

.div-balance {
    padding: 10px 3px;
    margin-top: 10px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    letter-spacing: 2px;

    a {
        text-decoration: none;
        color: white;
    }
}

.box-balance {
    border-radius: 8px;
    background-color: #0d6efd;
    color: white;

    // padding: .5% 2%;
    // .mdi-plus {
    //     font-size: 18px;
    // }
}

.box-absence {
    td {
        font-weight: bold;
        font-size: 20px;

        .dots-active {
            text-align: center;
            display: block;
            font-weight: bold !important;
            font-size: 20px;
            background-color: transparent;
        }
    }
}

#optionsMenu {
    position: fixed;
    width: fit-content;
    border-radius: 6px;
    text-align: center;
    z-index: 9999;
    top: 0px;
    border: 1px solid gray;
    overflow: hidden;
    display: none;

    .v-list-item {
        border-bottom: 1px solid gray;

        &:last-child {
            width: 100%;
            border-bottom: none;
        }

        * {
            padding: 0px;
            margin: 0px;
            padding-inline-start: 6px;
            padding-inline-end: 6px;
        }
    }
}

html {
    overflow-y: auto !important;
}
</style>
