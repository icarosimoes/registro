<?php

namespace App\Services;
use App\Models\Occurrence;
use App\Models\User;
use App\Services\Service;


class HomeService extends Service
{

    const STATUS = ["EM ABERTO" => 1, "ENCERRADO" => 2, "TODOS" => 0];

    /** GETs Home Dashboard */
    public function totalOccurrence()
    {
        $totalOccurrence = Occurrence::all()->count();
        return $totalOccurrence;
    }

    public function totalOccurrenceOpen()
    {
        $totalOccurrenceOpen = Occurrence::where([['status', 1]])->count();
        return $totalOccurrenceOpen;
    }

    public function totalOccurrenceClosed()
    {
        $totalOccurrenceClosed = Occurrence::where([['status', 3]])->count();
        return $totalOccurrenceClosed;
    }

    public function totalUsers()
    {
        $totalOccutotalUsersrrence = User::all()->count();
        return $totalOccutotalUsersrrence;
    }
    /** end GETs Home Dashboard */

}
