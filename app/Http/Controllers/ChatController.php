<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function form(Request $request)
    {
        $user = Auth::user();
        return view('chat', ['user' => $user]);
    }

    // public function send(Request $request)
    // {
    //     $pesan = $request['pesan'];
    //     broadcast(new \App\Events\ChatEvent($pesan, auth()->user()->id));
    // }

    public function notifikasi($user_id)
    {
        $ruang_obrolans = DB::table('ruang_obrolans as ro')
            ->select('ro.id')
            ->leftJoin('user_ruangs as ur', 'ur.ruang_obrolan_id', '=', 'ro.id')
            ->where('ur.user_id', $user_id)
            ->get();
        $ruang_obrolan_ids = $ruang_obrolans->pluck('id')->toArray();

        $result = DB::table('ruang_obrolans as ro')
            ->select('o.user_id', 'o.ruang_obrolan_id', DB::raw('count(o.id) as jumlah'))
            ->leftJoin('obrolans as o', function ($join) use ($user_id) {
                $join->on('o.ruang_obrolan_id', '=', 'ro.id')
                    ->on('o.user_id', '!=', DB::raw($user_id));
            })
            ->where('o.is_baca', 0)
            ->whereIn('o.ruang_obrolan_id', $ruang_obrolan_ids)
            ->groupBy('o.user_id', 'o.ruang_obrolan_id')
            ->get();

        $filteredResults = [];
        foreach ($result as $row) {
            $filteredResults[] = [
                'user_id' => $row->user_id,
                'ruang_obrolan_id' => $row->ruang_obrolan_id,
                'jumlah' => $row->jumlah,
                'type' => 'user',
            ];
        }
        return $filteredResults;
    }
}
