import {ref, computed} from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import axios from 'axios';
import {User} from '@js/types/user';


const logoutDialog = ref(false);
const logoutDialogLoading = ref(false);

export function useAuth() {
    const page = usePage();
    // @ts-ignore
    const user: ComputedRef<User | null> = computed(() => page.props.auth?.user as User | null);
    // @ts-ignore
    const isAuthenticated = computed(() => page.props.auth?.isAuthenticated ?? false);

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
        isAuthenticated,
        logout,
        logoutDialog,
        logoutDialogLoading,
    }
}
