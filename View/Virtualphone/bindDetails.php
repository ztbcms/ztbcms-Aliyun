<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <el-col :sm="16" :md="8" >
                <!--                插入template 文件-->
                <template>
                    <div>
                        <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="100px"
                                 label-position="top">
                            <el-form-item label="号码A" prop="PhoneNoA">
                                <el-input v-model="formData.PhoneNoA" placeholder="请输入号码A" clearable :style="{width: '100%'}">
                                    <template slot="append">手机号码</template>
                                </el-input>
                            </el-form-item>
                            <el-form-item label="号码B" prop="PhoneNoB">
                                <el-input v-model="formData.PhoneNoB" placeholder="请输入号码B" clearable :style="{width: '100%'}">
                                    <template slot="append">手机号码</template>
                                </el-input>
                            </el-form-item>
                            <el-form-item v-if="formData.secret_no" label="虚拟号码" prop="secret_no">
                                <el-input v-model="formData.secret_no" placeholder="请输入虚拟号码" readonly :disabled='true' clearable
                                          :style="{width: '100%'}"></el-input>
                            </el-form-item>
                            <el-form-item size="large">
                                <el-button type="primary" @click="submitForm">提交</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </template>
            </el-col>
        </el-card>
    </div>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                // 插入export default里面的内容
                components: {},
                props: [],
                data() {
                    return {
                        formData: {
                            PhoneNoA: '',
                            PhoneNoB: '',
                            secret_no: ''
                        },
                        rules: {
                            PhoneNoA: [{
                                required: true,
                                message: '请输入号码A',
                                trigger: 'blur'
                            }],
                            PhoneNoB: [{
                                required: true,
                                message: '请输入号码B',
                                trigger: 'blur'
                            }],
                        },
                    }
                },
                computed: {},
                watch: {},
                created() {},
                mounted() {},
                methods: {
                    submitForm() {

                        var that = this;

                        that.$refs['elForm'].validate(valid => {
                            if (!valid) return;
                            // TODO 提交表单

                            $.ajax({
                                url: "{:U('getBindAxb')}",
                                method: 'post',
                                dataType: 'json',
                                data: this.formData,
                                success: function (res) {
                                    if (!res.status) {
                                        layer.msg(res.msg)
                                    } else {
                                        that.formData.secret_no = res.data.secret_no;
                                        layer.msg(res.msg)
                                    }
                                }
                            });
                        })
                    }
                }
            });
        });
    </script>

    <style>

    </style>

</block>