<?php

namespace App\Http\Controllers\Admin;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class BoardController
 */
class BoardController extends Controller
{
    public function index(Request $request)
    {
        $board = Board::find($request->id);

        return view('admin.board', [
            'title' => 'Главная',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => route('main')
            ],
            'page' => 'resources/views/admin/dashboard.blade.php',
            'controller' => 'app/Http/Controllers/Admin/DashboardController.php',
            'board' => $board
        ]);
    }
}
