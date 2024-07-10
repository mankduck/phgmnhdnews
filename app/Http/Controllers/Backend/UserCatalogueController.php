<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\UserCatalogueServiceInterface as UserCatalogueService;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
// use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
use App\Http\Requests\StoreUserCatalogueRequest;

class UserCatalogueController extends Controller
{
    protected $userCatalogueService;
    protected $userCatalogueRepository;
    protected $permissionRepository;

    public function __construct(
        UserCatalogueService $userCatalogueService,
        UserCatalogueRepository $userCatalogueRepository,
        // PermissionRepository $permissionRepository
    ) {
        $this->userCatalogueService = $userCatalogueService;
        $this->userCatalogueRepository = $userCatalogueRepository;
        // $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        // $this->authorize('modules', 'user.catalogue.index');
        $userCatalogues = $this->userCatalogueService->paginate($request);
        $config = $this->config();
        $config['seo'] = config('apps.messages.userCatalogue');
        $config['model'] = 'UserCatalogue';
        $config['modelCheck'] = 'User_Catalogue';
        return view(
            'backend.user.catalogue.index',
            compact(
                'config',
                'userCatalogues'
            )
        );
    }

    public function create()
    {
        // $this->authorize('modules', 'user.catalogue.create');
        $config['seo'] = config('apps.messages.userCatalogue');
        $config['method'] = 'create';
        return view(
            'backend.user.catalogue.create',
            compact(
                'config',
            )
        );
    }

    public function store(StoreUserCatalogueRequest $request)
    {
        if ($this->userCatalogueService->create($request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        // $this->authorize('modules', 'user.catalogue.edit');
        $userCatalogue = $this->userCatalogueRepository->findById($id);
        $config['seo'] = config('apps.messages.userCatalogue');
        $config['method'] = 'edit';
        return view(
            'backend.user.catalogue.create',
            compact(
                'config',
                'userCatalogue',
            )
        );
    }

    public function update($id, StoreUserCatalogueRequest $request)
    {
        if ($this->userCatalogueService->update($id, $request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        // $this->authorize('modules', 'user.catalogue.delete');
        $config['seo'] = config('apps.messages.userCatalogue');
        $userCatalogue = $this->userCatalogueRepository->findById($id);
        return view(
            'backend.user.catalogue.delete',
            compact(
                'userCatalogue',
                'config',
            )
        );
    }

    public function destroy($id)
    {
        if ($this->userCatalogueService->destroy($id)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    // public function permission()
    // {
    //     $this->authorize('modules', 'user.catalogue.permission');
    //     $userCatalogues = $this->userCatalogueRepository->all();

    //     $permissions = $this->permissionRepository->all();
    //     $config['seo'] = __('messages.userCatalogue');
    //     return view(
    //         'backend.user.catalogue.permission',
    //         compact(
    //             'userCatalogues',
    //             'permissions',
    //             'config',
    //         )
    //     );
    // }

    // public function updatePermission(Request $request)
    // {
    //     if ($this->userCatalogueService->setPermission($request)) {
    //         return redirect()->route('user.catalogue.index')->with('success', 'Cập nhật quyền thành công');
    //     }
    //     return redirect()->route('user.catalogue.index')->with('error', 'Có vấn đề xảy ra, Hãy thử lại');
    // }

    private function config()
    {
        return [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/library.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
        ];
    }

}
