<?php

namespace Database\Seeders;

use Courage\CoString;
use Domain\AdminUser\AdminUserRole;
use Domain\AdminUser\AdminUserStatus;
use Domain\Common\CreatedAt;
use Domain\Common\UpdatedAt;
use Domain\User\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_users')->insert([
            'user_id' => 'takada_yuki',
            'password' => Hash::make('hogehoge'),
            'name' => '高田憂希',
            'role' => AdminUserRole::ROLE_SYSTEM,
            'status' => AdminUserStatus::STATUS_VALID,
            'created_at' => CreatedAt::now()->getSqlTimeStamp(),
            'updated_at' => UpdatedAt::now()->getSqlTimeStamp(),
        ]);
    }
}
