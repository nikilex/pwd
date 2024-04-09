<?php

namespace App\Http\Controllers\Admin;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('id')) {
            $board = Board::where('id', $request->get('id'))->first();
        } else {
            $board = Board::latest()->first();
        }

        return view('admin.board', [
            'title' => 'Главная',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => route('main')
            ],
            'page' => 'resources/views/admin/board.blade.php',
            'controller' => 'app/Http/Controllers/Admin/DashboardController.php',
            'board' => $board
        ]);
    }
}
