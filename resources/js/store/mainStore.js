import moment from "moment";
moment.locale(localStorage.language + "-dz");
import axios from "axios";
import { defineStore } from "pinia";
export const usemainStore = defineStore("mainStore", {
    state: () => ({
        passToggle: true,
        popup: false,
        auth: false,
        user: localStorage.user ? JSON.parse(localStorage.user) : false,
        users: ["getData"],
        reports: "getData",
        absences: '',
        regular: '',
        restallowance: '',
        rest: '',
        printLog: '',
        snackbar: false,
        redirect: false,
        text: " ",
        coler: "success",
        urlDirec: " ",
        timeout: 1000,
        colorBTN: '#08BC45',
    }),
    actions: {
        setAuthHeaderNew(token) {
            if (token) {
                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
                axios
                    .get(`check`)
                    .then((res) => {
                        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
                        this.auth = true;
                    })
                    .catch(() => {
                        delete axios.defaults.headers.common["Authorization"];
                        localStorage.removeItem("token");
                        localStorage.removeItem("user");
                        this.auth = false;
                        this.startSnack("error", "login", "danger", false);
                    });
            } else {
                delete axios.defaults.headers.common["Authorization"];
                this.auth = false;
                this.startSnack("error", "login", "danger", false);
            }
        },
        startSnack(text, urlDirect, coler = "success", snack = true, timeout = 2000) {
            this.text = text;
            this.coler = coler;
            this.urlDirec = urlDirect;
            this.timeout = timeout;
            this.snackbar = snack;
            this.redirect = true;
        },
        getUser() {
            if (this.user.id) {
                axios
                    .get(`users/${JSON.parse(localStorage.user).id}`)
                    .then((res) => {
                        this.user = res.data;
                    })
                    .catch(() => {
                        this.startSnack("error", "login", "danger");
                    });
            }
        },
        getReports() {
            axios.get(`user/${this.user.id}/reports`)
                .then((res) => {
                    if (res.data.length == 0) {
                        this.reports = "noData";
                    } else {
                        this.reports = res.data;
                    }
                })
                .catch(() => {
                    this.reports = "noData";
                    this.startSnack("error", "login", "danger");
                });
        },
        getUsers() {
            axios.get(`users`)
                .then((res) => {
                    if (res.data.length == 0) {
                        this.users = ["noData"];
                    } else {
                        this.users = res.data;
                    }
                })
                .catch(() => {
                    this.users = ["noData"];
                    this.startSnack("error", "login", "danger");
                });
        },
        getAbsences() {
            axios
                .get(`absence`)
                .then((res) => {
                    this.absences = res.data;
                })
                .catch((e) => {
                    this.startSnack("error", "login", "danger");
                });
            axios.get(`regular`).then((res) => {
                this.regular = res.data.balance;
            });
            axios.get(`rest`).then((res) => {
                this.rest = res.data.balance;
            });
            return axios.get(`restallowance`).then((res) => {
                this.restallowance = res.data;
            });
        },
        formatDate(date, format = 'YYYY-MM-DD') {
            // format = 'YYYY-MM-DD'
            return moment(date).format(format);
        }
    },
});
