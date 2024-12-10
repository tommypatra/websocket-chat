<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use App\Models\User;
use App\Models\Kawan;
use App\Models\Obrolan;
use App\Models\UserRuang;
use App\Models\RuangObrolan;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;

class ApiController extends Controller
{
    public function getUsers(Request $request)
    {
        $users = User::orderBy("name", "asc");
        if ($request->has('cari')) {
            $searchTerm = $request->input('cari');
            $users->where('name', 'like', '%' . $searchTerm . '%');
        }
        return response()->json($users->get());
    }

    public function getOrang(Request $request)
    {
        $user_id = $request->post('user_id');
        $searchTerm = $request->post('cari');
        $data = DB::table('users as u')
            ->select('u.id', 'u.name', 'u.email')
            ->leftJoin('kawans as t1', function ($join) use ($user_id) {
                $join->on('u.id', '=', 't1.user_id2')
                    ->where('t1.user_id1', '=', $user_id);
            })
            ->leftJoin('kawans as t2', function ($join) use ($user_id) {
                $join->on('u.id', '=', 't2.user_id1')
                    ->where('t2.user_id2', '=', $user_id);
            })
            ->whereNull('t1.user_id2')
            ->whereNull('t2.user_id1')
            ->where('u.id', '!=', $user_id)
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->get();
        return response()->json($data);
    }

    public function getFriends(Request $request, $user_id)
    {
        $cari = $request->input('cariuser');

        $friends1 = DB::table('kawans as k')
            ->select('u.id', 'u.name', DB::raw("'user' as type"))
            ->join('users as u', 'k.user_id1', '=', 'u.id')
            ->where('k.user_id2', $user_id);
        if (!empty($cari)) {
            $friends1->where('u.name', 'LIKE', "%$cari%");
        }
        $friends1 = $friends1->get();

        $friends2 = DB::table('kawans as k')
            ->select('u.id', 'u.name', DB::raw("'user' as type"))
            ->join('users as u', 'k.user_id2', '=', 'u.id')
            ->where('k.user_id1', $user_id);
        if (!empty($cari)) {
            $friends2->where('u.name', 'LIKE', "%$cari%");
        }
        $friends2 = $friends2->get();


        $allFriends = $friends1->merge($friends2)->unique('id');
        return response()->json($allFriends);
    }

    public function getGroups(Request $request, $user_id)
    {
        $cari = $request->input('carigrup');
        $groups = Grup::select('id', 'name', DB::raw("'group' as type"))
            ->whereHas('grupUser.User', function ($query) use ($user_id) {
                $query->where('users.id', $user_id);
                if (!empty($cari)) {
                    $groups->where('grup.name', 'LIKE', "%$cari%");
                }
            })->get();
        return response()->json($groups);
    }

    public function getAll(Request $request, $user_id)
    {
        $cariuser = $request->input('cariuser');
        $carigrup = $request->input('carigrup');

        $friendsArray = [];
        $groupsArray = [];

        if (!$carigrup) {
            $friends = $this->getFriends($request, $user_id);
            $friendsArray = json_decode($friends->getContent(), true);
        }
        if (!$cariuser) {
            $groups = $this->getGroups($request, $user_id);
            $groupsArray = json_decode($groups->getContent(), true);
        }

        $allData = array_merge($friendsArray, $groupsArray);

        return response()->json($allData);
    }


    public function getMembersGroup($grup_id)
    {
        $members = Grup::with(['grupUser.User'])
            ->where('id', $grup_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($members);
    }

    public function daftarObrolan($ruang_obrolan_id)
    {
        $users = Obrolan::where('ruang_obrolan_id', $ruang_obrolan_id)
            ->with(['ruangObrolan', 'user'])
            ->orderBy('id', 'asc')
            ->get();
        return response()->json($users);
    }

    public function cariRuanganObrolan(Request $request)
    {
        // dd($request->all());
        $user1 = $request->post('user1');
        $user2 = $request->post('user2');

        try {
            DB::beginTransaction();
            $ruangObrolanId = UserRuang::select('ruang_obrolan_id')
                ->where('user_id', $user1)
                ->orWhere('user_id', $user2)
                ->groupBy('ruang_obrolan_id')
                ->havingRaw('COUNT(DISTINCT user_id) = 2')
                ->first();
            // return response()->json($ruangObrolanId);

            if ($ruangObrolanId) {
                DB::commit();
                return response()->json($ruangObrolanId);
            } else {
                $newRuangObrolan = RuangObrolan::create();
                UserRuang::create(['user_id' => $user1, 'ruang_obrolan_id' => $newRuangObrolan->id]);
                UserRuang::create(['user_id' => $user2, 'ruang_obrolan_id' => $newRuangObrolan->id]);
                DB::commit();
                return response()->json(['status' => true, 'ruang_obrolan_id' => $newRuangObrolan->id]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function notif($user_id)
    {
        $chatController = new ChatController();
        $notif = $chatController->notifikasi($user_id);
        return response()->json($notif);
    }

    public function saveFriends(Request $request)
    {
        // return $request->all();
        $data = $request->all();
        $user = User::where("id", $data['user_id1'])->first();

        $data['status'] = 1;
        try {
            DB::beginTransaction();
            $new = Kawan::create($data);
            DB::commit();
            broadcast(new \App\Events\NotifUserEvent($data['user_id2'], $user, 'kontakgrup'));
            return response()->json(['status' => true, 'teman_id' => $new->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }



    public function kirimObrolan(Request $request)
    {
        $user_id = $request->post('user_id');
        $pesan = $request->post('pesan');
        $user_id_obrolan = $request->post('user_id_obrolan');
        $ruang_obrolan_id = $request->post('ruang_obrolan_id');
        $user = User::where("id", $user_id)->first();
        try {
            DB::beginTransaction();
            $new = Obrolan::create($request->all());
            DB::commit();
            //triger event di chatevent di ruang obralan id yang sama
            broadcast(new \App\Events\NotifUserEvent($user_id_obrolan, $user, 'notif'));
            broadcast(new \App\Events\ChatEvent($pesan, $user, $ruang_obrolan_id));
            return response()->json(['status' => true, 'obrolan_id' => $new->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }


    public function updateStatusBaca(Request $request)
    {
        $user_id = $request->post('user_id');
        $ruang_obrolan_id = $request->post('ruang_obrolan_id');
        try {
            DB::beginTransaction();
            $update = Obrolan::where('user_id', '!=', $user_id)
                ->where('ruang_obrolan_id', $ruang_obrolan_id)
                ->where('is_baca', 0)
                ->update(['is_baca' => 1]);
            DB::commit();
            return response()->json(['status' => true, 'data' => $update]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
