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

        .ranking-container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateY(-50%) translateX(-50%);
            max-width: 100vw;
            max-height: 100vh;
            width: 1000px;
            color: #606266;
        }

        .ranking-card {
            padding: 15px
        }

        .ranking-title {
            font-size: 32px;
            margin-bottom: 25px;
        }

        .ranking-item {
            padding: 15px 20px;
        }

        .my {
            box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
        }

        .head {
            text-align: right;
            margin-right: 30px;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="head">
        <el-tooltip content="我的" placement="bottom" effect="light">
            <el-button @click="goMyPage" type="text" style="font-size: 32px; color: #8c939d"><i class="el-icon-user-solid"></i></el-button>
        </el-tooltip>
        <el-tooltip content="做题去" placement="bottom" effect="light">
            <el-button @click="goLeetcode" type="text" style="font-size: 32px; color: #8c939d"><i class="el-icon-s-promotion"></i></el-button>
        </el-tooltip>
    </div>
    <div class="ranking-container">
        <el-row>
            <el-col :sm="12" :xs="24">
                <div class="ranking-card">
                    <div class="ranking-title">小白屋</div>
                    <div v-for="(item, index) in normalList" :key="index" class="ranking-item" :class="{my: item.slug === me.leetcode_slug}">
                        <el-row>
                            <el-col :span="6">
                                <img :src="item.avatar" style="max-width: 40px; max-height: 40px; border-radius: 20px"
                                     alt="">
                                <p style="font-size: 12px;">@{{ item.name }}</p>
                            </el-col>
                            <el-col :span="18">
                                <el-progress
                                    style="line-height: 56px"
                                    :stroke-width="26"
                                    :text-inside="true"
                                    :percentage="item.count / 5 * 100"
                                    :format="format(item)"
                                    :status="getStatus(item)"
                                ></el-progress>
                            </el-col>
                        </el-row>

                    </div>
                </div>
            </el-col>
            <el-col :sm="12" :xs="24">
                <div class="ranking-card">
                    <div class="ranking-title">小黑屋</div>
                    <div v-for="(item, index) in abnormalList" :key="index" class="ranking-item">
                        <el-row>
                            <el-col :span="6">
                                <img :src="item.avatar" style="max-width: 40px; max-height: 40px; border-radius: 20px"
                                     alt="">
                                <p style="font-size: 12px;">@{{ item.name }}</p>
                            </el-col>
                            <el-col :span="18">
                                <el-progress
                                    style="line-height: 56px"
                                    :stroke-width="26"
                                    :text-inside="true"
                                    :percentage="item.count / 5 * 100"
                                    :format="format(item)"
                                    :status="getStatus(item, 10)"
                                ></el-progress>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            </el-col>
        </el-row>
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
                normalList: [],
                abnormalList: [],
                me: {}
            }
        },
        methods: {
            goMyPage() {
                window.location.href = '/my';
            },
            goLeetcode() {
                window.open('https://leetcode-cn.com/problemset/all/');
            },
            format(item) {
                return _ => item.count + '道题目';
            },
            getStatus(item, target = 5) {
                if (item.count <= target * 0.4) {
                    return 'exception'
                }
                if (item.count <= target * 0.8) {
                    return 'warning'
                }
                if (item.count <= target * 1) {
                    return 'success'
                }
                return 'primary'
            },
            getList() {
                let self = this;
                axios.get('/api/week/rank/normal')
                    .then(function (response) {
                        if (response.code === 200) {
                            self.normalList = response.data;
                            console.log(self.normalList);
                        } else {
                            self.$message(response.msg);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                axios.get('/api/week/rank/abnormal')
                    .then(function (response) {
                        if (response.code === 200) {
                            self.abnormalList = response.data;
                        } else {
                            self.$message(response.msg);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getMe() {
                let self = this;
                axios.get('/api/me')
                    .then(function (response) {
                        if (response.code === 200) {
                            self.me = response.data;
                        } else {
                            self.$message(response.msg);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        created() {
            this.getList();
            this.getMe();
        }
    })
</script>
</html>
