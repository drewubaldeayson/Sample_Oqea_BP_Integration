<template>
    <button class="form-control"  type="button" @click="checkEmail()">Check Email</button>
</template>

<script>
    export default {
        data() {
            return {
                providers: []
            };
        },
        methods: {
            checkEmail() {
                let email = document.getElementById("email").value;
                axios
                    .get("/providers/"+email+"/check")
                    .then(response => {
                        if(JSON.parse(JSON.stringify(response)).data == ""){
                            alert("No data found in BP");
                        }else{
                            console.log("The data: ", response.data[0]);
                            document.getElementById("firstname").value = response.data[0].firstname
                            document.getElementById("lastname").value = response.data[0].lastname
                            document.getElementById("mobile_phone").value = response.data[0].mobilephone
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            }
        }
    }
</script>
