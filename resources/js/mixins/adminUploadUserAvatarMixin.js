import {mapActions} from 'vuex';

export default {
    data() {
        return {
            avatar: ''
        }
    },
    methods: {
        ...mapActions(['uploadUserAvatar']),
        async uploadAvatar(userId) {
            await this.uploadUserAvatar({
                id: userId,
                image_upload: this.avatar
            });
        },
        cropped(e) {
            this.avatar = e;
        },
        async uploadAvatarIfNeeded(userId) {
            if (this.avatar.length) {
                return await this.uploadAvatar(userId);
            }
        }
    }
};
