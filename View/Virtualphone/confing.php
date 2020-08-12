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
                            <el-form-item label="号码池Key" prop="pool_key">
                                <el-input v-model="formData.pool_key" placeholder="请输入号码池Key" clearable :style="{width: '100%'}">
                                </el-input>
                            </el-form-item>
                            <el-form-item label="默认过期时间" prop="expiration">
                                <el-input v-model="formData.expiration" placeholder="请输入默认解绑时间" clearable :style="{width: '100%'}">
                                    <template slot="append">秒</template>
                                </el-input>
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
                            id : "{$info.id}",
                            pool_key: "{$info.pool_key}",
                            expiration: "{$info.expiration}",
                        },
                        rules: {
                            pool_key: [{
                                required: true,
                                message: '请输入号码池Key',
                                trigger: 'blur'
                            }],
                            expiration: [{
                                required: true,
                                message: '请输入默认过期时间',
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
                            if (!valid) return
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