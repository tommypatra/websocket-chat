<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Grup;
use App\Models\GrupUser;

class GrupController extends Controller
{
    public function simpan(Request $request)
    {
        $grup = $request->post('grup');
        $users = $request->post('users');
        $grup_id = $request->post('id');
        $dataRespon = [];

        try {
            DB::beginTransaction();
            $user = User::where("id", auth()->user()->id)->first();

            $newGrup = Grup::create([
                "name" => $grup,
                "user_id" => auth()->user()->id,
            ]);

            //simpan data respon 
            $dataRespon = [
                [
                    "id" => $newGrup->id,
                    "name" => $grup,
                    "type" => "grup",
                ]
            ];

            //jadikan anggota grup yg create grup
            GrupUser::create([
                "user_id" => auth()->user()->id,
                "grup_id" => $newGrup->id,
            ]);

            //simpan user yang dipilih
            foreach ($users as $i => $dp) {
                GrupUser::create([
                    "user_id" => $dp,
                    "grup_id" => $newGrup->id,
                ]);
                broadcast(new \App\Events\NotifUserEvent($dp, $user, 'grupbaru', $dataRespon));
            }
            DB::commit();
            return response()->json(['status' => true, 'grup_id' => $newGrup->id, 'data' => $dataRespon]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
