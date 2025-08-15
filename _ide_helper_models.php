<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace Infra\EloquentModels{
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
	class AdminUser extends \Eloquent {}
}

namespace Infra\EloquentModels{
/**
 * 
 *
 * @property int $id
 * @property string $drug_name
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Infra\EloquentModels\MedicationHistory> $medicationHistories
 * @property-read int|null $medication_histories_count
 * @method static Builder<static>|Drug newModelQuery()
 * @method static Builder<static>|Drug newQuery()
 * @method static Builder<static>|Drug orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static Builder<static>|Drug query()
 * @method static Builder<static>|Drug sortSetting(string $orderBy, string $sortOrder, string $defaultKey = 'id')
 * @method static Builder<static>|Drug whereCreatedAt($value)
 * @method static Builder<static>|Drug whereDrugName($value)
 * @method static Builder<static>|Drug whereId($value)
 * @method static Builder<static>|Drug whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static Builder<static>|Drug whereUpdatedAt($value)
 * @method static Builder<static>|Drug whereUrl($value)
 * @mixin \Eloquent
 */
	class Drug extends \Eloquent {}
}

namespace Infra\EloquentModels{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $drug_id
 * @property string $amount 服薬量
 * @property string $medication_date
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @property-read \Infra\EloquentModels\Drug $drug
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory newQuery()
 * @method static Builder<static>|MedicationHistory orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereDrugId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereId($value)
 * @method static Builder<static>|MedicationHistory whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereMedicationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereUserId($value)
 * @mixin \Eloquent
 */
	class MedicationHistory extends \Eloquent {}
}

namespace Infra\EloquentModels{
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
	class UserDefinitiveRegisterToken extends \Eloquent {}
}

