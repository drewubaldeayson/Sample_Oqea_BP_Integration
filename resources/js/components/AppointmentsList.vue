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
                <button class="btn btn-success" @click="openModal()"><i class="fa fa-plus"></i> Add Appointment</button>
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
                <tr v-for="appointment in paginatedAppointments" :key="appointment.id">
                    <td>{{ appointment.patient }}</td>
                    <td>{{ appointment.internalid }}</td>
                    <td>{{ appointment.appointmentstartdatetime }}</td>
                    <td>{{ appointment.appointmentenddatetime }}</td>
                    <td>{{ appointment.appointmentlength }}</td>
                    <td>{{ appointment.provider }}</td>
                    <td>{{ appointment.urgent }}</td>
                    <td>{{ appointment.appointmenttype }}</td>
                    <td>{{ appointment.status }}</td>
                    <td>{{ appointment.arrivaltime }}</td>
                    <td>{{ appointment.consultationtime }}</td>
                    <td>{{ appointment.bookedby }}</td>
                    <td>{{ appointment.comment }}</td>
                    <td>{{ appointment.itemlist }}</td>
                    
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
        <div class="modal fade" id="appointmentsModal" tabindex="-1" role="dialog" aria-labelledby="appointmentsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title">Add New Appointment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="addAppointment()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input  v-model="rawAppointment.appointmentStartDateTime" type="datetime-local" name="start_datetime" placeholder=""
                                    class="form-control" :class="{ 'is-invalid': rawAppointment.errors.has('start_datetime') }">
                                <has-error :form="rawAppointment" field="start_datetime"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="rawAppointment.appointmentEndDateTime" type="datetime-local" name="end_datetime" placeholder=""
                                    class="form-control" :class="{ 'is-invalid': rawAppointment.errors.has('end_datetime') }">
                                <has-error :form="rawAppointment" field="end_datetime"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="rawAppointment.practitionerId" name="practitionerId" id = "practitionerId"
                                    class="form-control" :class="{ 'is-invalid': rawAppointment.errors.has('practitionerId') }">
                                    <option value="" disabled selected>Select Practitioner</option>
                                    <option value="3" >Dr. Ivor Cure</option>
                                </select>
                                <has-error :form="rawAppointment" field="practitionerId"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="rawAppointment.patientId" name="patientId" id = "patientId"
                                    class="form-control" :class="{ 'is-invalid': rawAppointment.errors.has('patientId') }">
                                    <option value="" disabled selected>Select Patient</option>
                                    <option value="1" >Anastasia Abbott</option>
                                    <option value="2" >Allan Abbott</option>
                                    <option value="6" >Alfred Charles Aldridge</option>
                                </select>
                                <has-error :form="rawAppointment" field="patientId"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="rawAppointment.loginId" name="loginId" id = "loginId"
                                    class="form-control" :class="{ 'is-invalid': rawAppointment.errors.has('loginId') }">
                                    <option value="" disabled selected>Select Login</option>
                                    <option value="3" >Dr. Ivor Cure</option>
                                </select>
                                <has-error :form="rawAppointment" field="loginId"></has-error>
                            </div>
                             <div class="form-group">
                                <select v-model="rawAppointment.locationId" name="locationId" id = "locationId"
                                    class="form-control" :class="{ 'is-invalid': rawAppointment.errors.has('locationId') }">
                                    <option value="" disabled selected>Select Location</option>
                                    <option value="1" >Default</option>
                                </select>
                                <has-error :form="rawAppointment" field="locationId"></has-error>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Add</button>
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
            this.getAppointments();
            Fire.$on("reloadAppointments", () => {
                this.getAppointments();
            });
            // this.getPatients();
        },
        data() {
            let sortOrders = {};
            let columns = [
                { label: "Patient", name: "patient" },
                { label: "Internal ID", name: "internalid" },
                { label: "Appointment Start", name: "appointmentstartdatetime" },
                { label: "Appointment End", name: "appointmentenddatetime" },
                { label: "Appointment Length", name: "appointmentlength" },
                { label: "Provider", name: "provider" },
                { label: "Urgent", name: "urgent" },
                { label: "Appointment Type", name: "appointmenttype" },
                { label: "Status", name: "status" },
                { label: "Arrival Time", name: "arrivaltime" },
                { label: "Consultation Time", name: "consultationtime" },
                { label: "Booked By", name: "bookedby" },
                { label: "Comment", name: "comment" },
                { label: "Item List", name: "itemlist" },
            ];

            columns.forEach(column => {
                sortOrders[column.name] = -1;
            });


            return {
                editMode: false,
                appointments: [],
                rawAppointment: new Form({
                    appointmentStartDateTime: "",
                    appointmentEndDateTime: "",
                    practitionerId:"",
                    patientId:"",
                    loginId:"",
                    locationId:""
                }),
                patients: [],
                columns: columns,
                sortKey: "appointmentstartdatetime",
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
            resetForm(){
                this.rawAppointment.reset();
                this.rawAppointment.clear();
            },
            getAppointments() {
                axios
                    .get("/appointments/", { params: this.tableShow })
                    .then(response => {
                        console.log("The data: ", response.data);
                        this.appointments = response.data;
                        this.pagination.total = this.appointments.length;
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            addAppointment(){
                console.log(this.rawAppointment.appointmentStartDateTime)
                this.rawAppointment.post('/appointments').then((addAppointmentResult)=>{
                    $("#appointmentsModal").modal("hide")
                    toast.fire({
                        type:'success',
                        icon:'success',
                        title:addAppointmentResult.data.message.toString()
                    })
                }).catch((err)=>{
                    if(!err.message.toString().includes('422')){
                        swal.fire(
                            'Error has occurred!',
                            "Error in adding appointment",
                            'error'
                        )
                    }
                })
            },
            getPatients(){
                axios
                    .get("/patients")
                    .then(response => {
                        console.log("The data: ", response.data);
                        this.patients = response.data;
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            openModal(){
                this.editMode = false;
                this.resetForm();
                $("#appointmentsModal").modal("show")
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
                let appointments = this.appointments;
                if (this.search) {
                    appointments = appointments.filter(row => {
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
                    appointments = appointments.slice().sort((a, b) => {
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
                return appointments;
            },
            paginatedAppointments() {
                return this.paginate(
                    this.filteredUsers,
                    this.length,
                    this.pagination.currentPage
                );
            }
        }
    }
</script>
