<template>
    <div class="card">
        <div class="card-header">{{ response.table}}</div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" v-for="column in response.displayable">
                            {{ column }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="record in response.records">
                        <td v-for="columnValue, column in record">{{ columnValue}}</td>
                        <td>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        props: ['endpoint'],
        data () {
            return {
                response: {
                    displayable: [],
                    records: [],
                    user: ''
                }
            }
        },
        methods: {
            getRecords () {
                return axios.get(`${this.endpoint}`).then(response => {
                    this.response = response.data.data
                })
            }
        },
        mounted() {
                this.getRecords()
        }
    }
</script>
