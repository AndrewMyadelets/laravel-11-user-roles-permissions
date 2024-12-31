import { usePage } from '@inertiajs/vue3';

export const usePermissions = () => {
    const userPermissions = usePage().props.auth.permissions;

    const hasPermission = (permission) => {
        if (typeof userPermissions === 'object') {
            return Object.values(userPermissions).includes(permission);
        } else {
            return false;
        }
    };

    const hasAnyPermission = (permissions) => {
        return permissions.some((permission) =>
            Object.values(userPermissions).includes(permission)
        );
    };

    const buildPermissionTree = (permissions) => {
        const permissionMap = new Map();
        const tree = [];

        permissions.forEach((permission) => {
            permissionMap.set(permission.id, { ...permission, children: [] });
        });

        permissions.forEach((permission) => {
            if (permission.parent_id) {
                const parentPermission = permissionMap.get(permission.parent_id);
                if (parentPermission) {
                    permissionMap.set(permission.id, {
                        ...permissionMap.get(permission.id),
                        parent: parentPermission
                    });
                    parentPermission.children.push(permissionMap.get(permission.id));
                }
            } else {
                tree.push(permissionMap.get(permission.id));
            }
        });

        return tree;
    };

    return {
        hasPermission,
        hasAnyPermission,
        buildPermissionTree
    };
};
