<template>
    <div class="card">
        <div class="card-header">{{ response.table}}</div>

        <div class="card-body">
            <form action="#" @submit.prevent="getRecords">
                <label for="">Search</label>
                <div class="row row-fluid">
                    <div class="form-group col-md-3">
                        <select class="form-control" v-model="search.column">
                            <option :value="column" v-for="column in response.displayable">{{ column }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select class="form-control" v-model="search.operator">
                            <option value="equals"> = </option>
                            <option value="contains"> contains </option>
                            <option value="starts_with"> starts with </option>
                            <option value="greater_than"> greater than </option>
                            <option value="less_than"> less than </option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="input-group">
                            <input type="text" id="search"  class="form-control" v-model="search.value">
                            <span class="input-group-btn">
                                <button class="btn btn-secondary" type="submit">Search</button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="filter">Quick search current results</label>
                    <input type="text" id="filter" class="form-control" v-model="quickSearchQuery">
                </div>
                <div class="form-group col-md-2">
                    <label for="limit">Limit</label>
                    <select  id="limit" v-model="limit" class="form-control" @change="getRecords">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="">All</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" v-for="column in response.displayable">
                            <span class="sortable" @click="sortBy(column)">{{ column }}</span>
                            <div
                                class="arrow"
                                 v-if="sort.key === column"
                                 :class="{'arrow--asc': sort.order === 'asc', 'arrow--desc' : sort.order === 'desc'}"
                            ></div>
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="record in filteredRecords">
                        <td v-for="columnValue, column in record">{{ columnValue}}</td>
                        <td>
                            <a href="#" @click="edit(record)">Edit</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>

<script>
    const queryString = require('query-string')
    export default {
        props: ['endpoint'],
        data () {
            return {
                response: {
                    displayable: [],
                    records: [],
                    user: ''
                },
                sort: {
                    key: 'id',
                    order: 'asc'
                },
                search: {
                    value: '',
                    operator: 'equals',
                    column: 'id'
                },
                quickSearchQuery: '',
                limit: 50
            }
        },
        computed: {
            filteredRecords () {
                let data =  this.response.records

                data = data.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLocaleLowerCase().indexOf(this.quickSearchQuery.toLocaleLowerCase()) > - 1
                    })
                })

                if(this.sort.key) {
                    data = _.orderBy(data, (i) => {
                        let value = i[this.sort.key]

                        if(!isNaN(parseFloat(value))) {
                            return parseFloat (value)
                        }

                        return  String(i[this.sort.key]).toLowerCase()
                    }, this.sort.order)
                }

                return data
            }
        },
        methods: {
            getRecords () {
                return axios.get(`${this.endpoint}?${this.getQueryParameter()}`).then(response => {
                    this.response = response.data.data
                })
            },
            getQueryParameter () {
              return queryString.stringify({
                  limit: this.limit,
                  ...this.search
              })
            },
            sortBy(column) {
                this.sort.key = column
                this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
            }
        },
        mounted() {
                this.getRecords()
        }
    }
</script>

<style lang="scss">
    .sortable {
        cursor: pointer;
    }
    .arrow {
        display: inline-block;
        vertical-align: middle;
        width: 0;
        height: 0;
        margin-left: 5px;
        opacity: .6;

        &--asc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #222;
        }
        &--desc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #222;
        }
    }
</style>
