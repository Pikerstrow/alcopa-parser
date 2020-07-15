<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'admin')->firstOrFail();

        $restricted_for_admin = [
            'edit_auctions',
            'add_auctions',
            'delete_auctions',
            'edit_auction_cars',
            'add_auction_cars',
            'delete_auction_cars',
            'browse_menus',
            'read_menus',
            'edit_menus',
            'add_menus',
            'delete_menus',
            'browse_roles',
            'read_roles',
            'edit_roles',
            'add_roles',
            'delete_roles',
            'browse_users',
            'read_users',
            'edit_users',
            'add_users',
            'delete_users',
            'browse_settings',
            'read_settings',
            'edit_settings',
            'add_settings',
            'delete_settings',
            'browse_hooks',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
        ];

        $permissions = Permission::whereNotIn('key', $restricted_for_admin)->get();

        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );
    }
}
