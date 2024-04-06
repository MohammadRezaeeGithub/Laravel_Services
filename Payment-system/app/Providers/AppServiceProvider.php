<?php

namespace App\Providers;

use App\Http\Support\Basket\Basket;
use App\Support\Storage\Contracts\StorageInterface;
use App\Support\Storage\SessionStorage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//here we tell laravel to return which class when we call StorageInterface.
		//we bind it in a way.
		$this->app->bind(StorageInterface::class, function ($app) {
			return new SessionStorage('cart');
		});
	}
}
