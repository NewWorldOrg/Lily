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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages newQuery()
 * @method static Builder<static>|Messages orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereId($value)
 * @method static Builder<static>|Messages whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereDiscordMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Messages extends AppModel
{
    protected $table = 'messages';

    protected $guarded = [
        'id'
    ];
}
