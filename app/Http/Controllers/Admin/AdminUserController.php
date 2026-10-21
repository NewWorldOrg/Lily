<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as AppController;
use App\Http\Requests\Admin\AdminUsers\CreateAdminUserRequest;
use App\Http\Requests\Admin\AdminUsers\UpdateAdminUserRequest;
use App\Services\AdminUserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Infra\EloquentModels\AdminUser;

class AdminUserController extends AppController
{
    public function __construct(private readonly AdminUserService $adminUserService)
    {
        parent::__construct();
    }

    /**
     * Index of users.
     *
     * @return View
     */
    public function index(): View
    {
        $adminUsers = $this->adminUserService->getAdminUsers();
        return view('admin_users.index', compact('adminUsers'));
    }

    /**
     * Form to create user.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin_users.create');
    }

    /**
     * Store a user.
     *
     * @param CreateAdminUserRequest $request
     * @return RedirectResponse
     */
    public function store(CreateAdminUserRequest $request): RedirectResponse
    {
        $this->adminUserService->createUser(
            $request->getUserId(),
            $request->getAdminUserRawPassword(),
            $request->getName(),
            $request->getRole(),
            $request->getStatus(),
        );
        return redirect(route('admin.admin_users.index'))->with('success', 'ユーザーを保存しました');
    }

    /**
     * Form to update the user.
     *
     * @param AdminUser $adminUser
     * @return View
     */
    public function edit(AdminUser $adminUser): View
    {
        return view('admin_users.edit', compact('adminUser'));
    }

    /**
     * Update the user.
     *
     * @param AdminUser $adminUser
     * @param UpdateAdminUserRequest $request
     * @return RedirectResponse
     */
    public function update(AdminUser $adminUser, UpdateAdminUserRequest $request): RedirectResponse
    {
        $this->adminUserService->updateUser(
            $adminUser->toDomain()->getId(),
            $request->getUserId(),
            $request->getRawPassword(),
            $request->getName(),
            $request->getRole(),
            $request->getStatus(),
        );
        return redirect(route('admin.admin_users.index'))->with(['success' => 'ユーザーを編集しました']);
    }

    /**
     * Delete the user.
     *
     * @param AdminUser $adminUser
     * @return RedirectResponse
     */
    public function destroy(AdminUser $adminUser): RedirectResponse
    {
        $this->adminUserService->deleteUser($adminUser->toDomain()->getId());
        return redirect(route('admin.admin_users.index'))->with(['success' => 'ユーザーを削除しました']);
    }
}
