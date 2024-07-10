<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected $postRepository;
    protected $postCatalogueRepository;
    protected $userRepository;
    public function __construct(
        PostRepository $postRepository,
        PostCatalogueRepository $postCatalogueRepository,
        UserRepository $userRepository
    ) {
        $this->postRepository = $postRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        // dd(Auth::id());
        $posts = $this->postRepository->findByCondition(...$this->agrumentPost());
        //lấy thông danh sách bài viết
        $users = $this->userRepository->findByCondition(...$this->agrumentUsers());
        //Lấy danh sách User, có role là cộng tác viên bài viết
        $perpage = 10;
        $postCatalogues = $this->postCatalogueRepository->paginate($perpage);
        //Lấy ra 10 danh mục
        return view('frontend.index', compact('posts', 'postCatalogues', 'users'));
    }


    public function compose(View $view)
    {
        $postCatalogues = $this->postCatalogueRepository->all();
        $listMenu = recursive($postCatalogues);         //Convert lại mảng danh mục lấy được
        $html = frontend_recursive_menu($listMenu);     //Tạo html từ mảng đã convert
        $view->with('html', $html);                     //Trả ra view
    }

    public function successMail(Request $request)
    {
        $data = [];
        $to = $request->input('email');     //Lấy địa chỉ Email người dùng nhập vào
        Mail::to($to)->send(new SendMail($data));       //Gọi đến send Mail
        return redirect()->route('home.index')->with('success', 'Gửi email nhận thông tin thành công!');
    }


    private function agrumentPost()
    {
        return [
            'condition' => [
                ['publish', '=', 2]
            ],
            'flag' => true,
            'orderBy' => ['id', 'DESC'],
            'param' => [
                'perpage' => 10
            ]
        ];
    }

    private function agrumentUsers()
    {
        return [
            'condition' => [
                ['user_catalogue_id', '=', 3]
            ],
            'flag' => true,
            'orderBy' => ['id', 'DESC'],
        ];
    }
}