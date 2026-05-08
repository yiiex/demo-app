import {ref, computed} from 'vue'
import {router, usePage} from '@inertiajs/vue3'
import axios from 'axios'

interface User {
    id: number
    name: string
    email: string
    fullName: string | null
}

const logoutDialog = ref(false);
const logoutDialogLoading = ref(false);

export function useAuth() {
    const page = usePage();
    // @ts-ignore
    const user = computed(() => page.props.auth?.user as User | null);
    // @ts-ignore
    const isAuthenticated = computed(() => page.props.auth?.isAuthenticated ?? false);
    const defaultAvatar = '/images/avatar.webp';
    // @ts-ignore
    const avatar = computed(() => user.value?.avatar || defaultAvatar);

    const logout = async () => {
        logoutDialogLoading.value = true
        try {
            const response = await axios.post('/user/logout')
            if (response.data.success) {
                logoutDialog.value = false
                router.get('/')
            }
        } catch (error) {
            console.error('Logout failed:', error)
        } finally {
            logoutDialogLoading.value = false
        }
    };

    return {
        user,
        avatar,
        isAuthenticated,
        logout,
        logoutDialog,
        logoutDialogLoading,
        defaultAvatar,
    }
}
