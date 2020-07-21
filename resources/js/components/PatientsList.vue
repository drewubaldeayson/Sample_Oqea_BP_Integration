<template>
    <div class="usres-style">
        <div class="table-style">
            <input
                class="input"
                type="text"
                v-model="search"
                placeholder="Search..."
                @input="resetPagination()"
                style="width: 250px;"
            />
            <div class="control">
                <div class="select">
                    <select v-model="length" @change="resetPagination()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.name"
                        @click="sortBy(column.name)"
                        :class="
                            sortKey === column.name
                                ? sortOrders[column.name] > 0
                                    ? 'sorting_asc'
                                    : 'sorting_desc'
                                : 'sorting'
                        "
                        style="width: 40%; cursor: pointer;"
                    >
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="patient in paginatedPatients" :key="patient.id">
                    <td>{{ patient.patient_name }}</td>
                    <td>{{ patient.address }}</td>
                    <td>{{ patient.dob }}</td>
                    <td>{{ patient.sex }}</td>
                    <td>{{ patient.ethnicity }}</td>
                    <td>{{ patient.home_phone }}</td>
                    <td>{{ patient.work_phone }}</td>
                    <td>{{ patient.mobile_phone }}</td>
                    <td>{{ patient.email }}</td>
                    <td>{{ patient.medicare_no }}</td>
                    <td>{{ patient.pension_no }}</td>
                    <td>{{ patient.religion }}</td>
                    <td>{{ patient.usual_doctor }}</td>
                </tr>
            </tbody>
        </table>
        <div>
            <nav class="pagination" v-if="!tableShow.showdata">
                <span class="page-stats"
                    >{{ pagination.from }} - {{ pagination.to }} of
                    {{ pagination.total }}</span
                >
                <a
                    v-if="pagination.prevPageUrl"
                    class="btn btn-sm btn-primary pagination-previous"
                    @click="--pagination.currentPage"
                >
                    Prev
                </a>
                <a
                    class="btn btn-sm btn-primary pagination-previous"
                    v-else
                    disabled
                >
                    Prev
                </a>
                <a
                    v-if="pagination.nextPageUrl"
                    class="btn btn-sm pagination-next"
                    @click="++pagination.currentPage"
                >
                    Next
                </a>
                <a
                    class="btn btn-sm btn-primary pagination-next"
                    v-else
                    disabled
                >
                    Next
                </a>
            </nav>

            <nav class="pagination" v-else>
                <span class="page-stats">
                    {{ pagination.from }} - {{ pagination.to }} of
                    {{ filteredUsers.length }}

                    <span
                        v-if="`filteredUsers.length < pagination.total`"
                    ></span>
                </span>
                <a
                    v-if="pagination.prevPage"
                    class="btn btn-sm btn-primary pagination-previous"
                    @click="--pagination.currentPage"
                >
                    Prev
                </a>
                <a
                    class="btn btn-sm pagination-previous btn-primary"
                    v-else
                    disabled
                >
                    Prev
                </a>
                <a
                    v-if="pagination.nextPage"
                    class="btn btn-sm btn-primary pagination-next"
                    @click="++pagination.currentPage"
                >
                    Next
                </a>
                <a
                    class="btn btn-sm pagination-next btn-primary"
                    v-else
                    disabled
                >
                    Next
                </a>
            </nav>
        </div>
    </div>
</template>

<script>
    export default {
        created() {
            this.getPatients();
            Fire.$on("reloadPatients", () => {
                this.getPatients();
            });
        },
        data() {
            let sortOrders = {};
            let columns = [
                { label: "Patient", name: "patient_name" },
                { label: "Address", name: "address" },
                { label: "Date of Birth", name: "dob" },
                { label: "Sex", name: "sex" },
                { label: "Ethnicity", name: "ethnicity" },
                { label: "Home Phone No.", name: "home_phone" },
                { label: "Work Phone No.", name: "work_phone" },
                { label: "Mobile Phone No.", name: "mobile_phone" },
                { label: "Email", name: "email" },
                { label: "Medicare No.", name: "medicare_no" },
                { label: "Pension No.", name: "pension_no" },
                { label: "Religion", name: "religion" },
                { label: "Usual Doctor", name: "usual_doctor" },
            ];

            columns.forEach(column => {
                sortOrders[column.name] = -1;
            });

            return {
                patients: [],
                columns: columns,
                sortKey: "patient_name",
                sortOrders: sortOrders,
                length: 10,
                search: "",
                tableShow: {
                    showdata: true
                },
                pagination: {
                    currentPage: 1,
                    total: "",
                    nextPage: "",
                    prevPage: "",
                    from: "",
                    to: ""
                }
            };
        },
        methods: {
            getPatients() {
                axios
                    .get("/patients/", { params: this.tableShow })
                    .then(response => {
                        console.log("The data: ", response.data);
                        this.patients = response.data;
                        this.pagination.total = this.patients.length;
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            paginate(array, length, pageNumber) {
                this.pagination.from = array.length
                    ? (pageNumber - 1) * length + 1
                    : " ";
                this.pagination.to =
                    pageNumber * length > array.length
                        ? array.length
                        : pageNumber * length;
                this.pagination.prevPage = pageNumber > 1 ? pageNumber : "";
                this.pagination.nextPage =
                    array.length > this.pagination.to ? pageNumber + 1 : "";
                return array.slice((pageNumber - 1) * length, pageNumber * length);
            },
            resetPagination() {
                this.pagination.currentPage = 1;
                this.pagination.prevPage = "";
                this.pagination.nextPage = "";
            },
            sortBy(key) {
                this.resetPagination();
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },
            getIndex(array, key, value) {
                return array.findIndex(i => i[key] == value);
            }
        },
        computed: {
            filteredUsers() {
                let patients = this.patients;
                if (this.search) {
                    patients = patients.filter(row => {
                        return Object.keys(row).some(key => {
                            return (
                                String(row[key])
                                    .toLowerCase()
                                    .indexOf(this.search.toLowerCase()) > -1
                            );
                        });
                    });
                }
                let sortKey = this.sortKey;
                let order = this.sortOrders[sortKey] || 1;

                if (sortKey) {
                    patients = patients.slice().sort((a, b) => {
                        let index = this.getIndex(this.columns, "name", sortKey);
                        a = String(a[sortKey]).toLowerCase();
                        b = String(b[sortKey]).toLowerCase();
                        if (
                            this.columns[index].type &&
                            this.columns[index].type === "date"
                        ) {
                            return (
                                (a === b
                                    ? 0
                                    : new Date(a).getTime() > new Date(b).getTime()
                                    ? 1
                                    : -1) * order
                            );
                        } else if (
                            this.columns[index].type &&
                            this.columns[index].type === "number"
                        ) {
                            return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;
                        } else {
                            return (a === b ? 0 : a > b ? 1 : -1) * order;
                        }
                    });
                }
                return patients;
            },
            paginatedPatients() {
                return this.paginate(
                    this.filteredUsers,
                    this.length,
                    this.pagination.currentPage
                );
            }
        }
    }
</script>
