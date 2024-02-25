<template>
    <div>
        <AddUserComponent />
    </div>
    <div style="white-space: nowrap; overflow: auto; width: 100%">
        <div v-if="store.users.length == ''" class="text-center  m-5">{{ $t("noData") }}</div>
        <table v-else class="table table-striped text-end">
            <thead>
                <tr>
                    <th>#</th>
                    <th> الاسم </th>
                    <th> الايميل </th>
                    <th> رقم الهاتف </th>
                    <th> الوظيفة </th>
                    <th> الصلاحيات </th>
                    <th>تاريخ الانشاء</th>
                    <th> اجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr class="box-user" v-for="(user, index) in store.users" @click="ActiveClass(user)"
                    @contextmenu="optionsMenu(user)" :id="'id-' + user.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.phone }}</td>
                    <td>{{ user.Job_title }}</td>
                    <td>{{ user.roles }}</td>
                    <td>{{ store.formatDate(user.created_at) }}</td>
                    <td><i @click="optionsMenuDots($event, user)" id="dots-active"
                            class="mdi mdi-dots-vertical dots-active"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="optionsMenu" class="d-f-c">
            <v-list>
                <v-list-item @click="dialog3 = true" prepend-icon="mdi-square-edit-outline">تعديل</v-list-item>
                <v-list-item @click="dialog = !dialog" prepend-icon="mdi-delete">حذف</v-list-item>
            </v-list>
        </div>

        <v-dialog v-model="dialog" width="auto">
            <v-card>
                <v-card-title>
                    تاكيد حذف
                </v-card-title>
                <v-card-text>
                    هل تريد حذف {{ userDescription }}
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
        <v-dialog v-model="dialog3" width="auto">
            <v-card>
                <v-card-title>
                    تعديل {{ userActive.name }}
                </v-card-title>
                <v-card-text>
                    <v-form lazy-validation>
                        <v-text-field variant="outlined" :label="$t('enterName')" v-model="userActive.name"
                            :rules="[(v) => !!v || 'This field is required']"></v-text-field>
                        <v-text-field variant="outlined" :label="$t('enterEmail')"
                            :rules="[(v) => !!v || 'This field is required']" v-model="userActive.email"></v-text-field>
                        <v-text-field variant="outlined" type="number" :label="$t('enterTelephone')"
                            :rules="[(v) => !!v || 'This field is required']" v-model="userActive.phone"></v-text-field>
                        <v-btn class="my-3 btn-password"
                            @click="editPass = !editPass">{{ !editPass ? $t("changePassword") : $t("noChangePassword") }}</v-btn>
                        <v-text-field v-if="editPass" :type="store.passToggle == true ? 'password' : 'text'"
                            :append-inner-icon="store.passToggle ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
                                " @click:appendInner="store.passToggle = !store.passToggle" variant="outlined"
                            autocomplete="ON" v-model="userActive.password" :label="$t('enterPassword')"
                            :rules="[(v) => !!v || 'This field is required']"></v-text-field>
                    </v-form>
                    <v-btn color="secondary" class="my-3" @click="editProfil">{{ $t("save") }}</v-btn>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" variant="text" @click="dialog3 = false">
                        اغلاق
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>
</template>
<script setup>
import AddUserComponent from "../../components/user/AddUserComponent.vue";
import { usemainStore } from "../../store/mainStore";
import { onMounted, ref } from "vue";
import axios from "axios";
const store = usemainStore();
const userActive = ref(null);
const editPass = ref(false);
const userId = ref(0);
const dialog = ref(false)
const dialog2 = ref(false)
const dialog3 = ref(false)
onMounted(() => {
    store.getUsers();
});

function optionsMenuDots(event, user) {
    userActive.value = user;
    const optionsMenuEl = document.getElementById('optionsMenu');
    const styleEl = {
        display: 'block',
        top: `${event.pageY + 10}px`,
        left: `${event.pageX >= 150 ? event.pageX - 100 : event.pageX + 30}px`,
    };
    userId.value = user.id;
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

function optionsMenu(user) {
    event.preventDefault();
    userActive.value = user;
    const optionsMenuEl = ref(document.getElementById('optionsMenu'));
    const styleEl = ref({
        display: 'block',
        top: `${event.pageY + 10}px`,
        left: `${event.pageX >= 150 ? event.pageX - 100 : event.pageX + 30}px`,
    });
    userId.value = user.id;
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

    ActiveClass(user);
}
function ActiveClass(user) {
    userActive.value = user;
    userId.value = user.id;

    // إضافة كلاس active إلى العنصر الهدف
    document.getElementById(`id-${user.id}`).classList.add('active');
    // إزالة كلاس active من باقي العناصر
    Array.from(document.querySelectorAll(`.box-user`)).forEach(element => {
        if (element !== document.getElementById(`id-${user.id}`)) {
            element.classList.remove('active');
        }
    });

}
function funDelete() {
    axios.delete(`users/${userId.value}`).then(() => {
        store.getUsers();
        dialog.value = false;
        store.startSnack("success", "no", "success");
    }).catch(() => {
        store.startSnack("error", "no", "danger");;
    })
    document.getElementById('optionsMenu').style.display = 'none';
}

function editProfil() {
    axios
        .put(`users/${userActive.value.id}`, userActive.value)
        .then(() => {
            store.startSnack("success", "no", "success");
            dialog3.value = false;
        })
        .catch(() => {
            store.startSnack("error", "no", "danger");
        });
}
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
    width: auto;
    color: white;
    padding: 2px 10px;

    .mdi-plus {
        font-size: 50px;
        font-weight: bold;
    }
}

.box-user {
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
