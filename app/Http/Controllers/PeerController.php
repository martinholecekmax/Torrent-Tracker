<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Peer;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PeerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $peers = Peer::all();
        return response()->json($peers)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function announce(Request $request)
    {
        $this->validate($request, [
            'peer_id' => 'required|max:64',
            'port' => 'required|integer',
            'info_hash' => 'required|max:64',
            'event' => ['required', Rule::in(['started', 'stopped', 'completed'])],
            'downloaded' => 'required|integer',
            'uploaded' => 'required|integer',
            'left' => 'required|integer',
        ]);

        $peer = Peer::where('info_hash', $request->info_hash)->where('peer_id', $request->peer_id)->first();

        if ($peer == null) {
            $peer = new Peer();
        }

        $peer->peer_id = $request->peer_id;
        $peer->ip = $request->ip();
        $peer->port = $request->port;
        $peer->info_hash = $request->info_hash;
        $peer->event = $request->event;
        $peer->downloaded = $request->downloaded;
        $peer->uploaded = $request->uploaded;
        $peer->left = $request->left;
        $peer->save();

        $allPeers = Peer::where('info_hash', $request->info_hash)->get();
        $completed = 0;
        $incomplete = 0;

        foreach ($allPeers as $peer) {
            if ($peer->event == "downloaded") {
                $completed++;
            } else {
                $incomplete++;
            }
        }

        return response()->json([
            'peers' =>  $allPeers,
            'interval' => 1800,
            'completed' => $completed, 
            'incomplete' => $incomplete
        ])->header('Content-Type', 'application/json');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Peer::where('created_at', '<=', Carbon::now()->subMinutes(5)->toDateTimeString())->each(function ($item) {
            $item->delete();
        });
    }
}
