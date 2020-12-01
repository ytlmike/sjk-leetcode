<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>leetcoder</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/element-ui/2.14.1/theme-chalk/index.min.css">
    <style>
        * {
            margin: 0;
            padding: 0
        }

        .register-box {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateY(-50%) translateX(-50%);
        }

        .register-card {
            max-width: calc( 100vw - 60px );
            width: 300px;
            padding: 30px;
        }

        .avatar-uploader .el-upload {
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .avatar-uploader .el-upload:hover {
            border-color: #409EFF;
        }

        .avatar-uploader-icon {
            font-size: 28px;
            color: #8c939d;
            width: 178px;
            height: 178px;
            line-height: 178px;
            text-align: center;
        }

        .avatar {
            width: 178px;
            height: 178px;
            display: block;
        }

        .title {
            font-size: 28px;
            margin-bottom: 25px;
            color: #606266;
        }
    </style>
</head>
<body>
<div id="app" style="width: 100vw; height: 100vh; position: relative">
    <div class="register-box">
        <el-card class="register-card">
            <div class="title">
                欢迎加入
            </div>
            <el-form ref="form1" :rules="rules" :model="registerData" label-position="top" v-show="step===1">
                <el-form-item label="leetcode用户名：" prop="leetcode_slug">
                    <el-row>
                        <el-col :span="22">
                            <el-input v-model="registerData.leetcode_slug"></el-input>
                        </el-col>
                        <el-col :span="2">
                            <div style="padding-left: 10px">
                                <el-tooltip effect="light">
                                    <div slot="content">
                                        <p>不知道你的用户名？去leetcode个人主页看一下</p>
                                        <img src="/img/name.png" alt="" style="max-width: 280px">
                                    </div>
                                    <i class="el-icon-question" style="color: #a1a8ab; font-size: 24px"></i>
                                </el-tooltip>
                            </div>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item label="密码：" prop="password">
                    <el-input show-password v-model="registerData.password"></el-input>
                </el-form-item>
                <el-form-item label="确认密码：" prop="repeat_password">
                    <el-input show-password v-model="registerData.repeat_password"></el-input>
                </el-form-item>
            </el-form>
            <el-form ref="form2" :rules="rules" :model="registerData" label-position="top" v-show="step===2">
                <el-form-item label="名字：" prop="name">
                    <el-input v-model="registerData.name"></el-input>
                </el-form-item>
                <el-form-item label="头像：" prop="avatar">
                    <div style="text-align: center">
                        <el-upload
                            class="avatar-uploader"
                            action="avatar/upload"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload">
                            <img v-if="registerData.avatar" :src="registerData.avatar" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                    </div>
                </el-form-item>
            </el-form>
            <div style="text-align: center">
                <div style="margin-top: 15px">
                    <el-button type="primary" @click="fillProfile" v-show="step===1">继续</el-button>
                    <el-button type="primary" @click="submit" v-show="step===2">完成</el-button>
                </div>
                <div style="margin-top: 15px">
                    <el-link type="primary" href="/login">绑定过了？去登录</el-link>
                </div>
            </div>

        </el-card>
    </div>
</div>
</body>
<script src="https://cdn.staticfile.org/axios/0.1.0/axios.min.js"></script>
<script src="https://cdn.staticfile.org/vue/2.6.9/vue.min.js"></script>
<script src="https://cdn.staticfile.org/element-ui/2.14.1/index.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                step: 1,
                registerData: {
                    leetcode_slug: '',
                    password: '',
                    repeat_password: '',
                    name: '',
                    avatar: ''
                },
                rules: {
                    leetcode_slug: [{required: true, message: "请输入用户名"}],
                    password: [{required: true, message: "请输入密码"}],
                    repeat_password: [{required: true, validator: this.getRepeatPasswordValidator()}],
                    name: [{required: true, message: "你的名字？"}]
                },
            }
        },
        methods: {
            getRepeatPasswordValidator() {
                let self = this;
                return function (rule, value, callback) {
                    if (!value || value === '') {
                        callback(new Error('请输入密码'));
                    }
                    if (value !== self.registerData.password) {
                        callback(new Error('两次输入不一致'));
                    }
                    callback();
                }
            },
            fillProfile() {
                let valid = false;
                this.$refs['form1'].validate((val) => {
                    valid = val;
                });
                if (!valid) {
                    return;
                }

                let self = this;
                axios.get('/api/leetcode/user?leetcode_slug=' + this.registerData.leetcode_slug)
                    .then(function (response) {
                        if (response.code === 200) {
                            self.registerData.avatar = response.data.userAvatar;
                            self.registerData.name = response.data.realName;
                            self.step = 2;
                        } else {
                            self.$alert(response.msg, '提示', {
                                confirmButtonText: '确定',
                                callback: _ => {
                                    self.registerData.leetcode_slug = '';
                                    if (response.code === 409) {
                                        window.location.href = '/login';
                                    }
                                }
                            });
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            submit() {
                let valid = false;
                this.$refs['form2'].validate((val) => {
                    valid = val;
                });
                if (!valid) {
                    return;
                }

                axios.post('/api/register', this.registerData)
                    .then(function (response) {
                        if (response.code === 200) {
                            window.location.href = '/login';
                        } else {
                            this.$message(response.message);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            handleAvatarSuccess(res, file) {
                this.registerData.avatar = URL.createObjectURL(file.raw);
            },
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isPNG = file.type === 'image/png';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG && !isPNG) {
                    this.$message.error('上传头像图片只能是 JPG/JPEG/PNG 格式!');
                    return false;
                }
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                    return false;
                }
                return true;
            }
        }
    })
</script>
</html>
