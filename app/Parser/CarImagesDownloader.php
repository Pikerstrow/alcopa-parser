<?php


namespace App\Parser;


use App\AuctionCar;
use App\Exceptions\AuctionsParserException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CarImagesDownloader
{
    private const BASE_URL = 'https://www.alcopa-auction.fr/en/';
    protected $urls_list;
    protected $car;

    /**
     * CarImagesDownloader constructor.
     * @param array $list
     * @param AuctionCar $car
     */
    public function __construct(array $list, AuctionCar $car)
    {
        $this->urls_list = $list;
        $this->car       = $car;
    }


    /**
     * @throws \Exception
     */
    public function downloadImages()
    {
        try {
            foreach($this->urls_list as $url){
                $image_data = $this->download($url);
                $this->car->images()->updateOrCreate(['auction_car_id' => $this->car->id], $image_data);
            }
        } catch (\Throwable $exception) {
            throw new \Exception('Download images method failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param string $url
     * @return array
     * @throws AuctionsParserException
     */
    public function download(string $url)
    {
        try {
            $resource = curl_init($url);
            curl_setopt($resource, CURLOPT_VERBOSE, 1);
            curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($resource, CURLOPT_AUTOREFERER, false);
            curl_setopt($resource, CURLOPT_REFERER, static::BASE_URL);
            curl_setopt($resource, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($resource, CURLOPT_HEADER, 0);
            $result = curl_exec($resource);
            curl_close($resource);

            if($result){
                return $this->saveImage($url, $result);
            } else {
                Log::info(['image not downloaded' => 'URL: ' . $url . '. No result from CURL request']);
            }
        } catch (\Throwable $exception) {
            throw new AuctionsParserException('Download car image failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param string $url
     * @return array
     * @throws AuctionsParserException
     */
    private function getImageName(string $url): array
    {
        try {
            $file_name = substr($url, strrpos($url, '/') + 1);
            $parts = explode('.', $file_name);
            return [
                'name' => $parts[0],
                'extension' => $parts[1]
            ];
        } catch (\Throwable $exception) {
            throw new AuctionsParserException('Fetching image name from ulr failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param string $url
     * @param $response
     * @return array
     * @throws \Exception
     */
    private function saveImage(string $url, $response): array
    {
        try {
            $image_data = $this->getImageName($url);
            $dynamic_path = 'public' . DS . 'car_images' . DS . $this->car->auction_id . DS . $this->car->alcopa_car_id;
            if(!Storage::exists($dynamic_path)){
                if (!Storage::makeDirectory($dynamic_path)) {
                    throw new \Exception('Creating directory operation failed. Dir name: ' . $dynamic_path);
                }
            }
            $file_name = $dynamic_path . DS . $image_data['name'] . '.' . $image_data['extension'];
            Storage::put($file_name, $response);
            return [
                'path' => $file_name,
                'uri' => "car_images/{$this->car->auction_id}/{$this->car->alcopa_car_id}/{$image_data['name']}.{$image_data['extension']}"
            ];
        } catch (\Throwable $exception) {
            throw new \Exception('Storing car image failed. Reason: ' . $exception->getMessage());
        }
    }
}
