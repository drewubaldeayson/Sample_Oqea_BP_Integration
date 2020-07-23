<template>
    <div class="users-style">
        
        <div class="table-style">
            <input
                class="input"
                type="text"
                v-model="search"
                placeholder="Search..."
                @input="resetPagination()"
                style="width: 250px;"
            />
            <div class="card-tools">
                <button class="btn btn-success" @click="openModal()"><i class="fa fa-plus"></i> Add Patient</button>
            </div>
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
                    <td>{{ patient.marital_status }}</td>
                    <td>{{ patient.religion }}</td>
                    <td>{{ patient.birth_country }}</td>
                    <td>{{ patient.employment }}</td>
                    <td>{{ patient.occupation }}</td>
                    <td>{{ patient.home_phone }}</td>
                    <td>{{ patient.work_phone }}</td>
                    <td>{{ patient.mobile_phone }}</td>
                    <td>{{ patient.email }}</td>
                    <td>{{ patient.medicare }}</td>
                    <td>{{ patient.medicare_expiry }}</td>
                    <td>{{ patient.health_fund }}</td>
                    <td>{{ patient.health_fund_membership_no }}</td>
                    <td>{{ patient.dva_card_no }}</td>
                    <td>{{ patient.dva_card_expiry }}</td>
                    <td>{{ patient.dva_card_type }}</td>
                    <td>{{ patient.pension_no }}</td>
                    <td>{{ patient.pension_type }}</td>
                    <td>{{ patient.pension_expiry }}</td>
                    <td>{{ patient.ihi }}</td>
                    <td>{{ patient.next_kin }}</td>
                    <td v-if="patient.record_status == 1"><span class="right badge badge-success">Active</span></td>
                    <td v-else><span class="right badge badge-danger">Deleted</span></td>
                    <td v-if="patient.record_status != 0">
                        <a href="#" @click="deletePatient(patient.internal_id)">
                            <i class="fa fa-stop-circle text-red"></i>
                        </a>
                    </td>
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
        <div class="modal fade" id="patientsModal" tabindex="-1" role="dialog" aria-labelledby="patientsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Patient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
        
                    <form @submit.prevent="addPatient()">
                        <div class="modal-body">
                            <div class="form-group">
                                <select v-model="rawPatients.titleCode"  name="titleCode" id = "titleCode"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('titleCode') }">
                                    <option value="" disabled selected>Select Title</option>
                                    <option value="0" >Mr</option>
                                    <option value="1" >Ms</option>
                                </select>
                                <has-error :form="rawPatients" field="titleCode"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.firstname" type="text" name="firstname" placeholder="Enter First Name"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('firstname') }">
                                <has-error :form="rawPatients" field="firstname"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.middlename" type="text" name="middlename" placeholder="Enter Middle Name"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('middlename') }">
                                <has-error :form="rawPatients" field="middlename"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.lastname" type="text" name="lastname" placeholder="Enter Last Name"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('lastname') }">
                                <has-error :form="rawPatients" field="lastname"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.address" type="text" name="address" placeholder="Enter Address"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('address') }">
                                <has-error :form="rawPatients" field="address"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.city" type="text" name="city" placeholder="Enter City"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('city') }">
                                <has-error :form="rawPatients" field="city"></has-error>
                            </div>
                             <div class="form-group">
                                <input  v-model="rawPatients.postcode" type="text" name="postcode" placeholder="Enter Post Code"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('postcode') }">
                                <has-error :form="rawPatients" field="postcode"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.dob" type="datetime-local" name="dob" placeholder="Select Date of Birth"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('dob') }">
                                <has-error :form="rawPatients" field="dob"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="rawPatients.sex"  name="sex" id = "sex"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('sex') }">
                                    <option value="" disabled selected>Select Sex</option>
                                    <option value="1" >Male</option>
                                    <option value="2" >Female</option>
                                </select>
                                <has-error :form="rawPatients" field="sex"></has-error>
                            </div>
                            <div class="form-group">
                                <input  v-model="rawPatients.email" type="email" name="email" placeholder="Enter Email Address"
                                    class="form-control" :class="{ 'is-invalid': rawPatients.errors.has('email') }">
                                <has-error :form="rawPatients" field="email"></has-error>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
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
                { label: "Marital Status", name: "marital_status" },
                { label: "Religion", name: "religion" },
                { label: "Birth Country", name: "birth_country" },
                { label: "Employment", name: "employment" },
                { label: "Occupation", name: "occupation" },
                { label: "Home Phone No.", name: "home_phone" },
                { label: "Work Phone No.", name: "work_phone" },
                { label: "Mobile Phone No.", name: "mobile_phone" },
                { label: "Email", name: "email" },
                { label: "Medicare No.", name: "medicare" },
                { label: "Medicare Expiry", name: "medicare_expiry" },
                { label: "Health Fund", name: "health_fund" },
                { label: "Health Fund Membership No.", name: "health_fund_membership_no" },
                { label: "DVA Card No.", name: "dva_card_no" },
                { label: "DVA Card Expiry", name: "dva_card_expiry" },
                { label: "DVA Card Type", name: "dva_card_type" },
                { label: "Pension No.", name: "pension_no" },
                { label: "Pension Type", name: "pension_type" },
                { label: "Pension Expiry", name: "pension_expiry" },
                { label: "Ihi", name: "ihi" },
                { label: "Next Kin", name: "next_kin" },
                { label: "Actions" },
            ];

            columns.forEach(column => {
                sortOrders[column.name] = -1;
            });

            return {
                patients: [],
                rawPatients: new Form({
                    titleCode: "",
                    firstname: "",
                    middlename: "",
                    lastname: "",
                    address: "",
                    city: "",
                    postcode: "",
                    dob: "",
                    sex: "",
                    email: "",
                    internal_id: ""
                }),
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
            addPatient(){
                console.log(this.rawPatients)
                this.rawPatients.post('/patients').then((addPatientResult)=>{
                    $("#patientsModal").modal("hide")
                    toast.fire({
                        type:'success',
                        icon:'success',
                        title:addPatientResult.data.message.toString()
                    })
                }).catch((err)=>{
                    if(!err.message.toString().includes('422')){
                        swal.fire(
                            'Error has occurred!',
                            "Error in adding patient",
                            'error'
                        )
                    }
                })
            },
            deletePatient(internal_id){
                swal.fire({
                    title: 'Are you sure you want to delete this patient?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.value) {
                        this.rawPatients.internal_id=internal_id;
                        this.rawPatients.post('/patients/remove').then((formDeleteResult)=>{
                            toast.fire({
                                icon:'success',
                                type:'success',
                                title:formDeleteResult.data.message.toString(),
                            })
                        }).catch((formDeleteErr)=> {
                            swal.fire(
                                'Error has occurred!',
                                'Unable to delete this patient',
                                'error'
                            )
                        })
                    }
                })
            },
            resetForm(){
                this.rawPatients.reset();
                this.rawPatients.clear();
            },
            openModal(){
                this.editMode = false;
                this.resetForm();
                $("#patientsModal").modal("show")
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
