<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Domain\Channel\ChannelId;
use Domain\Channel\DiscordChannelId;
use Infra\EloquentModels\Model as AppModel;

/**
 * 
 *
 * @property int $id
 * @property string $discord_channel_id
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel newQuery()
 * @method static Builder<static>|Channel orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereDiscordChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereId($value)
 * @method static Builder<static>|Channel whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Channel extends AppModel
{
    protected $table = 'channels';

    protected $guarded = [
        'id'
    ];

    public function toDomain(): \Domain\Channel\Channel
    {
        return new \Domain\Channel\Channel(
            new ChannelId((int)$this->id),
            new DiscordChannelId($this->discord_channel_id),
        );
    }
}
