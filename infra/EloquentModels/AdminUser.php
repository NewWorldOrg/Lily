<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Domain\AdminUser\AdminId;
use Domain\AdminUser\AdminUserId;
use Domain\AdminUser\AdminUserName;
use Domain\AdminUser\AdminUserRole;
use Domain\AdminUser\AdminUserStatus;
use Domain\Common\HashedPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Domain\AdminUser\AdminUser as AdminUserDomain;

/**
 * 
 *
 * @property int $id id
 * @property string $user_id 管理者ID
 * @property string $password パスワード
 * @property string $name 名前
 * @property int $role ロール
 * @property bool $status ステータス
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereUserId($value)
 * @mixin \Eloquent
 */
class AdminUser extends Authenticatable
{
    use Notifiable;

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function toDomain(): AdminUserDomain
    {
        return new AdminUserDomain(
            new AdminId($this->id),
            new AdminUserId($this->user_id),
            new HashedPassword($this->password),
            new AdminUserName($this->name),
            AdminUserRole::tryFrom((int)$this->role),
            AdminUserStatus::tryFrom((int)$this->status)
        );
    }
}
