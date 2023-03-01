<?php

namespace pyTonicis\Seat\SeatCorpMiningTax\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class CorpMiningMoonMinings extends Controller
{
    private $MoonObservers;
    private $extractions = [];

    public function getCorpObservers(Request $request)
    {
        $data = DB::table('corporation_industry_mining_observers as o')
            ->select(
                'o.observer_id',
                's.name'
            )
            ->LeftJoin('universe_structures as s', 'o.observer_id', '=', 's.structure_id')
            ->where('o.corporation_id', '=', '98496411')
            ->orderBy('s.name', 'desc')
            ->get();
        return view('corpminingtax::corpmoonmining', ['data' => $data]);
    }

    public function getCorpMoonExtractions(int $id)
    {
        $data = DB::table('corporation_industry_mining_observer_data')
            ->select(
                'last_updated'
            )
            ->selectRAW("sum(quantity) as quantity")
            ->groupby('last_updated')
            ->where('observer_id', $id)
            ->get();
        return $data;
    }

    public function getCorpMoonMiningData(Request $request)
    {
        $minings = $this->getCorpMoonExtractions((int)$request->get('observer'));
        return view('corpminingtax::corpmoonmining', ['minings' => $minings]);
    }
}