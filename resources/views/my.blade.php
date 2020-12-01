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
            position: relative;
        }

        .container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateY(-50%) translateX(-50%);
            max-width: 100vw;
            max-height: 100vh;
            width: 800px;
            color: #606266;
        }

        .my-card {
            color: #606266
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
            width: 108px;
            height: 108px;
            line-height: 108px;
            text-align: center;
        }

        .avatar {
            width: 108px;
            height: 108px;
            display: block;
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
        <el-tooltip content="做题去" placement="bottom" effect="light">
            <el-button @click="goLeetcode" type="text" style="font-size: 32px; color: #8c939d"><i class="el-icon-s-promotion"></i></el-button>
        </el-tooltip>
    </div>
    <div class="container">
        <el-card class="my-card">
            <el-row>
                <el-col :span="12" style="text-align: center">
                    <el-upload
                        class="avatar-uploader"
                        action="api/avatar"
                        :show-file-list="false"
                        :on-success="handleAvatarSuccess"
                        :before-upload="beforeAvatarUpload">
                        <img v-if="me.avatar" :src="me.avatar" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-col>
                <el-col :span="12">
                    <div v-if="editName">
                        <el-input style="width: 150px" size="small" v-model="me.name"></el-input>
                        <el-button @click="saveName" type="text" size="small" style="font-size: 16px"><i class="el-icon-check"></i></el-button>
                    </div>
                    <div v-if="!editName">
                        @{{ me.name }}
                        <el-button @click="editName=true" type="text" size="small" style="font-size: 16px"><i class="el-icon-edit-outline"></i></el-button>
                    </div>
                    <span style="color:#909399; font-size: 14px">@{{ me.leetcode_slug }}</span>
                    <el-button @click="logout" type="text" size="small" style="font-size: 16px">退出登录</el-button>
                </el-col>
            </el-row>
        </el-card>
        <el-card  class="my-card" style="margin-top: 15px">
            <h4>
                提交记录
                <el-button @click="syncSubmissions" type="text" size="small" style="font-size: 16px"><i class="el-icon-refresh"></i></el-button>
            </h4>

            <el-table border :data="tableData" style="width: 100%; margin-top: 15px">
                <el-table-column prop="submit_at" label="时间"></el-table-column>
                <el-table-column prop="question_title" label="题目">
                    <template slot-scope="scope">
                        <el-link type="primary" :href="buildQuestionLink(scope.row.question_name)" target="_blank">@{{ scope.row.question_title }}</el-link>
                    </template>
                </el-table-column>
                <el-table-column prop="language" label="语言"></el-table-column>
                <el-table-column prop="result" label="结果"></el-table-column>
            </el-table>
            <div style="padding: 15px 30px; display: flex; justify-content: center">
                <el-pagination
                    background
                    @current-change="getList"
                    :current-page="page"
                    :page-size="pageSize"
                    :total="total"
                    layout="prev, pager, next">
                </el-pagination>
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
            tableData: [],
            total: 0,
            me: {},
            page: 1,
            pageSize: 15,
            editName: false
        },
        methods: {
            goLeetcode() {
                window.open('https://leetcode-cn.com/problemset/all/');
            },
            buildQuestionLink(questionName) {
                return 'https://leetcode-cn.com/problems/' + questionName.replace(/\s/g, '-');
            },
            logout() {
                window.location.href = '/logout';
            },
            getList(page = this.page) {
                let self = this;
                this.page = page;
                axios.get('/api/leetcode/submissions?page=' + this.page)
                    .then(function (response) {
                        if (response.code === 200) {
                            self.tableData = response.data.list;
                            self.total = response.data.total;
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
            },
            saveName() {
                let self = this;
                axios.post('/api/name', {name: this.me.name})
                    .then(function (response) {
                        if (response.code === 200) {
                            self.me = response.data;
                            self.editName = false;
                        } else {
                            self.$message(response.msg);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
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
            handleAvatarSuccess(res, file) {
                if (res.code === 200) {
                    this.me.avatar = res.data.avatar;
                } else {
                    this.$message(res.msg);
                }
            },
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isPNG = file.type === 'image/png';
                const isGIF = file.type === 'image/gif';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG && !isPNG && !isGIF) {
                    this.$message.error('上传头像图片只能是 JPG/JPEG/PNG/GIF 格式!');
                    return false;
                }
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                    return false;
                }
                return true;
            }
        },
        created() {
            this.getMe();
            this.getList();
        }
    })
</script>
</html>
