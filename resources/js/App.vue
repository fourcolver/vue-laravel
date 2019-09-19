<template>
  <!-- <div id="app" class="wrapper"> -->
    <router-view></router-view>
  <!-- </div> -->
</template>

<script>
    import {mapGetters, mapState, mapActions} from 'vuex';
    import {displayError} from 'helpers/messages';

    export default {
        mounted () {
            document.documentElement.setAttribute('style', `
                --primary-color: #6AC06F;
                --accent-color: #F7CA18;
                --danger-color: #cf000f;
                --text-color: #3f3f3f;
            `)
        }
    }
</script>


<style lang="scss">
    .custom-remote-select {
        .el-select__tags {
            padding-left: 20px;
        }

        .el-input__prefix {
            display: flex;
            align-items: center;
            margin-left: 5px;
        }

        .el-input--prefix .el-input__inner {
            padding-left: 35px;
            padding-right: 35px;
        }
    }

    .full-button {
        width: 100%;
    }
</style>

