<?php

namespace pyTonicis\Seat\SeatCorpMiningTax\Services;

use pyTonicis\Seat\SeatCorpMiningTax\Helpers\CharacterHelper;
use pyTonicis\Seat\SeatCorpMiningTax\Helpers\EvePraisalHelper;
use pyTonicis\Seat\SeatCorpMiningTax\Models\TaxData\CharacterData;
use pyTonicis\Seat\SeatCorpMiningTax\Models\TaxData\CharacterMiningRecord;
use pyTonicis\Seat\SeatCorpMiningTax\Models\TaxData\MiningTaxResult;
use pyTonicis\Seat\SeatCorpMiningTax\Models\TaxData\OreType;
use Illuminate\Support\Facades\DB;

/**
 * Class MiningTaxService
 */
class MiningTaxService
{
    /**
     * @param int $corpId
     * @param int $month
     * @param int $year
     * @return mixed
     */
    private function getMiningResultsFromDb(int $corpId, int $month, int $year)
    {

        return DB::table('character_minings as cm')
            ->select(
                'cm.character_id',
                'cm.quantity',
                'cm.type_id',
                'it.typeName'
            )
            ->join('corporation_members as cmem', 'cm.character_id', '=', 'cmem.character_id')
            ->join('market_prices as mp', 'cm.type_id', 'mp.type_id')
            ->join('invTypes as it', 'cm.type_id', '=', 'it.typeID')
            ->where('cmem.corporation_id', '=', $corpId)
            ->where('cm.month', '=', $month)
            ->where('cm.year', '=', $year)
            ->get();
    }

    public function createMiningTaxResult(int $corpId, int $month, int $year): MiningTaxResult
    {
        $miningResult = new MiningTaxResult($month, $year);

        foreach ($this->getMiningResultsFromDb($corpId, $month, $year) as $data) {

            $mainCharacterData = CharacterHelper::getMainCharacterCharacter($data->character_id);

            if (!$miningResult->hasOreType($data->type_id)) {

                $ore = new OreType();
                $ore->id = $data->type_id;
                $ore->name = $data->typeName;
                $ore->price = EvePraisalHelper::getItemPriceByTypeId($data->type_id);

                $miningResult->addOre($ore);
            }

            if (!$miningResult->hasCharacterData($mainCharacterData->main_character_id)) {

                $charData = new CharacterData(
                    $mainCharacterData->main_character_id,
                    $mainCharacterData->name
                );

                $miningResult->addCharacterData($charData);
            } else {
                $charData = $miningResult->getCharacterDataById($mainCharacterData->main_character_id);
            }

            $charData->addCharacterMining(new CharacterMiningRecord(
                $data->type_id,
                $data->quantity
            ));

            $charData->addToPriceSummary($data->quantity);
            $charData->addTax(EvePraisalHelper::getItemPriceByTypeId($data->type_id) * $data->quantity);
        }

        return $miningResult;
    }
}