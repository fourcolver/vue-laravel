<template>
    <el-container>
        <el-aside width="512px">
            <router-link to="/" class="logo">
                <img src="~img/logo3.png"/>
            </router-link>
            <router-view></router-view>
        </el-aside>
        <el-main>
            <div class="content">
                <h1>Lorem ipsum dolor sit amet</h1>
                <p>
                    Morbi quis ligula sed felis luctus convallis. Nunc viverra vestibulum nisi consequat dignissim. Morbi auctor vulputate metus eu ornare. Fusce nec aliquam arcu. Nullam vitae pretium neque, et laoreet nib
                </p>
                <el-row :gutter="24">
                    <el-col :span="24">
                        <i class="ti-shield" />
                        <div class="content">
                            <h3>Lorem ipsum dolor sit amet</h3>
                            <p>
                                Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis eu dolor ligula.
                            </p>
                        </div>                        
                    </el-col>
                    <el-col :span="24">
                        <i class="ti-pencil" />
                        <div class="content">
                            <h3>Lorem ipsum dolor</h3>
                            <p>
                                Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis eu dolor ligula.
                            </p>
                        </div> 
                    </el-col>
                    <el-col :span="24">
                        <i class="ti-comment-alt" />
                        <div class="content">
                            <h3>Lorem ipsum dolor sit</h3>
                            <p>
                                Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis eu dolor ligula.
                            </p>
                        </div> 
                    </el-col>
                </el-row>
            </div>            
        </el-main>
    </el-container>
</template>
<style lang="scss" scoped>
    .el-container {
        height: 100%;
        .el-aside {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3em;
            margin-top: 0;
            margin-right: 0;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 
                        0 6px 6px rgba(0, 0, 0, 0.23);
            .logo {
                margin: 1em auto;
                img {
                    width: 192px;
                }
            }
            &:before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                background-image: url("~img/5c3b59fab215e.png");
                filter: opacity(.024) sepia(100%);
                z-index: -1;
                width: 100%;
                height: 100%;
            }
        }
        .el-main {
            position: relative;
            padding: 8em;
            padding-right: 4em;
            display: flex;
            align-items: center;
            &:before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                background-image: url('~img/5c3b64bee128b.png');
                background-repeat: no-repeat;
                background-size: cover;
                z-index: -1;
                filter: opacity(.05);
                width: 100%;
                height: 100%;
            }
            &:after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                background-image: url('~img/5c3aaf1eed25e.png');
                background-repeat: no-repeat;
                background-size: 88em;
                background-position: left calc(100% + 7em);
                z-index: 1;
                opacity: .32;
                /*filter: drop-shadow(0 0 0) opacity(.8);*/
                width: 100%;
                height: 100%;
            }
            .content {
                position: relative;
                z-index: 2;
                h1 {
                    font-size: 3.5em;
                    color: #6AC06F;
                    margin-top: 0;
                    margin-bottom: .5em;
                }
                p {
                    font-size: 16px;
                    color: #525761;
                }
                .el-row {
                    margin-top: 4em;
                    margin-bottom: 4em;
                    .el-col {
                        &:not(:last-of-type) {
                            margin-bottom: 2em;
                        }
                        display: flex;
                        align-items: center;
                        i {
                            width: 72px;
                            height: 72px;
                            flex-shrink: 0;
                            display: block;
                            border-radius: .25em;
                            font-size: 2.4em;
                            text-align: center;
                            line-height: 72px;
                            color: #fff;
                            background-color: #6AC06F !important;
                            box-shadow: 0 10px 20px rgba(0,0,0,0.19),
                                        0 6px 6px rgba(0,0,0,0.23);
                            margin-right: .5em;
                        }
                        .content {
                            h3 {
                                font-size: 1.4em;
                                margin: 0;
                                margin-bottom: .32em;
                                color: #6AC06F;
                            }
                            p {

                                margin: 0;
                                font-size: 14px;
                                color: #525761;
                            }
                        }
                    }
                }
            }
        }
    }

</style>
