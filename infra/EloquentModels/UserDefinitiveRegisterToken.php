<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Domain\Common\ExpiredAt;
use Domain\Common\Token;
use Domain\User\Id as UserId;
use Domain\User\DefinitiveRegisterToken\DefinitiveRegisterToken;
use Infra\EloquentModels\Model as AppModel;

/**
 * 
 *
 * @property int $id id
 * @property int $user_id
 * @property string $token トークン
 * @property int $is_verify 認証したか
 * @property string $expired_at 有効期限
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken newQuery()
 * @method static Builder<static>|UserDefinitiveRegisterToken orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereIsVerify($value)
 * @method static Builder<static>|UserDefinitiveRegisterToken whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDefinitiveRegisterToken whereUserId($value)
 * @mixin \Eloquent
 */
class UserDefinitiveRegisterToken extends AppModel
{
    protected $table = 'user_definitive_register_tokens';

    protected $guarded = [
        'id',
    ];

    public function toDomain(): DefinitiveRegisterToken
    {
        return new DefinitiveRegisterToken(
          new UserId((int)$this->user_id),
            new Token($this->token),
            ExpiredAt::forStringTime((string)$this->expired_at),
        );
    }
}
