<?php

namespace pyTonicis\Seat\SeatCorpMiningTax\Http\Controllers;

use pyTonicis\Seat\SeatCorpMiningTax\Helpers\CharacterHelper;
use pyTonicis\Seat\SeatCorpMiningTax\Services\ItemParser;
use pyTonicis\Seat\SeatCorpMiningTax\Services\Reprocessing;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use View;

class CorpMiningEvents extends Controller
{
    public function getHome()
    {
        $events = DB::table('corp_mining_tax_events')
            ->get();
        return view('corpminingtax::corpminingevents', ['events' => $events]);
    }

    public function eventCmd(Request $request)
    {
        redirect()->back();
    }

    public function createEvent(Request $request)
    {
        $update = DB::table('corp_mining_tax_events')
            ->insert(['event_name' => $request->get('event'), 'event_start' => $request->get('start'),
                'event_duration' => $request->get('duration'), 'event_status' => 1, 'event_tax' => $request->get('taxrate'), 'event_stop' => '1999-12-31']);
        $events = DB::table('corp_mining_tax_events')
            ->get();
        return view('corpminingtax::corpminingevents', ['events' => $events]);
    }

    public function addMining(Request $request)
    {
        $event_id = $request->get('event_id');
        $character = CharacterHelper::getCharacterName($request->get('character'));
        $parsed_items = ItemParser::parseItems($request->get('ore'));
        $refinedMaterials = [];
        $summary = 0;

        foreach($parsed_items as $key => $item) {

            $raw = Reprocessing::ReprocessOreByTypeId($item['typeID'], $item['quantity'], ((float)$request->get('modifier') / 100));
            foreach($raw as $n => $value) {
                $inv_type = InvType::where('typeId', '=', $n)->first();
                $price = Price::where('type_id', '=', $n)->first();
                if (!array_key_exists($inv_type->typeName, $refinedMaterials)) {
                    $refinedMaterials[$inv_type->typeName]['name'] = $inv_type->typeName;
                    $refinedMaterials[$inv_type->typeName]['typeID'] = $n;
                    $refinedMaterials[$inv_type->typeName]['quantity'] = $value;
                    $refinedMaterials[$inv_type->typeName]['price'] = $price->average_price;
                    $summary += (int)$price->average_price * (int)$value;
                } else {
                    $refinedMaterials[$inv_type->typeName]['quantity'] += $value;
                    $summary += (int)$price->average_price * (int)$value;
                }
            }
            DB::table('corp_mining_tax_event_minings')
                ->insertOrUpdate(['character_name' => $character, 'event_id' => $event_id, 'type_id' => $item['typeID'], 'quantity' => $item['quantity'], 'refined_price' => $summary]);
            $summary = 0;
        }
        return redirect()->back()->with('status', "Hallo".$character);
    }

    public function getDetails($eid = 0)
    {
        $event_minings = DB::table('corp_mining_tax_event_minings as em')
            ->select('em.*', 'it.typeName')
            ->join('invTypes as it', 'em.type_id', '=', 'it.typeID')
            ->where('em.event_id', $eid)
            ->orderBy('em.character_name')
            ->get();
        $characters = CharacterHelper::getMainCharacters();
        return view::make('corpminingtax::eventdetails', ['event_minings' => $event_minings, 'characters' => $characters, 'event_id' => $eid])->render();
    }

    private function getEventMinings(int $event_id)
    {
        $minings = DB::table('corp_mining_tax_event_minings')
            ->where('event_id', $event_id)
            ->orderBy('character_name')
            ->get();
        return $minings;
    }

    public function getCharacters(Request $request)
    {
        if ($request->has('q')) {
            $data = DB::table('character_infos')
                ->select(
                    'character_id AS id',
                    'name'
                )
                ->where('name', 'LIKE', "%" . $request->get('q') . "%")
                ->orderBy('name', 'asc')
                ->get();
        }
        return response()->json($data);
    }
}