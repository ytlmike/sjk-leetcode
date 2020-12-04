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

        #app {
            width: 100vw;
            height: 100vh;
            position: relative
        }

        .login-box {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateY(-50%) translateX(-50%);
        }

        .login-card {
            width: 300px;
            padding: 30px;
        }

        .title {
            font-size: 28px;
            margin-bottom: 25px;
            color: #606266;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="login-box">
        <el-card class="login-card">
            <div class="title">
                你来啦
            </div>
            <el-form ref="form" :rules="rules" :model="loginData" label-position="top">
                <el-form-item label="leetcode用户名：" prop="leetcode_slug">
                    <el-row>
                        <el-col :span="22">
                            <el-input v-model="loginData.leetcode_slug"></el-input>
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
                <el-form-item label="本站密码：" prop="password">
                    <el-input show-password v-model="loginData.password"></el-input>
                </el-form-item>
            </el-form>
            <div style="text-align: center">
                <div style="margin-top: 15px">
                    <el-button type="primary" @click="submit">登录</el-button>
                </div>
                <div style="margin-top: 15px">
                    <el-link type="primary" href="/register">第一次来？先绑定leetcode吧</el-link>
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
        data: {
            loginData: {
                leetcode_slug: '',
                password: '',
            },
            rules: {
                leetcode_slug: [{required: true, message: "请输入用户名"}],
                password: [{required: true, message: "请输入密码"}]
            }
        },
        methods: {
            submit() {
                let self = this;
                let valid = false;
                this.$refs['form'].validate((val) => {
                    valid = val;
                });
                if (!valid) {
                    return;
                }
                axios.post('/api/login', this.loginData)
                    .then(function (response) {
                        if (response.code === 200) {
                            window.location.href = '/';
                        } else {
                            self.$message(response.msg);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }
    })
</script>
</html>
