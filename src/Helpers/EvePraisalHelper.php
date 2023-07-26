<?php

/*
 *  INFO: EvePraisal deactivated
 */
namespace pyTonicis\Seat\SeatCorpMiningTax\Helpers;

use Illuminate\Support\Facades\Cache;

class EvePraisalHelper
{
    private $priceData;

    /**
     * @param int $typeId
     * @return mixed|null
     */
    public static function getAllItemPrices(int $typeId)
    {

        $cacheId = "tax_" . $typeId;

        if (Cache::has($cacheId)) {
            $prices = Cache::get($cacheId);
        } else {
            $prices = self::doCall($typeId);
            Cache::put($cacheId, $prices, 3600);
        }

        return $prices;
    }

    /**
     * @param int $typeId
     * @return int
     */
    public static function getItemPriceByTypeId(int $typeId): int
    {
        return self::getAllItemPrices($typeId)["summaries"][0]["prices"]["buy"]["percentile"];
    }

    /**
     * @param string $itemTypeId
     * @return mixed|null
     */
    public static function doCall(string $itemTypeId)
    {

        $url = sprintf("https://evepraisal.com/item/%d.json", $itemTypeId);
        $data = @file_get_contents($url);

        if ($data === false) {
            return null;
        }

        return json_decode($data, true);

    }

}
