<template>
    <el-container>
        <el-header :height="header.height">
            <div class="container">
                <el-menu ref="menu"
                         mode="horizontal"
                         :router="true"
                         :background-color="header.menu.background"
                         v-on:scroll="handleScroll">
                    <el-menu-item index="1" class="logo">
                        <img src="~img/logo3.png" />
                    </el-menu-item>
                    <el-menu-item index="2" class="spacer"></el-menu-item>
                    <el-menu-item index="3" route="login">
                        <el-button type="primary" icon="ti-user" round>Login</el-button>
                    </el-menu-item>
                </el-menu>
            </div>
            <div class="container">
                <h1>Lorem. Ipsum. Dolor sit amet.</h1>
                <p>Aliquam erat volutpat. Sed a erat sed magna fringilla viverra ut in lectus. Praesent lobortis ex commodo imperdiet tincidunt. Suspendisse eget congue enim. Aenean vel eros sit amet arcu iaculis porttitor.</p>
                <el-input placeholder="Where do you want to live?"></el-input>
            </div>
        </el-header>
        <el-main>
            <section class="services container">
                <el-row type="flex" :gutter="16">
                    <el-col :span="8">
                        <el-card shadow="never">
                            <i class="ti-money"></i>
                            <div>
                                <h5>No additional costs</h5>
                                <p>We do not charge any additional fees for administration, adjustments and do not charge any cancellation fees.</p>
                            </div>
                        </el-card>
                    </el-col>
                    <el-col :span="8">
                        <el-card shadow="never">
                            <i class="ti-thumb-up"></i>
                            <div>
                                <h5>Consistent bonus</h5>
                                <p>The premium for your <b>rental deposit</b> is manageable and remains the same in the following years.</p>
                            </div>
                        </el-card>
                    </el-col>
                    <el-col :span="8">
                        <el-card shadow="never">
                            <i class="ti-lock"></i>
                            <div>
                                <h5>Simple, safe and modern</h5>
                                <p>Our solution is an innovative alternative to the bank deposit, which protects the relocation budget.</p>
                            </div>
                        </el-card>
                    </el-col>
                </el-row>
            </section>
            <section class="container about">
                <div class="heading">
                    How it works
                    <small>Simple and fast way to rent</small>
                </div>
                <el-row :gutter="20" type="flex" align="middle" justify="center">
                    <el-col :span="8">
                        <img src="~img/5c3a1e02ad2ae.png" />
                    </el-col>
                    <el-col :span="12">
                        <h3>Lorem <b>ipsum</b></h3>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pretium eros a tellus faucibus interdum. Donec eget neque nulla. Donec at auctor dui, in facilisis dolor. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi eget dapibus libero, vel aliquet ligula. Aenean molestie malesuada euismod. Morbi tincidunt sapien eget iaculis porta. Nulla efficitur diam ultrices orci lobortis elementum vitae vitae mauris. Morbi efficitur elit quis purus sodales hendrerit.
                        </p>
                    </el-col>
                </el-row>
                <el-row :gutter="20" type="flex" align="middle" justify="center">
                    <el-col :span="12">
                        <h3><b>Lorem</b> ipsum</h3>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pretium eros a tellus faucibus interdum. Donec eget neque nulla. Donec at auctor dui, in facilisis dolor. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi eget dapibus libero, vel aliquet ligula. Aenean molestie malesuada euismod. Morbi tincidunt sapien eget iaculis porta. Nulla efficitur diam ultrices orci lobortis elementum vitae vitae mauris. Morbi efficitur elit quis purus sodales hendrerit.
                        </p>
                    </el-col>
                    <el-col :span="8">
                        <img src="~img/5c3a1c1ef099f.png" />
                    </el-col>
                </el-row>
                <el-row :gutter="20" type="flex" align="middle" justify="center">
                    <el-col :span="8">
                        <img src="~img/5c3a1bc5406a4.png" />
                    </el-col>
                    <el-col :span="12">
                        <h3>Lorem <b>ipsum</b></h3>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pretium eros a tellus faucibus interdum. Donec eget neque nulla. Donec at auctor dui, in facilisis dolor. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi eget dapibus libero, vel aliquet ligula. Aenean molestie malesuada euismod. Morbi tincidunt sapien eget iaculis porta. Nulla efficitur diam ultrices orci lobortis elementum vitae vitae mauris. Morbi efficitur elit quis purus sodales hendrerit.
                        </p>
                    </el-col>
                </el-row>
            </section>
            <section class="get-started">
                <div class="container">
                    <div class="heading inverted">
                        Morbi a libero facilisis
                    </div>
                    <p>
                        Aenean euismod metus eros, vitae ultricies nunc sollicitudin ac. Nullam pharetra sed libero nec auctor. Vivamus vel sapien sollicitudin augue pretium elementum. Maecenas dui ligula, tincidunt nec rutrum vitae, pharetra at est. Cras a nulla nisl. Suspendisse potenti. Integer auctor rutrum fermentum. Nulla tristique varius arcu, ut elementum ipsum aliquam in. Nulla a orci id velit pretium faucibus et a ipsum. Sed ornare egestas felis sed porta. Nam ut malesuada elit.
                    </p>
                    <el-button round>Get started</el-button>
                </div>
            </section>
            <section class="container benefits">
                <div class="heading">
                    More benefits
                    <small>
                        Integer tincidunt, arcu id molestie sodales, sem lacus iaculis odio, vel laoreet nisl tortor eu elit.
                    </small>
                </div>
                <el-row :gutter="20" type="flex">
                    <el-col :span="6">
                        <i class="ti-heart"></i>
                        <h5>Attractive insurance</h5>
                        <p>Our premium is cheap and manageable, there will be no administration fees.</p>
                    </el-col>
                    <el-col :span="6">
                        <i class="ti-pencil"></i>
                        <h5>Simply terminable</h5>
                        <p>The Mietkautionspolice will be resolved upon return of the rental property directly from the landlord.</p>
                    </el-col>
                    <el-col :span="6">
                        <i class="ti-desktop"></i>
                        <h5>Online degree</h5>
                        <p>The security deposit insurance can be completed quickly and easily online.</p>
                    </el-col>
                    <el-col :span="6">
                        <i class="ti-back-left"></i>
                        <h5>Refund warranty</h5>
                        <p>You will be refunded the full premium if, for example, your landlord does not accept the cashless rental deposit.</p>
                    </el-col>
                </el-row>
            </section>
            <section class="customers">
                <div class="heading">
                    We have spread some happiness
                    <small>Over 2,500 customers</small>
                </div>
            </section>
            <section class="container testimonials">
                <div class="heading">
                    Our clients say
                    <small class="inverted">Don't just take our word</small>
                </div>
                <el-carousel :autoplay="false" :interval="4000" type="card" height="300px">
                    <el-carousel-item v-for="item in 5" :key="item">
                        <img src="http://i.pravatar.cc/300" />
                        <h5>
                            Kate Swome
                            <small>Designer</small>
                        </h5>
                        <p>
                            Aenean molestie purus velit, vel placerat orci ultrices non. Integer tincidunt, arcu id molestie sodales, sem lacus iaculis odio, vel laoreet nisl tortor eu elit. Integer nec ex nibh. Integer ut semper mauris. Aliquam vitae mi nisl. Proin volutpat justo sed tellus tempor, eget hendrerit arcu iaculis.
                        </p>
                    </el-carousel-item>
                </el-carousel>
            </section>
            <section class="faq">
                <div class="heading">
                    FAQs
                    <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
                </div>
                <div class="container">
                    <el-row :gutter="20" type="flex" align="middle">
                        <el-col :span="12">
                            <el-collapse accordion value="1">
                                <el-collapse-item title="Consistency" name="1">
                                    <div>Consistent with real life: in line with the process and logic of real life, and comply with languages and habits that the users are used to;</div>
                                    <div>Consistent within interface: all elements should be consistent, such as: design style, icons and texts, position of elements, etc.</div>
                                </el-collapse-item>
                                <el-collapse-item title="Feedback" name="2">
                                    <div>Operation feedback: enable the users to clearly perceive their operations by style updates and interactive effects;</div>
                                    <div>Visual feedback: reflect current state by updating or rearranging elements of the page.</div>
                                </el-collapse-item>
                                <el-collapse-item title="Efficiency" name="3">
                                    <div>Simplify the process: keep operating process simple and intuitive;</div>
                                    <div>Definite and clear: enunciate your intentions clearly so that the users can quickly understand and make decisions;</div>
                                    <div>Easy to identify: the interface should be straightforward, which helps the users to identify and frees them from memorizing and recalling.</div>
                                </el-collapse-item>
                                <el-collapse-item title="Controllability" name="4">
                                    <div>Decision making: giving advices about operations is acceptable, but do not make decisions for the users;</div>
                                    <div>Controlled consequences: users should be granted the freedom to operate, including canceling, aborting or terminating current operation.</div>
                                </el-collapse-item>
                            </el-collapse>
                        </el-col>
                    </el-row>
                </div>
            </section>
            <section class="more">
                <div class="container">
                    <el-row :gutter="20" type="flex" align="middle">
                        <el-col :span="12">
                            <img src="~img/buildings.png" />
                        </el-col>
                        <el-col :span="12">
                            <h3>Lorem <b>ipsum</b></h3>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pretium eros a tellus faucibus interdum. Donec eget neque nulla. Donec at auctor dui, in facilisis dolor.
                            </p>
                            <el-button type="primary" icon="ti-hand-point-right">Get started now!</el-button>
                        </el-col>
                    </el-row>
                </div>
            </section>
        </el-main>
        <el-footer>
            <img src="~img/logo3.png" />
            <ul>
                <li>
                    <i class="ti-location-pin"></i>
                    Freiburgstrasse 574, 3172 Niederwangen
                </li>
                <li>
                    <i class="ti-mobile"></i>
                    0800 461 461
                </li>
                <li>
                    <i class="ti-email"></i>
                    info@gocaution.ch
                </li>
            </ul>
            <ul>
                <li>
                    <el-button icon="ti-facebook" class="facebook" circle></el-button>
                </li>
                <li>
                    <el-button icon="ti-twitter" class="twitter" circle></el-button>
                </li>
                <li>
                    <el-button icon="ti-google" class="google-plus" circle></el-button>
                </li>
            </ul>
            <h6>Copyright © 2019 myHome. All rights reserved.</h6>
        </el-footer>
    </el-container>
</template>
<script>
    export default {
        name: 'Landing',
        data() {
            return {
                header: {
                    height: '800px',
                    menu: {
                        background: 'transparent'
                    }
                }
            }
        },
        methods: {
            handleScroll() {
               if (window.scrollY > 0) {
                   this.header.menu.background = '#fff';
                   this.$refs.menu.$el.classList.add('container');
                   this.$refs.menu.$el.parentElement.classList.add('scrolling');
               } else {
                   this.header.menu.background = 'transparent';
                   this.$refs.menu.$el.classList.remove('container');
                   this.$refs.menu.$el.parentElement.classList.remove('scrolling');
               }
            }
        },
        created () {
            window.addEventListener('scroll', this.handleScroll);
        },
        destroyed () {
            window.removeEventListener('scroll', this.handleScroll);
        }
    }
</script>
<style lang="scss" scoped>
    .container {
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        }
        @media (min-width: 768px) {
        .container {
            width: 750px;
        }
        }
        @media (min-width: 992px) {
        .container {
            width: 970px;
        }
        }
        @media (min-width: 1200px) {
        .container {
            width: 1170px;
        }
    }
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .h1,
    .h2,
    .h3,
    .h4,
    .h5,
    .h6 {
    font-family: inherit;
    font-weight: 500;
    line-height: 1.1;
    color: inherit;
    }
    h1 small,
    h2 small,
    h3 small,
    h4 small,
    h5 small,
    h6 small,
    .h1 small,
    .h2 small,
    .h3 small,
    .h4 small,
    .h5 small,
    .h6 small,
    h1 .small,
    h2 .small,
    h3 .small,
    h4 .small,
    h5 .small,
    h6 .small,
    .h1 .small,
    .h2 .small,
    .h3 .small,
    .h4 .small,
    .h5 .small,
    .h6 .small {
    font-weight: 400;
    line-height: 1;
    color: #777777;
    }
    h1,
    .h1,
    h2,
    .h2,
    h3,
    .h3 {
    margin-top: 20px;
    margin-bottom: 10px;
    }
    h1 small,
    .h1 small,
    h2 small,
    .h2 small,
    h3 small,
    .h3 small,
    h1 .small,
    .h1 .small,
    h2 .small,
    .h2 .small,
    h3 .small,
    .h3 .small {
    font-size: 65%;
    }
    h4,
    .h4,
    h5,
    .h5,
    h6,
    .h6 {
    margin-top: 10px;
    margin-bottom: 10px;
    }
    h4 small,
    .h4 small,
    h5 small,
    .h5 small,
    h6 small,
    .h6 small,
    h4 .small,
    .h4 .small,
    h5 .small,
    .h5 .small,
    h6 .small,
    .h6 .small {
    font-size: 75%;
    }
    h1,
    .h1 {
    font-size: 36px;
    }
    h2,
    .h2 {
    font-size: 30px;
    }
    h3,
    .h3 {
    font-size: 24px;
    }
    h4,
    .h4 {
    font-size: 18px;
    }
    h5,
    .h5 {
    font-size: 14px;
    }
    h6,
    .h6 {
    font-size: 12px;
    }
    p {
    margin: 0 0 10px;
    }

    .el-container {
        .el-header {
            padding: 0;
            position: relative;
            background: linear-gradient(to bottom, #F9F9F9, #fff);
            display: flex;
            flex-direction: column;
            .el-menu {
                padding: 0;
                border-bottom-style: none;
                display: flex;
                align-items: center;
                .el-menu-item {
                    background: none !important;
                    :global(i) {
                        color: #fff;
                        margin-right: .5em;
                    }
                    &.spacer {
                        flex: 1;
                    }
                    &.logo {
                        padding: .5em;
                        img {
                            height: 100%;
                            vertical-align: baseline;
                        }
                    }
                }
            }
            > .container {
                &:first-of-type {
                    padding: 0;
                    position: fixed;
                    z-index: 9;
                    left: 0;
                    right: 0;
                    margin: 24px auto;
                    transition: all .32s cubic-bezier(.51,.92,.24,1.15);
                    &.scrolling {
                        background-color: #fff;
                        width: 100%;
                        min-width: 100%;
                        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                        margin-top: 0;
                        display: flex;
                        align-items: center;
                    }
                }
                &:last-of-type {
                    height: 100%;
                    margin-top: -4em;
                    text-align: center;
                    h1 {
                        font-weight: bold;
                    }
                    .el-input {
                        width: 512px;
                        margin: 1em;
                        :global(.el-input__inner) {
                            height: 56px;
                            line-height: 56px;
                            border-style: none;
                            font-size: 1.2em;
                            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                        }
                    }
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-direction: column;
                }
            }
            &:before {
                content: '';
                min-width: 1440px;
                position: absolute;
                top: 0;
                left: 0;
                background-image: url("~img/50575138_23843117704460378_1605133040630104064_n.png");
                background-size: contain;
                background-repeat-y: no-repeat;
                background-position: center bottom;
                filter: opacity(.24);
                box-shadow: inset 0 0 8em 8em #fff;
                width: 100%;
                height: 100%;
            }
        }
        .el-main {
            padding: 0;
            section {
                position: relative;
                .heading {
                    font-size: 2em;
                    font-weight: bold;
                    position: relative;
                    text-align: center;
                    display: flex;
                    flex-direction: column;
                    margin-bottom: 1em;
                    line-height: 1.32;
                    small {
                        display: block;
                        font-size: 56%;
                        color: darken(#fff, 40%);
                        &.inverted {
                            order: -1;
                        }
                    }
                    &.inverted {
                        &, small {
                            color: #fff;
                        }
                        &:after {
                            background-color: #fff;
                        }
                    }
                    &:after {
                        content: '';
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        width: 80px;
                        height: 3px;
                        margin: -.5em auto;
                        background-color: darken(#fff, 16%);
                    }
                }
                &:not(:last-of-type) {
                    margin-bottom: 4em;
                }
                &.services {
                    .el-row {
                        margin: 4px;
                        .el-col {
                            &:not(:last-child) {
                                margin-right: -2px;
                            }
                            $colors: #DB2828 #2185D0 #A333C8;
                            @each $color in $colors {
                                $i: index($colors, $color);
                                &:nth-child(#{$i}) .el-card i {
                                    background-color: $color;
                                }
                            }
                            .el-card {
                                position: relative;
                                height: 100%;
                                box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
                                display: flex;
                                align-items: center;
                                :global(.el-card__body) {
                                    padding: 1em;
                                    h5 {
                                        font-weight: bold;
                                        font-size: 1.24em;
                                    }
                                    i {
                                        font-size: 2em;
                                        display: block;
                                        color: #fff;
                                        border-radius: 50%;
                                        box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
                                        min-width: 64px;
                                        display: flex;
                                        align-items: center;
                                        width: 64px;
                                        height: 64px;
                                        margin-right: .5em;
                                    }
                                    p {
                                        color: darken(#fff, 40%);
                                    }
                                    display: flex;
                                    align-items: center;
                                }
                            }
                        }
                    }
                }
                &.about {
                    .el-row {
                        position: relative;
                        &:nth-child(even) {
                            .el-col:last-of-type {
                                text-align: right;
                                &:before {
                                    content: '';
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    background-image:url("~img/shape2.png");
                                    background-position: top right;
                                    background-repeat: no-repeat;
                                    filter: opacity(.1) sepia();
                                    width: 100%;
                                    height: 100%;
                                }
                            }
                        }
                        &:nth-child(odd) {
                            .el-col:first-of-type {
                                &:before {
                                    content: '';
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    background-image:url("~img/shape1.png");
                                    background-position: top left;
                                    background-repeat: no-repeat;
                                    filter: opacity(.1) sepia();
                                    width: 100%;
                                    height: 100%;
                                }
                            }
                        }
                        .el-col {
                            h3 {
                                font-size: 2em;
                                b {
                                    color: #6AC06F;
                                }
                            }
                            p {
                                color: lighten(#000, 56%);
                            }
                            img {
                                width: 100%;
                            }
                        }
                    }
                }
                &.get-started {
                    color: #fff;
                    padding: 8em;
                    position: relative;
                    background: linear-gradient(to right, #6AC06F, lighten(#6AC06F, 24%));
                    text-align: center;
                    .container {
                        position: relative;
                        z-index: 1;
                    }
                    &:before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        z-index: 0;
                        background-image: url("~img/5c3a33943fe05.png");
                        background-size: cover;
                        background-position: center;
                        background-attachment: fixed;
                        filter: drop-shadow(0 8px 8px) opacity(.12);
                        width: 100%;
                        height: 100%;
                    }
                }
                &.benefits {
                    .el-row {
                        flex-wrap: wrap;
                        .el-col {
                            $colors: #AC0404 #05726C #C68E03 #780644;
                            @each $color in $colors {
                                $i: index($colors, $color);
                                &:nth-child(#{$i}) i {
                                    color: $color;
                                }
                            }
                            position: relative;
                            text-align: center;
                            i {
                                background-color: #fff;
                                border-radius: 50%;
                                font-size: 2em;
                                margin: 0 auto;
                                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                width: 64px;
                                height: 64px;
                            }
                            h5 {
                                margin: 1em;
                                font-size: 1.24em;
                                font-weight: bold;
                            }
                            &:before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: 0;
                                background-image: url('~img/5c3a67c2381e2.png');
                                background-position: center;
                                opacity: .1;
                                transform: rotate(90deg);
                                z-index: -1;
                                width: 100%;
                                height: 100%;
                            }
                        }
                    }
                }
                &.customers {
                    height: 576px;
                    background-image: url("~img/customers.png");
                    background-repeat: no-repeat;
                    background-size: contain;
                    background-position: center 8em;
                    box-shadow: inset 0 0 4em 2em #fff;
                }
                &.testimonials {
                    .el-carousel {
                        :global(.el-carousel__container) {
                            margin: 1em 0;
                            .el-carousel__item {
                                background-color: #fff;
                                border-radius: 6px;
                                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                                padding: 1em;
                                text-align: center;
                                h5 {
                                    font-size: 1.4em;
                                    small {
                                        display: block;
                                        font-size: 72%;
                                        color: darken(#fff, 40%);
                                    }
                                }
                                img {
                                    width: 80px;
                                    border: 4px transparentize(darken(#fff, 20%), .5) solid;
                                    border-radius: 50%;
                                }
                                &:not(.is-active) {
                                    filter: blur(4px);
                                }
                            }
                        }
                    }
                }
                &.faq {
                    background-color: darken(#fff, 1%);
                    padding: 2em;
                    margin-bottom: 0;
                    .container {
                        position: relative;
                        &:before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: 0;
                            background-image: url("~img/5c3b4e60a600a.png");
                            background-size: 56em;
                            background-position: right center;
                            background-repeat: no-repeat;
                            filter: opacity(.5);
                            width: 100%;
                            height: 100%;
                        }
                        :global(.el-collapse) {
                            border-style: none;
                            :global(.el-collapse-item) {
                                border-radius: 4px;
                                overflow: hidden;
                                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                                &:not(:last-of-type) {
                                    margin-bottom: 8px;
                                }
                                :global(.el-collapse-item__header) {
                                    padding: .5em;
                                    font-size: 1.2em;
                                    font-weight: bold;
                                    height: 40px;
                                    line-height: 40px;
                                }
                                :global(.el-collapse-item__content) {
                                    padding: 1em;
                                    font-size: 14px;
                                }
                            }
                        }
                    }
                }
                &.more {
                    background: linear-gradient(to right, #6AC06F lighten(#6AC06F, 24%));
                    color: #fff;
                    padding: 2em;
                    &:before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        background-image: url("~img/5c3b59fab215e.png");
                        filter: opacity(.1) invert(100%);
                        width: 100%;
                        height: 100%;
                    }
                    .el-row {
                        .el-col {
                            &:first-of-type img {
                                width: 100%;
                                filter: invert(100%) hue-rotate(156deg);
                            }
                            &:last-of-type .el-button :global(i) {
                                margin-right: .5em;
                            }
                        }
                    }
                }

            }
        }
        .el-footer {
            border-top: 1px darken(#fff, 6%) solid;
            position: relative;
            height: auto !important;
            padding: 2em;
            text-align: center;
            background: linear-gradient(to top, darken(#fff, 5%), transparent);
            img {
                margin: 1em;
                width: 192px;
            }
            ul {
                list-style: none;
                padding: 0;
                margin: 0;
                li {
                    padding: .25em;
                }
                &:last-of-type {
                    display: inline-flex;
                    li {
                        .el-button {
                            color: #fff;
                            border: 1px transparent solid;
                            &.facebook {
                                background-color: #2D4BA2;
                                border-color: darken(#2D4BA2, 4%);
                            }
                            &.twitter {
                                background-color: #1C9CEA;
                                border-color: darken(#1C9CEA, 4%);
                            }
                            &.google-plus {
                                background-color: #D64A39;
                                border-color: darken(#D64A39, 4%);
                            }
                        }
                    }
                }
            }
            h6 {
                font-size: 1em;
                margin: 1em;
                font-weight: bold;
            }
        }
    }
</style>
