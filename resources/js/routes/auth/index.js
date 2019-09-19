import {guest} from 'middlewares';
import Layout from 'layouts/AuthLayout';
import Login from 'views/Auth/Login';
import AutoLogin from 'views/Auth/AutoLogin';
import ForgotPassword from 'views/Auth/ForgotPassword';
import ResetPassword from 'views/Auth/ResetPassword';

export default [{
    path: '/',
    component: Layout,
    children: [{
        path: 'login',
        component: Login,
        name: 'login',
        meta: {
            title: 'Login',
            middleware: guest
        }
    }, {
        path: 'autologin',
        component: AutoLogin,
        name: 'autoLogin',
        meta: {
            title: 'Auto Login'
        }
    }, {
        path: 'forgot',
        component: ForgotPassword,
        name: 'forgot',
        meta: {
            title: 'Forgot Password',
            middleware: guest
        }
    }, {
        path: 'reset-password',
        component: ResetPassword,
        name: 'resetPassword',
        meta: {
            title: 'Reset Password',
            middleware: guest
        }
    }]
}];
