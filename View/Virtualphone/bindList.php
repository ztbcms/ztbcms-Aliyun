<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <div class="filter_container">
                <el-row :gutter="10">
                    <el-col :span="3">
                        <el-input v-model="form.phone_no_a" placeholder="号码A" size="medium"/>
                    </el-col>

                    <el-col :span="3">
                        <el-input v-model="form.phone_no_b" placeholder="号码B" size="medium"/>
                    </el-col>

                    <el-col :span="3">
                        <el-input v-model="form.secret_no" placeholder="隐私号码" size="medium"/>
                    </el-col>

                    <el-col :span="4">
                        <el-date-picker
                            v-model="form.search_date"
                            value-format="yyyy-MM-dd HH:mm:ss"
                            type="datetimerange"
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期" size="medium">
                        </el-date-picker>
                    </el-col>
                </el-row>

                <el-row :gutter="10" style="margin-top: 10px;">

                    <el-col :span="10">
                        <el-button type="primary" @click="getList" size="medium">
                            筛选
                        </el-button>

                        <el-button @click="getBindAxb" type="primary" plain size="medium">
                            绑定号码
                        </el-button>
                    </el-col>
                </el-row>
            </div>


            <el-tabs type="border-card" style="margin-top: 20px;"
                     @tab-click="handleClickTabs">
                <el-tab-pane label="全部"></el-tab-pane>
                <el-tab-pane label="成功"></el-tab-pane>
                <el-tab-pane label="绑定失败"></el-tab-pane>
                <el-tab-pane label="已解绑"></el-tab-pane>

                <el-table
                    size="medium"
                    :data="tableData"
                    border
                    style="width: 100%;"
                    @sort-change="handleSortChange">
                    <el-table-column
                        prop="id"
                        label="ID"
                        width="80">
                    </el-table-column>

                    <el-table-column
                            prop="secret_no"
                            label="隐私号码"
                            width="150">
                    </el-table-column>

                    <el-table-column
                        prop="phone_no_a"
                        label="号码A"
                        width="150">
                    </el-table-column>

                    <el-table-column
                            prop="phone_no_b"
                            label="号码B"
                            width="150">
                    </el-table-column>

                    <el-table-column
                        prop="bind_status"
                        label="状态"
                        width="80">
                        <template slot-scope="scope">
                            <span v-if="scope.row.bind_status == 1" style="color: green">已绑定</span>
                            <span v-if="scope.row.bind_status == 2" style="color: red">绑定失败</span>
                            <span v-if="scope.row.bind_status == 3" style="color: red">已解绑</span>
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="message"
                        label="描述">
                        <template slot-scope="scope">
                            <p v-html="scope.row.message"></p>
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="time"
                        label="绑定时间"
                        width="200"
                        sortable="custom"
                    >
                        <template slot-scope="scope">
                            <span>{{ scope.row.add_time | formatTime }}</span>
                        </template>
                    </el-table-column>

                </el-table>
                <div class="pager_container">
                    <el-pagination
                        background
                        layout="prev, pager, next"
                        :page-size="pagination.limit"
                        :current-page.sync="pagination.page"
                        :total="pagination.total_items"
                        @current-change="getList">
                    </el-pagination>
                </div>
            </el-tabs>


        </el-card>


    </div>

    <style>
        .filter_container{
            background: #f8f8f8;
            padding: 25px 36px 12px;
        }
        .pager_container {

        }
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    tableData: [],
                    pagination: {
                        page: 1,
                        limit: 20,
                        total_pages: 0,
                        total_items: 0,
                    },

                    form: {
                        search_date: [],
                        phone_no_a: '',
                        phone_no_b: '',
                        start_time: '',
                        end_time: '',
                        bind_status: '',
                        sort_time: '',//排序：时间
                    }
                },
                watch: {
                    'form.search_date': function (newValue) {
                        if (newValue && newValue.length == 2) {
                            this.form.start_time = newValue[0]
                            this.form.end_time = newValue[1]
                        }
                    }
                },
                filters: {
                    formatTime(timestamp) {
                        var date = new Date();
                        date.setTime(parseInt(timestamp) * 1000);
                        return moment(date).format('YYYY-MM-DD HH:mm:ss')
                    }
                },
                methods: {
                    getList: function () {
                        var that = this;
                        var where = {
                            page: this.pagination.page,
                            limit: this.pagination.limit,
                            bind_status: this.form.bind_status,
                            start_time: this.form.start_time,
                            end_time: this.form.end_time,
                            phone_no_a: this.form.phone_no_a,
                            phone_no_b: this.form.phone_no_b,
                            secret_no: this.form.secret_no,
                            sort_time : this.form.sort_time
                        };
                        $.ajax({
                            url: "{:U('Aliyun/Virtualphone/bindList')}",
                            data: where,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                var data = res.data;
                                that.pagination.page = data.page;
                                that.pagination.limit = data.limit;
                                that.pagination.total_pages = data.total_pages;
                                that.pagination.total_items = data.total_items;
                                that.tableData = data.items
                            }
                        })
                    },
                    getBindAxb: function () {
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '操作',
                            content: "/Aliyun/Virtualphone/bindDetails",
                            area: ['80%', '70%'],
                            end: function () {
                                that.getList()
                            }
                        })
                    },
                    handleClickTabs(tab){
                        if(tab.index == 0){
                            this.form.bind_status = '';
                        }
                        if(tab.index == 1){
                            this.form.bind_status = '1';
                        }
                        if(tab.index == 2){
                            this.form.bind_status = '2';
                        }
                        if(tab.index == 3){
                            this.form.bind_status = '3';
                        }
                        this.getList()
                    },
                    handleSortChange(event){
                        if(event.prop == 'time'){
                            if(event.order.toLowerCase().indexOf('asc') >= 0){
                                this.form.sort_time = 'asc';
                                this.getList();
                                return;
                            }else if(event.order.toLowerCase().indexOf('desc') >= 0){
                                this.form.sort_time = 'desc';
                                this.getList();
                                return;
                            }
                        }

                        this.form.sort_time = '';
                        this.getList();
                    }

                },
                mounted: function () {
                    this.getList();
                }
            })
        })
    </script>
</block>
