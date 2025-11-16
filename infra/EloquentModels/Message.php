<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Infra\EloquentModels\Model as AppModel;

/**
 *
 *
 * @property int $id
 * @property int $channel_id
 * @property string $discord_message_id
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static Builder<static>|Message orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static Builder<static>|Message whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereDiscordMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends AppModel
{
    protected $table = 'messages';

    protected $guarded = [
        'id'
    ];
}
