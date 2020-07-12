<?php


namespace App\Parser;

use App\Auction;
use App\CarMake;
use App\Exceptions\AuctionsParserException;
use App\Parser\Traits\SharedMethods;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\DomCrawler\Crawler;



class AuctionCarParser
{
    use SharedMethods;

    private const NEEDLE_CHARACTERISTICS = [
        'Make',
        'Model',
        'Finish',
        'Registration',
        'Fuel type',
        'First produced',
        'Mileage',
        'Serial number',
        'Storage location',
        'Gearbox',
    ];
    private $tags;
    private $car_data;
    private $auction;


    /**
     * AuctionCarParser constructor.
     * @param array $car_data
     * @param Auction $auction
     */
    public function __construct(array $car_data, Auction $auction)
    {
        $this->tags     = config('parser.car');
        $this->car_data = $car_data;
        $this->auction  = $auction;
    }


    /**
     * @return Model
     * @throws AuctionsParserException
     */
    public function parse()
    {
        try {
            $html = $this->getWebPage($this->car_data['url']);
            $crawler = new Crawler(null, BASE_URL);
            $crawler->addHtmlContent($html, 'UTF-8');

            $car_data = [];
            $price_data = $this->getCarPrice($crawler);

            $car_data['lot_price'] = $price_data['price'];
            $car_data['lot_price_currency'] = $price_data['currency'];
            $car_data['category'] = $this->car_data['category'];
            $car_data['alcopa_car_id'] = $this->car_data['id'];
            $car_data['lot_number'] = $this->getLotNumber($crawler);
            $car_data['description'] = $this->getCarDescription($crawler);
            $car_data['inspection_report_url'] = $this->getCarReportUrl($crawler);
            $car_data = $this->addCarCharacteristics($car_data, $crawler);

            $auction_car = $this->auction->cars()->updateOrCreate(['alcopa_car_id' => $car_data['alcopa_car_id']], $car_data);
            $images_urls = $this->getCarImagesUrls($crawler);

            $image_downloader = new CarImagesDownloader($images_urls, $auction_car);
            $image_downloader->downloadImages();
            return $auction_car;
        } catch (\Throwable $exception) {
            $message = 'Car parsing operation failed. Reason: ' . $exception->getMessage();
            $message .= '. File: ' . $exception->getFile() . '. Line: ' . $exception->getLine();
            throw new AuctionsParserException($message);
        }
    }


    private function addCarCharacteristics(&$car_data, Crawler $crawler)
    {
        try {
            $car_characteristics = $this->getCarCharacteristics($crawler);
            $map = [
                "Finish" => "finish",
                "Registration" => "registration_number",
                "Fuel type" => "fuel_type",
                "First produced" => "first_produced",
                "Mileage" => "mileage",
                "Serial number" => "vin",
                "Storage location" => "storage_location",
                "Gearbox" => "gearbox",
            ];
            foreach ($map as $key => $value) {
                if ($key === 'First produced') {
                    $car_data[$value] = static::transformDate($car_characteristics[$key]);
                } else {
                    $car_data[$value] = $car_characteristics[$key];
                }
            }

            $car_make = $this->handleCarMake($car_characteristics['Make']);
            $car_model = $this->handleCarModel($car_make, $car_characteristics['Model']);
            $car_data['car_make_id'] = $car_make->id; //Впринципі це зайве... це можна отримати через звязок по моделі (car_model_id)
            $car_data['car_model_id'] = $car_model->id;
            return $car_data;
        } catch (\Throwable $exception) {
            throw new \Exception('Adding car characteristics failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    private function handleCarMake(string $name)
    {
        try {
            return CarMake::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        } catch (\Throwable $exception) {
            throw new \Exception('Handling car make failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param CarMake $make
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    private function handleCarModel(CarMake $make, string $name)
    {
        try {
            return $make->models()->updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        } catch (\Throwable $exception) {
            throw new \Exception('Handling car model failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param Crawler $crawler
     * @return null
     * @throws \Exception
     */
    private function getCarReportUrl(Crawler $crawler)
    {
        try {
            $needle = $this->tags['inspection_report_url']['anchor'];
            $nodes = $crawler->filter($needle['tag']);

            $url = null;
            if (!empty($nodes) && count($nodes) > 0) {
                $nodes->each(static function (Crawler $node) use ($needle, &$url) {
                    $options[] = $node->attr('href');
                    if ($node->text() === $needle['text']) {
                        $url = $node->attr('href');
                    }
                });
            }
            return $url;
        } catch (\Throwable $exception) {
            throw new \Exception('Retrieving car report url failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param Crawler $crawler
     * @return array
     * @throws \Exception
     */
    private function getCarImagesUrls(Crawler $crawler)
    {
        try {
            $nodes = $crawler->filter($this->tags['images']);
            $urls = [];
            if (!empty($nodes) && count($nodes) > 0) {
                $nodes->each(static function (Crawler $node) use (&$urls) {
                    $urls[] = $node->attr('href');
                });
            }
            return $urls;
        } catch (\Throwable $exception) {
            throw new \Exception('Retrieving car images url failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param Crawler $crawler
     * @return string
     * @throws \Exception
     */
    private function getCarDescription(Crawler $crawler): string
    {
        try {
            $needle = $this->tags['description']['anchor'];
            $nodes = $crawler->filter($needle['tag']);

            $options = [];
            if (!empty($nodes) && count($nodes) > 0) {
                $nodes->each(static function (Crawler $node) use ($needle, &$options) {
                    if ($node->text() === $needle['text']) {
                        $siblings = $node->siblings();
                        if (!empty($siblings) && count($siblings) > 0) {
                            $table = null;
                            $siblings->each(static function (Crawler $node) use (&$table) {
                                if ($node->nodeName() === 'table') {
                                    $table = $node;
                                }
                            });
                            if (!empty($table)) {
                                $rows = $table->filter('tr');
                                if (!empty($rows) && count($rows) > 0) {
                                    $rows->each(static function (Crawler $node) use (&$options) {
                                        $children = $node->children();
                                        if (!empty($children) && count($children) > 0) {
                                            $td = $children->first();
                                            if (!empty($td)) {
                                                $value = $td->text();
                                                $options[] = $value;
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                });
            }
            return !empty($options) ? implode(", ", $options) : '';
        } catch (\Throwable $exception) {
            throw new \Exception('Retrieving car description (options) failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param Crawler $crawler
     * @return string|null
     * @throws \Exception
     */
    private function getCarPrice(Crawler $crawler)
    {
        try {
            $needle_tag = $this->tags['lot_price'];
            $node = $crawler->filter($needle_tag);
            if (!empty($node) && count($node) > 0) {
                $needle = $node->first();
                if (!empty($needle)) {
                    $price_str = $needle->text();
                    return [
                        'price' => preg_replace('#\D#', '', $price_str),
                        'currency' => preg_replace('#(\d|\s)#', '', $price_str),
                    ];
                };
            }
            return null;
        } catch (\Throwable $exception) {
            throw new \Exception('Retrieving car price failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param Crawler $crawler
     * @return string|string[]|null
     * @throws \Exception
     */
    private function getLotNumber(Crawler $crawler)
    {
        try {
            $needle_tag = $this->tags['lot_number'];
            $node = $crawler->filter($needle_tag);
            if (!empty($node) && count($node) > 0) {
                $needle = $node->first();
                if (!empty($needle)) {
                    $dirty = $needle->text();
                    return preg_replace('#\D#', '', $dirty);
                };
            }
            return null;
        } catch (\Throwable $exception) {
            throw new \Exception('Retrieving lot number failed. Reason: ' . $exception->getMessage());
        }
    }


    /**
     * @param Crawler $crawler
     * @return array
     * @throws \Exception
     */
    private function getCarCharacteristics(Crawler $crawler): array
    {
        try {
            $needle = $this->tags['characteristics']['anchor'];
            $nodes = $crawler->filter($needle['tag']);

            $characteristics = [];
            if (!empty($nodes) && count($nodes) > 0) {
                $nodes->each(static function (Crawler $node) use ($needle, &$characteristics) {
                    if ($node->text() === $needle['text']) {
                        $siblings = $node->siblings();
                        if (!empty($siblings) && count($siblings) > 0) {
                            $table = null;
                            $siblings->each(static function (Crawler $node) use (&$table) {
                                if ($node->nodeName() === 'table') {
                                    $table = $node;
                                }
                            });
                            if (!empty($table)) {
                                $rows = $table->filter('tr');
                                if (!empty($rows) && count($rows) > 0) {
                                    $rows->each(static function (Crawler $node) use (&$characteristics) {
                                        $children = $node->children();
                                        if (!empty($children) && count($children) > 0) {
                                            $th = $children->first();
                                            $td = $children->last();
                                            if (!empty($th) && !empty($td)) {
                                                $property = $th->text();
                                                $value = $td->text();
                                                if (in_array($property, static::NEEDLE_CHARACTERISTICS)) {
                                                    $characteristics[$property] = $value;
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                });
            }
            return $characteristics;
        } catch (\Throwable $exception) {
            throw new \Exception('Retrieving car characteristics failed. Reason: ' . $exception->getMessage());
        }
    }
}
