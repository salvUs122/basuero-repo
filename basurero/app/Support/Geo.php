<?php

namespace App\Support;

class Geo
{
    // Distancia Haversine (metros)
    public static function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    // Distancia punto -> segmento (aprox en metros)
    public static function pointToSegmentMeters($plat, $plng, $alat, $alng, $blat, $blng): float
    {
        // Proyección equirectangular para aproximar en plano local
        $x = deg2rad($plng) * cos(deg2rad($plat));
        $y = deg2rad($plat);

        $x1 = deg2rad($alng) * cos(deg2rad($alat));
        $y1 = deg2rad($alat);

        $x2 = deg2rad($blng) * cos(deg2rad($blat));
        $y2 = deg2rad($blat);

        $dx = $x2 - $x1; $dy = $y2 - $y1;
        if ($dx == 0 && $dy == 0) {
            return self::haversine($plat, $plng, $alat, $alng);
        }

        $t = (($x - $x1)*$dx + ($y - $y1)*$dy) / ($dx*$dx + $dy*$dy);
        $t = max(0, min(1, $t));

        $xc = $x1 + $t*$dx;
        $yc = $y1 + $t*$dy;

        // Convertimos radianes a metros aproximando con Haversine usando punto cercano
        $clat = rad2deg($yc);
        $clng = rad2deg($xc / cos(deg2rad($clat)));

        return self::haversine($plat, $plng, $clat, $clng);
    }

    // Distancia mínima punto -> polyline (array de [lat,lng])
    public static function pointToPolylineMeters($lat, $lng, array $lineLatLngs): float
    {
        $min = INF;
        for ($i=0; $i < count($lineLatLngs)-1; $i++) {
            [$aLat,$aLng] = $lineLatLngs[$i];
            [$bLat,$bLng] = $lineLatLngs[$i+1];
            $d = self::pointToSegmentMeters($lat, $lng, $aLat, $aLng, $bLat, $bLng);
            if ($d < $min) $min = $d;
        }
        return $min === INF ? 0 : $min;
    }
}
