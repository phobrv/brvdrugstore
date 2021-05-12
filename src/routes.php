<?php
Route::middleware(['web', 'auth', 'auth:sanctum', 'lang', 'verified'])->namespace('Phobrv\BrvCore\Http\Controllers')->group(function () {
	Route::middleware(['can:drugstore_manage'])->prefix('admin')->group(function () {
		Route::resource('region', 'TermController');
	});
});

Route::middleware(['web', 'auth', 'auth:sanctum', 'lang', 'verified'])->namespace('Phobrv\BrvDrugstore\Controllers')->group(function () {
	Route::middleware(['can:drugstore_manage'])->prefix('admin')->group(function () {
		Route::resource('drugstore', 'DrugstoreController');
		Route::post('/drugstore/updateUserSelectRegion', 'DrugstoreController@updateUserSelectRegion')->name('drugstore.updateUserSelectRegion');
	});
});
