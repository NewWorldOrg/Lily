<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\AdminUsers;

use App\Http\Requests\Request as AppRequest;
use Domain\AdminUser\AdminUserId;
use Domain\AdminUser\AdminUserName;
use Domain\AdminUser\AdminUserRole;
use Domain\AdminUser\AdminUserStatus;
use Domain\Common\RawPassword;

class UpdateAdminUserRequest extends AppRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|max:255',
            'password' => 'nullable|min:8',
            'password_confirm' => 'required_with:password|same:password',
            'name' => 'required',
            'role' => 'required|int',
            'status' => 'required|int'
        ];
    }


    /**
     * messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'ログインIDは必須です',
            'password.required' => 'パスワードは必須です',
            'password.min' => 'パスワードは8文字以上の文字列を入力してください',
            'password_confirm.required_with' => 'パスワードを変更する場合はパスワード(確認)は必須です',
            'name.required' => '名前は必須です',
            'role.required' => 'ロールは必須です',
            'status.required' => 'ステータスは必須です',
            'password_confirm.same' => 'パスワードが一致しません',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'ログインID',
            'password' => 'パスワード',
            'password_confirm' => 'パスワード(確認)',
            'name' => '名前',
            'role' => 'ロール',
            'status' => 'ステータス'
        ];
    }

    public function getUserId(): AdminUserId
    {
        return new AdminUserId($this->input('user_id'));
    }

    public function getRawPassword(): RawPassword
    {
        return new RawPassword($this->input('password'));
    }

    public function getName(): AdminUserName
    {
        return new AdminUserName($this->input('name'));
    }

    public function getRole(): AdminUserRole
    {
        return AdminUserRole::tryFrom((int)$this->input('role'));
    }

    public function getStatus(): AdminUserStatus
    {
        return AdminUserStatus::tryFrom((int)$this->input('status'));
    }
}
