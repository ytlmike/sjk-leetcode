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
        }

        .ranking-container {
            width: 900px;
            color: #606266;
            margin: 0 auto;
            position: relative;
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

        ranking-card .el-link--inner {
            font-size: 12px
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
        <div style="margin-left: 15px">
            <div style="margin-right: 15px; display: inline-block">
                <el-tooltip effect="light">
                    <div slot="content" style="font-size: 14px; color: #606266; line-height: 32px">
                        <b style="font-size: 18px">规则说明</b>
                        <p>每周做题结算，累计5分即为完成，未完成的同学掉入小黑屋，需在接下来的一周达成10分才能回到小白屋。</p>
                        <p>每题分值按难度区分：简单难度每题1分，中等2分，困难4分。</p>
                        <p>算法路远，贵在坚持。</p>
                    </div>
                    <i class="el-icon-question" style="color: #a1a8ab; font-size: 24px"></i>
                </el-tooltip>
            </div>
            <el-tooltip effect="light" content="同步">
                <el-button @click="syncSubmissions" type="text" size="small" style="font-size: 24px"><i class="el-icon-refresh"></i></el-button>
            </el-tooltip>
        </div>
        <el-row>
            <el-col :sm="12" :xs="24">
                <div class="ranking-card">
                    <div class="ranking-title">小白屋</div>
                    <div v-for="(item, index) in normalList" :key="index" class="ranking-item" :class="{my: item.slug === me.leetcode_slug}">
                        <el-row>
                            <el-col :span="6" style="text-align: center">
                                <el-link type="primary" :href="'https://leetcode-cn.com/u/' + item.slug" target="_blank" :underline="false">
                                    <img :src="item.avatar" style="max-width: 40px; max-height: 40px; border-radius: 20px"
                                         alt="">
                                </el-link>
                                <p style="font-size: 12px;">@{{ item.name }}</p>
                            </el-col>
                            <el-col :span="18">
                                <el-progress
                                    style="line-height: 56px"
                                    :stroke-width="26"
                                    :text-inside="true"
                                    :percentage="item.point / 5 * 100"
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
                                <el-link type="primary" :href="'https://leetcode-cn.com/u/' + item.slug" target="_blank" :underline="false">
                                    <img :src="item.avatar" style="max-width: 40px; max-height: 40px; border-radius: 20px" alt="">
                                </el-link>
                                <p style="font-size: 12px;">@{{ item.name }}</p>
                            </el-col>
                            <el-col :span="18">
                                <el-progress
                                    style="line-height: 56px"
                                    :stroke-width="26"
                                    :text-inside="true"
                                    :percentage="item.point / 5 * 100"
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
            syncSubmissions() {
                let self = this;
                axios.post('/api/leetcode/sync', {})
                    .then(function (response) {
                        if (response.code === 200) {
                            self.getList(1);
                        } else {
                            self.$message(response.msg);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            goMyPage() {
                window.location.href = '/my';
            },
            goLeetcode() {
                window.open('https://leetcode-cn.com/problemset/all/');
            },
            format(item) {
                return _ => item.point + '分';
            },
            getStatus(item, target = 5) {
                if (item.point <= target * 0.4) {
                    return 'exception'
                }
                if (item.point <= target * 0.8) {
                    return 'warning'
                }
                if (item.point <= target * 1) {
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
