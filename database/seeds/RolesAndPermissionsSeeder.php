<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'read-dashboard']);

        Permission::create(['name' => 'create-transklinik']);
        Permission::create(['name' => 'read-transklinik']);
        Permission::create(['name' => 'update-transklinik']);
        Permission::create(['name' => 'delete-transklinik']);

        Permission::create(['name' => 'create-rekam-medis']);
        Permission::create(['name' => 'read-rekam-medis']);
        Permission::create(['name' => 'update-rekam-medis']);
        Permission::create(['name' => 'delete-rekam-medis']);

        Permission::create(['name' => 'create-klinik']);
        Permission::create(['name' => 'read-klinik']);
        Permission::create(['name' => 'update-klinik']);
        Permission::create(['name' => 'delete-klinik']);

        Permission::create(['name' => 'create-pasien']);
        Permission::create(['name' => 'read-pasien']);
        Permission::create(['name' => 'update-pasien']);
        Permission::create(['name' => 'delete-pasien']);

        Permission::create(['name' => 'create-dokter']);
        Permission::create(['name' => 'read-dokter']);
        Permission::create(['name' => 'update-dokter']);
        Permission::create(['name' => 'delete-dokter']);

        Permission::create(['name' => 'create-operator']);
        Permission::create(['name' => 'read-operator']);
        Permission::create(['name' => 'update-operator']);
        Permission::create(['name' => 'delete-operator']);

        Permission::create(['name' => 'create-tarif']);
        Permission::create(['name' => 'read-tarif']);
        Permission::create(['name' => 'update-tarif']);
        Permission::create(['name' => 'delete-tarif']);

        // create roles and assign created permissions

        $role = Role::create(['name' => 'super_admin']);
        $role->syncPermissions([
            'read-dashboard',
            'read-transklinik',
            'create-klinik',
            'read-klinik',
            'update-klinik',
            'delete-klinik',
            'read-pasien',
            'create-dokter',
            'read-dokter',
            'update-dokter',
            'delete-dokter',
            'create-operator',
            'read-operator',
            'update-operator',
            'delete-operator',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);

        $role = Role::create(['name' => 'admin_klinik']);
        $role->syncPermissions([
            'read-dashboard',
            'create-transklinik',
            'read-transklinik',
            'update-transklinik',
            'delete-transklinik',
            'create-klinik',
            'read-klinik',
            'update-klinik',
            'delete-klinik',
            'read-pasien',
            'update-pasien',
            'delete-pasien',
            'create-dokter',
            'read-dokter',
            'update-dokter',
            'delete-dokter',
            'create-operator',
            'read-operator',
            'update-operator',
            'delete-operator',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);

        $role = Role::create(['name' => 'dokter_praktek']);
        $role->syncPermissions([
            'read-dashboard',
            'create-transklinik',
            'read-transklinik',
            'update-transklinik',
            'delete-transklinik',
            'create-rekam-medis',
            'read-rekam-medis',
            'update-rekam-medis',
            'delete-rekam-medis',
            'create-klinik',
            'read-klinik',
            'update-klinik',
            'delete-klinik',
            'create-pasien',
            'read-pasien',
            'update-pasien',
            'delete-pasien',
            'create-operator',
            'read-operator',
            'update-operator',
            'delete-operator',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);

        $role = Role::create(['name' => 'dokter_klinik']);
        $role->syncPermissions([
            'read-dashboard',
            'create-rekam-medis',
            'read-rekam-medis',
            'update-rekam-medis',
            'delete-rekam-medis',
            'read-pasien',
            'update-pasien',
            'delete-pasien'
        ]);

        $role = Role::create(['name' => 'operator']);
        $role->syncPermissions([
            'read-dashboard',
            'create-transklinik',
            'read-transklinik',
            'update-transklinik',
            'delete-transklinik',
            'create-pasien',
            'read-pasien',
            'update-pasien',
            'delete-pasien',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);
    }
}
