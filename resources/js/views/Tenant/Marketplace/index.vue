<template>
    <div class="tenant-marketplace">
        <heading icon="icon-basket" title="Marketplace">
            <div slot="description" class="description">Start selling things you don't need anymore.</div>
            <el-button type="primary" icon="el-icon-circle-plus-outline" round @click="dialogs.addProductForm.visible = true">Add product</el-button>
            <el-dialog top="0" width="100%" :class="['el-dialog-add-product-form-wrapper', {'is-md': el.is.md}]" custom-class="el-dialog-add-product-form" title="Add product" :visible.sync="dialogs.addProductForm.visible" :fullscreen="dialogs.addProductForm.fullscreen" append-to-body>
                <add-product-form ref="add-product-form" />
                <template class="dialog-footer" slot="footer">
                    <el-button icon="el-icon-close" round @click="dialogs.addProductForm.visible = false">
                        Cancel
                    </el-button>
                    <el-button type="primary" icon="el-icon-check" round @click="addProduct">
                        Save
                    </el-button>
                </template>
            </el-dialog>
        </heading>
        <el-card class="products" v-loading="loading">
            <div class="title">
                Latest products added
                <div class="content">
                    <el-input size="small" prefix-icon="el-icon-search" v-model="searchModel" v-debounce:240="handleSearch" placeholder="Search..." />
                    <el-popover popper-class="products-filter" placement="bottom-end" trigger="click" :width="192">
                        <el-button size="small" slot="reference" icon="el-icon-sort" round>Filters</el-button>
                        <filters ref="filters" layout="row" :data.sync="filters.data" :schema="filters.schema" @changed="onFiltersChanged" />
                        <el-button type="primary" size="mini" icon="el-icon-sort-up" @click="resetFilters">Reset filters</el-button>
                    </el-popover>
                </div>
            </div>
            <placeholder :src="require('img/5ca7dde590fa1.png')" :size="512" v-if="!loading && !products.data.length">
                Unfortunately, no one is selling what you are looking for...
                <small slot="secondary">Do not worry, definitely there will be someone selling soon the product you want, so you may come and check again a bit later!</small>
            </placeholder>
            <!-- <p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lobortis urna non justo hendrerit mattis. Vivamus tristique nisi lorem, ac egestas orci varius sit amet. Morbi venenatis lacinia libero, ac imperdiet est volutpat quis. Nulla luctus, dolor eget porttitor elementum, nisl nunc imperdiet nulla, vitae volutpat dolor ante non nisi. Proin id nibh sit amet ante molestie eleifend eu eu orci. In hac habitasse platea dictumst. Nunc nec velit commodo dolor lobortis lobortis venenatis at arcu. Sed aliquam mi eget massa vehicula, nec fermentum justo efficitur. Nam convallis dui et tortor vulputate pulvinar. Duis pharetra diam vitae sapien condimentum fringilla. Nam at bibendum metus, vel rutrum tellus. Cras rhoncus pulvinar dapibus. Morbi pretium interdum augue quis convallis.

In at mi eu nisi sollicitudin iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Praesent eget mauris ac dui ultricies condimentum quis et dui. Cras dui massa, posuere ut laoreet quis, hendrerit sed diam. Donec diam ex, ultricies ac nibh sit amet, pretium commodo urna. Quisque sit amet leo ligula. Phasellus ut gravida tellus, sed commodo leo. In hac habitasse platea dictumst. Fusce sapien nibh, scelerisque eu volutpat et, mollis quis tortor. Donec finibus tincidunt vehicula. Praesent maximus, quam eu tristique rhoncus, sapien ante tempor magna, in condimentum turpis eros sed felis.

Mauris ut nisi dignissim, porta lectus ac, blandit quam. Maecenas consequat massa urna, eget posuere augue aliquam sit amet. In hac habitasse platea dictumst. Aenean ut erat venenatis nisl malesuada varius. Donec at commodo sapien, vel feugiat nunc. Etiam finibus, purus nec pharetra sollicitudin, nulla justo sagittis lorem, ut ornare urna urna eu erat. Nulla laoreet condimentum nibh, a auctor quam imperdiet a. Aliquam lacinia malesuada ante vel vehicula. In a ligula quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ut mi pretium, luctus lectus sit amet, auctor ipsum. Mauris ante leo, mattis nec urna eu, pretium laoreet nisi. Duis venenatis sit amet velit ut ornare.

Aliquam erat volutpat. Pellentesque consectetur euismod elementum. Cras tempus metus et ipsum placerat, nec dictum mi aliquam. Ut molestie eu mi a interdum. In suscipit turpis eget libero imperdiet aliquam. Cras ornare feugiat neque lacinia varius. Integer vitae magna eget orci fringilla bibendum.

Praesent in sapien a tortor varius ultrices sed at nisl. Integer accumsan interdum quam in sodales. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam lacinia orci a dui interdum faucibus. Quisque aliquam massa nec luctus posuere. Donec rutrum nunc ornare consequat semper. In aliquam nisl sollicitudin, commodo massa ut, sollicitudin massa. Maecenas justo nisi, elementum ac mollis sodales, volutpat vitae lacus. Quisque imperdiet justo at egestas ornare. Nulla cursus ornare purus eget tempor. Donec eleifend suscipit massa, sed vehicula nulla fermentum id. Maecenas sagittis lacus ipsum, quis rhoncus velit eleifend non. Quisque et est elit. Donec ac sapien odio. Cras molestie libero erat, nec rhoncus arcu congue sit amet.
            </p> -->
            <product-card v-for="product in products.data" :key="product.id" :data="product" :lazy-image-scroll-container="$el" @click="openProduct(product)" />
            <el-dialog top="0" width="100%" :class="['el-dialog-product-details-wrapper', {'is-md': el.is.md}]" custom-class="el-dialog-product-details" :visible.sync="dialogs.productDetails.visible" :before-close="onProductDetailsDialogClose" :fullscreen="dialogs.productDetails.fullscreen" append-to-body>
                <product-details :data="openedProduct" v-if="openedProduct" />
            </el-dialog>
            <el-pagination :layout="pagination.layout" :current-page="pagination.current" :page-size="pagination.size" :page-sizes="pagination.sizes" :total="products.total" @size-change="onSizeChange" @current-change="onCurrentPageChange" background hide-on-single-page />
        </el-card>
    </div>
</template>

<script>
    import Filters from 'components/Filters'
    import Placeholder from 'components/Placeholder'
    import Heading from 'components/Heading'
    import StickyHeading from 'components/StickyHeading'
    import {displayError, displaySuccess} from 'helpers/messages'
    import ProductCard from 'components/tenant/MarketplaceProductCard'
    import AddProductForm from 'components/tenant/MarketplaceAddProductForm'
    import ProductDetails from 'components/tenant/MarketplaceProductDetails'
    import {ResponsiveMixin} from 'vue-responsive-components'

    const DEFAULT_PAGINATION_LAYOUT = 'total, sizes, prev, pager, next, jumper'
    const MINIMAL_PAGINATION_LAYOUT = 'prev, pager, next'

    export default {
        mixins: [
            ResponsiveMixin
        ],
        components: {
            Heading,
            Filters,
            ProductCard,
            Placeholder,
            StickyHeading,
            ProductDetails,
            AddProductForm
        },
        data () {
            return {
                loading: false,
                searchModel: undefined,
                openedProduct: null,
                products: {
                    data: []
                },
                dialogs: {
                    productDetails: {
                        visible: false,
                        fullscreen: false
                    },
                    addProductForm: {
                        visible: false,
                        fullscreen: false
                    }
                },
                pagination: {
                    current: 1,
                    size: 15,
                    sizes: [15, 25, 50, 100],
                    layout: DEFAULT_PAGINATION_LAYOUT
                },
                filters: {
                    schema: [{
                        type: 'el-switch',
                        title: 'My offerings',
                        name: 'user_id',
                        props: {
                            activeValue: this.$store.getters.loggedInUser.id.toString(),
                            inactiveValue: null
                        }
                    }, {
                        type: 'el-select',
                        title: 'Type',
                        name: 'type',
                        props: {
                            clearable: true,
                            size: 'mini'
                        },
                        children: [{
                            type: 'el-option',
                            props: {
                                label: 'All',
                                value: null
                            }
                        }].concat(Object.entries(this.$constants.products.type).map(([value, label]) => ({
                            type: 'el-option',
                            props: {label, value}
                        })))
                    }],
                    data: {
                        user_id: null,
                        type: null
                    }
                }
            }
        },
        methods: {
            async fetch(params = {
                page: this.pagination.current,
                per_page: this.pagination.size,
                search: this.searchModel
            }) {
                if (this.loading && this.products.data.length) {
                    return
                }

                this.loading = true

                this.pagination.current = +params.page
                this.pagination.size = +params.per_page

                if ('search' in params && !params.searchModel) {
                    delete params.searchModel
                }

                this.$router.replace({query: params, name: this.$route.name})

                try {
                    const {data} = await this.$store.dispatch('products2/get', {
                        sortedBy: 'desc',
                        orderBy: 'created_at',
                        ...params
                    })

                    this.products = data

                    this.$el.scrollTop = 0
                } catch (error) {
                    displayError(error)
                } finally {
                    this.loading = false
                }
            },
            async handleSearch (value) {
                const {page, per_page, search, ...rest} = this.$route.query

                await this.fetch({page, per_page, search: value, ...rest})
            },
            async onFiltersChanged (filters) {
                await this.fetch({
                    page: 1,
                    per_page: this.pagination.size,
                    ...filters
                })
            },
            async onSizeChange (per_page) {
                await this.fetch({page: 1, per_page})
            },
            async onCurrentPageChange (page) {
                await this.fetch({page, per_page: this.pagination.size})
            },
            resetFilters () {
                this.$refs.filters.reset()
            },
            addProduct () {
                this.$watch(() => this.$refs['add-product-form'].loading, state => {
                    this.$nextTick(async () => {
                        this.$refs['add-product-form'].$el.classList.remove('el-loading-parent--relative')

                        if (!state) {
                            this.dialogs.addProductForm.visible = false

                            await this.fetch()
                        }
                    })
                })

                this.$refs['add-product-form'].submit()
            },
            openProduct (product) {
                this.openedProduct = product
                this.dialogs.productDetails.visible = true
            },
            onProductDetailsDialogClose (done) {
                this.openedProduct = null

                done()
            }
        },
        computed: {
            breakpoints () {
                return {
                    sm: el => {

                    },
                    md: el => {
                        if (el.width <= 735) {
                            this.dialogs.addProductForm.fullscreen = true
                            this.pagination.layout = MINIMAL_PAGINATION_LAYOUT

                            return true
                        } else if (el.width <= 848) {
                            this.dialogs.productDetails.fullscreen = true
                            this.dialogs.addProductForm.fullscreen = false
                            this.pagination.layout = MINIMAL_PAGINATION_LAYOUT

                            return true
                        } else if (el.width <= 880) {
                            this.pagination.layout = MINIMAL_PAGINATION_LAYOUT
                            this.dialogs.addProductForm.fullscreen = false

                            return true
                        } else {
                            this.dialogs.productDetails.fullscreen = false
                            this.dialogs.addProductForm.fullscreen = false
                            this.pagination.layout = DEFAULT_PAGINATION_LAYOUT

                            return false
                        }
                    },
                    lg: el => {

                    }
                }
            }
        },
        async mounted () {
            const {page = this.pagination.current, per_page = this.pagination.size, search, ...rest} = this.$route.query

            await this.fetch({page, per_page, search, ...rest})
        },
        created () {
            const {search, ...rest} = this.$route.query

            if (search) {
                this.searchModel = search
            }

            Object.entries(rest).forEach(([param, value]) => {
                if (this.filters.data.hasOwnProperty(param)) {
                    this.filters.data[param] = value
                }
            })
        }
    }
</script>

<style lang="scss">
    .el-dialog__wrapper.el-dialog-add-product-form-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;

        .el-dialog-add-product-form {
            z-index: 1;
            margin: 0;
            overflow: hidden;
            max-width: 750px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;

            .el-dialog__body {
                height: 100%;

                .add-product-form {
                    .el-input__inner,
                    .el-textarea__inner {
                        background-color: transparentize(#fff, .44);
                    }
                }

                &:before {
                    content: '';
                    top: 0;
                    left: 0;
                    z-index: -1;
                    width: 100%;
                    height: 100%;
                    opacity: .16;
                    border-radius: 6px;
                    position: absolute;
                    background-size: 64em;
                    pointer-events: none;
                    background-repeat: no-repeat;
                    background-position: -4em -12em;
                    background-image: url('~img/5ca7ec589fc67.png');
                }
            }
        }
    }

    .el-dialog__wrapper.el-dialog-product-details-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;

        .el-dialog-product-details {
            margin: 0;
            overflow: hidden;
            max-width: 1024px;
            border-radius: 6px;

            .el-dialog__header {
                padding: 0;

                .el-dialog__headerbtn {
                    z-index: 3;
                }
            }

            .el-dialog__body {
                padding: 0;

                .product-details {
                    will-change: display;
                }
            }
        }

        &.is-md .el-dialog-product-details {
            border-radius: 0;

            .el-dialog__body {
                height: 100%;

                .product-details {
                    height: 100%;
                    display: flex;
                    flex-direction: column;

                    .media-gallery-carousel .el-carousel {
                        height: 336px;
                    }

                    .el-tabs {
                        height: 100%;
                        margin-top: -12px;
                        display: flex;
                        flex-direction: column;

                        .el-tabs__content {
                            height: 100%;
                            display: flex;
                            flex-direction: column;

                            #pane-overview,
                            #pane-comments {
                                height: 100%;
                                display: flex;
                                flex-direction: column;
                            }

                            #pane-overview {
                                .container {
                                    height: 100%;
                                    overflow: auto;
                                    padding: 12px 0;
                                    margin: -12px 0;
                                }
                            }

                            #pane-comments {
                                .chat {
                                    .add-comment {
                                        margin-bottom: 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    .el-popover.products-filter {
        .el-button {
            width: 100%;
            margin-top: 12px;
        }
    }
</style>

<style lang="scss" scoped>
    .tenant-marketplace {
        // height: 100% !important;
        // margin: -16px;
        // padding: 16px;
        // overflow-y: auto;
        // -webkit-overflow-scrolling: touch;
        // transform: translate3d(0,0,0);
        // padding: 16px;
        // margin: -16px;
        // overflow-y: auto;
        // will-change: transform;
        // height: 100% !important;
        // -webkit-overflow-scrolling: touch;
        // -webkit-backface-visibility: hidden;
        // -webkit-perspective: 1000;

        .heading {
            margin-bottom: 24px;

            .description {
                color: darken(#fff, 40%);
            }
        }

        .el-card.products {
            will-change:transform;
            -webkit-backface-visibility: hidden;
            -webkit-perspective: 1000;
            -webkit-overflow-scrolling: touch;

            :global(.el-card__body) {
                padding: 8px;
                min-height: 256px;
                display: grid;
                grid-gap: 8px;
                grid-template-columns: repeat(auto-fill, minmax(256px, 1fr));

                .title {
                    width: 100%;
                    padding: 8px;
                    display: flex;
                    font-size: 16px;
                    font-weight: bold;
                    align-items: center;
                    grid-column: 1 / -1;
                    box-sizing: border-box;
                    align-self: flex-start;

                    .content {
                        display: flex;
                        margin-left: auto;
                        align-items: center;

                        > * {
                            &.el-input {
                                :global(.el-input__inner) {
                                    border-radius: 20px;
                                }
                            }

                            &:not(:last-child) {
                                margin-right: 8px;
                            }
                        }
                    }
                }

                .placeholder {
                    font-size: 16px;
                    grid-column: 1 / -1;

                    small {
                        color: darken(#fff, 40%);
                    }
                }

                .product {
                    cursor: pointer;
                    align-self: flex-start;
                    transition: box-shadow .32s ease;
                    // will-change: box-shadow, transform;

                    &:hover {
                        box-shadow: 0 1px 3px transparentize(#000, .88),
                                    0 1px 2px transparentize(#000, .76);
                    }
                }
                .el-pagination {
                    width: 100%;
                    display: flex;
                    grid-column: 1 / -1;
                    justify-content: center;

                    :global(.el-pagination__sizes) {
                        flex: 1;
                    }
                }
            }
        }
    }
</style>