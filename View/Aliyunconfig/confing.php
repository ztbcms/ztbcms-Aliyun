<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <el-col :sm="16" :md="12" >
                <!--                插入template 文件-->

                <template>
                    <div>
                        <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="100px"
                                 label-position="top">
                            <el-form-item label="access_key_id" prop="access_key_id">
                                <el-input v-model="formData.access_key_id" placeholder="请输入access_key_id" clearable
                                          :style="{width: '100%'}"></el-input>
                            </el-form-item>
                            <el-form-item label="access_secret" prop="access_secret">
                                <el-input v-model="formData.access_secret" placeholder="请输入access_secret" clearable
                                          :style="{width: '100%'}"></el-input>
                            </el-form-item>
                            <el-form-item size="large">
                                <el-button type="primary" @click="submitForm">保存</el-button>
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
                            id : "{$info.id}",
                            access_key_id: "{$info.access_key_id}",
                            access_secret: "{$info.access_secret}",
                        },
                        rules: {
                            access_key_id: [{
                                required: true,
                                message: '请输入access_key_id',
                                trigger: 'blur'
                            }],
                            access_secret: [{
                                required: true,
                                message: '请输入access_secret',
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
                        this.$refs['elForm'].validate(valid => {
                            if (!valid) return;
                            // TODO 提交表单
                            $.ajax({
                                url: "{:U('confing')}",
                                method: 'post',
                                dataType: 'json',
                                data: this.formData,
                                success: function (res) {
                                    if (!res.status) {
                                        layer.msg(res.msg)
                                    } else {
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