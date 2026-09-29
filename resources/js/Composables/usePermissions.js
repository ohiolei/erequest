import { usePage } from '@inertiajs/vue3';

/** Roles that only a super_admin may impersonate via Login As. */
export const RESTRICTED_LOGIN_AS_ROLES = ['DR', 'PAR'];

export function usePermissions() {
    const page = usePage();

    const hasRole = (roleName) => {
        const roles = page.props.auth?.roles ?? [];
        return roles.includes(roleName);
    };

    const isSuperAdmin = () => hasRole('super_admin');

    const hasPermission = (permName) => {
        if (isSuperAdmin()) {
            return true;
        }

        const permissions = page.props.auth?.permissions ?? [];
        return permissions.some((perm) => (typeof perm === 'string' ? perm : perm.name) === permName);
    };

    const hasAnyRole = (roleNames) => {
        const roles = page.props.auth?.roles ?? [];
        return roleNames.some((roleName) => roles.includes(roleName));
    };

    const isImpersonating = () => page.props.auth?.is_impersonating === true;

    const canPerformLoginAs = () => {
        // Nested Login As is not allowed — return to your account first.
        if (isImpersonating()) {
            return false;
        }

        return isSuperAdmin() || hasPermission('censis.users.update');
    };

    const isRestrictedLoginAsTarget = (user) => {
        return (user?.roles ?? []).some((role) => RESTRICTED_LOGIN_AS_ROLES.includes(role.name));
    };

    const canLoginAsUser = (user, { protectedEmail = 'dev@tasued.edu.ng' } = {}) => {
        if (!user || !canPerformLoginAs()) {
            return false;
        }

        const currentUserId = page.props.auth?.user?.id;
        if (currentUserId && user.id === currentUserId) {
            return false;
        }

        if (user.email === protectedEmail) {
            return false;
        }

        // Only super_admin may login as another super_admin.
        if (!isSuperAdmin() && (user.roles ?? []).some((role) => role.name === 'super_admin')) {
            return false;
        }

        if (!isSuperAdmin() && isRestrictedLoginAsTarget(user)) {
            return false;
        }

        return true;
    };

    return {
        hasPermission,
        hasRole,
        hasAnyRole,
        isSuperAdmin,
        isImpersonating,
        canPerformLoginAs,
        isRestrictedLoginAsTarget,
        canLoginAsUser,
    };
}
