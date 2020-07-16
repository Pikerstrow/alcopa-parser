<?php


namespace App\Parser;


use App\AuctionCar;
use App\AuctionCarImage;
use App\Exceptions\AuctionsParserException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CarImagesDownloader
{
    private const BASE_URL = 'https://www.alcopa-auction.fr/en/';
    protected $base_path;
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
        $this->base_path = 'public' . DS . 'car_images' . DS . $car->auction_id;
    }


    /**
     * @throws \Exception
     */
    public function downloadImages()
    {
        try {
            foreach($this->urls_list as $url){
                $image_name_data = $this->getImageName($url);
                $new_file_name = $this->base_path . DS . $this->car->alcopa_car_id . DS . $image_name_data['name'] . '.' . $image_name_data['extension'];
                if (!file_exists($new_file_name)) {
                    $this->download($url, $new_file_name, $image_name_data);
                }
            }
        } catch (\Throwable $exception) {
            throw new \Exception('Download images method failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param string $url
     * @param string $new_file_name
     * @param array $image_name_data
     * @return array
     * @throws AuctionsParserException
     */
    public function download(string $url, string $new_file_name, array $image_name_data)
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
                return $this->saveImage($url, $result, $new_file_name, $image_name_data);
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
     * @param string $new_file_name
     * @param array $image_name_data
     * @return array
     * @throws \Exception
     */
    private function saveImage(string $url, $response, string $new_file_name, array $image_name_data): array
    {
        try {
            $directory = $this->base_path . DS . $this->car->alcopa_car_id;
            if(!Storage::exists($directory)){
                if (!Storage::makeDirectory($directory)) {
                    throw new \Exception('Creating directory operation failed. Dir name: ' . $directory);
                }
            }

            Storage::put($new_file_name, $response);

            $uri = "car_images/{$this->car->auction_id}/{$this->car->alcopa_car_id}/{$image_name_data['name']}.{$image_name_data['extension']}";

            //Storing image data in DB
            $image_data_db = [
                'path' => $new_file_name,
                'uri' => $uri
            ];
            $this->car->images()->updateOrCreate(['uri' => $uri], $image_data_db);
            return $image_data_db;
        } catch (\Throwable $exception) {
            throw new \Exception('Storing car image failed. Reason: ' . $exception->getMessage());
        }
    }
}
