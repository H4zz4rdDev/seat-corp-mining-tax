<?php

/*
This file is part of SeAT

Copyright (C) 2015 to 2020  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

Route::group([
    'namespace' => 'pyTonicis\Seat\SeatCorpMiningTax\Http\Controllers',
    'middleware' => ['web', 'auth', 'locale'],
], function () {
    Route::prefix('/corpminingtax')
        ->group(function () {

            Route::get('/')
                ->name('corpminingtax.home')
                ->uses('CorpMiningTaxController@getHome');

            Route::post('/getMiningData')
                ->name('corpminingtax.data')
                ->uses('CorpMiningTaxController@getData');

            Route::get('/getCorporations')
                ->name('getCorporations')
                ->uses('CorpMiningTaxController@getCorporations');
        });
});