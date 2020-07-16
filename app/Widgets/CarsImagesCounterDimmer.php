<?php


namespace App\Widgets;


use App\AuctionCar;
use App\AuctionCarImage;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Widgets\BaseDimmer;
use Illuminate\Support\Facades\Storage;

class CarsImagesCounterDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = AuctionCarImage::count();
        $string = trans_choice('voyager::dimmer.auction_car_images', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-photo',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.car_images_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('voyager::dimmer.auction_car_link_text'),
                'link' => route('voyager.auction_cars.index'),
            ],
            'image' => asset('widget-backgrounds/03.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed(): bool
    {
        return Auth::user()->can('browse', app(AuctionCar::class));
    }
}
