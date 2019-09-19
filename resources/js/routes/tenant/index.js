import Layout from 'layouts/TenantLayout'

import My from './my'
import Posts from './posts'
import Requests from './requests'
import Settings from './settings'
import Dashboard from './dashboard'
import Marketplace from './marketplace'
import Cleanify from './cleanify'

export default [{
    path: '/',
    component: Layout,
    children: [My, Posts, Requests, Settings, Dashboard, Marketplace, Cleanify]
}]
