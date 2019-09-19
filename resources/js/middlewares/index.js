import authMiddleware from './auth';
import adminMiddleware from './admin';
import tenantMiddleware from './tenant';
import guestMiddleware from './guest';

export const auth = authMiddleware;
export const tenant = tenantMiddleware;
export const admin = adminMiddleware;
export const guest = guestMiddleware;
