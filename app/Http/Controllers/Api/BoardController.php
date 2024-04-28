<?php

namespace App\Http\Controllers\Api;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class BoardController
 */
class BoardController extends Controller
{
    public function getBoards()
    {
        $boards = Board::where('user_id', backpack_user()->id)->get();
        return response()->json($boards);
    }

    public function getColumns(Request $request)
    {
        $columns = Column::where([
            'board_id' => $request->id,
        ])
            ->orderBy('sort', 'ASC')
            ->with(['cards' => function ($q) {
                $q->orderBy('sort', 'ASC');
            }])
            ->get();

        return response()->json($columns);
    }

    public function getCard(Request $request)
    {
        $card = Card::where('id', $request->card_id)->first();
        return response()->json($card);
    }

    public function saveCardDescription(Request $request)
    {
        $card = Card::find($request->id);

        $card->update([
            'description' => $request->description,
        ]);

        return Card::find($card->id);
    }

    public function saveCardTitle(Request $request)
    {
        $card = Card::find($request->id);

        $card->update([
            'title' => $request->title,
        ]);

        return Card::find($card->id);
    }

    public function changeColumnTitle(Request $request)
    {
        return Column::updateOrCreate([
            'id' => $request->get('column')['id'] ?? null,
        ], [
            'title'    => $request->get('column')['title'],
            'board_id' => $request->get('board_id'),
        ]);
    }

    public function changeCardTitle(Request $request)
    {
        return Card::updateOrCreate([
            'id' => $request->get('card')['id'] ?? null,
        ], [
            'title'     => $request->get('card')['title'],
            'column_id' => $request->get('column_id'),
        ]);
    }

    public function transferCard(Request $request)
    {
        Card::where('id', $request->card_id)->update([
            'column_id' => $request->column_id,
            'sort'      => 9999
        ]);

        $card = Card::find($request->card_id);

        return $card;
    }

    public function updateSort(Request $request)
    {
        return Card::upsert($request->cards, ['id'], ['sort', 'column_id']);
    }

    public function updateColumnSort(Request $request)
    {
        return Column::upsert($request->columns, ['id'], ['sort']);
    }

    public function archiveCard(Request $request)
    {
        $card = Card::find($request->card_id);
        return $card->delete();
    }

    public function archiveColumn(Request $request)
    {
        $column = Column::find($request->column_id);
        return $column->delete();
    }

    public function getArchivedColumns(Request $request)
    {
        return Column::where('board_id', $request->board_id)->onlyTrashed()->get();
    }

    public function getArchivedCards(Request $request)
    {
        return Card::whereHas('column', function($q) use ($request) {
            $q->where('board_id', $request->board_id);
        })->onlyTrashed()->get();
    }

    public function unarchivedColumn(Request $request)
    {
        return Column::where('id', $request->column_id)->withTrashed()->update(['deleted_at' => NULL]);
    }

    public function unarchivedCard(Request $request)
    {
        return Card::where('id', $request->card_id)->withTrashed()->update(['deleted_at' => NULL]);
    }
}
