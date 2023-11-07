<?php

namespace pyTonicis\Seat\SeatCorpMiningTax\Http\Controllers;

use pyTonicis\Seat\SeatCorpMiningTax\Services\SettingService;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class CorpMiningStatistics extends Controller
{
    public function __construct()
    {
        $this->settingService = new SettingService();
    }

    public function getHome()
    {
        $act_m = (date('m', time()));
        $act_y = (date('Y', time()));
        $total_units = 0;
        $total_volume = 0;
        $total_price = 0;
        $total_tax = 0;
        $minings = DB::table('corp_mining_tax')
            ->select('*')
            ->get();
        $moon_mining = DB::table('corporation_industry_mining_observer_data')
            ->selectRaw('sum(quantity) as quantity, DATE_FORMAT(updated_at, "%Y-%m") as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get();
        foreach ($minings as $mining) {
            $total_units += $mining->quantity;
            $total_volume += $mining->volume;
            $total_price += $mining->price;
            $total_tax += $mining->tax;
        }
        DB::statement("SET SQL_MODE=''");
        $top_ten_miners = DB::table('corp_mining_tax as t')
            ->select('t.main_character_id', 't.quantity', 't.volume', 't.price', 'c.name')
            ->join('character_infos as c', 't.main_character_id', '=', 'c.character_id')
            ->groupBy('t.main_character_id')
            ->orderBy('t.volume', 'desc')
            ->limit(5)
            ->get();
        DB::statement("SET SQL_MODE=''");
        $top_ten_miners_last = DB::table('corp_mining_tax as t')
            ->select('t.main_character_id', 't.quantity', 't.volume', 't.price', 'c.name')
            ->join('character_infos as c', 't.main_character_id', '=', 'c.character_id')
            ->groupBy('t.main_character_id')
            ->orderBy('t.volume', 'desc')
            ->where('month', '=', $act_m - 1)
            ->where('year', '=', $act_y)
            ->limit(5)
            ->get();
        $total_members = DB::table('corp_mining_tax')
            ->select('main_character_id')
            ->groupBy('main_character_id')
            ->get();
        DB::statement("SET SQL_MODE=''");
        $events = DB::table('corp_mining_tax_event_minings')
            ->selectRaw('sum(refined_price) as price')
            ->first();
        $total_event_price = $events->price;
        return view('corpminingtax::corpminingstatistics', [
            'total_quantity' => $total_units,
            'total_volume' => $total_volume,
            'total_price' => $total_price,
            'total_tax' => $total_tax,
            'total_members' => $total_members,
            'top_ten_miners' => $top_ten_miners,
            'top_ten_miners_last' => $top_ten_miners_last,
            'total_event_price' => $total_event_price,
            'moon_mining' => $moon_mining,
        ]);
    }
}