<?php

use App\Auction;
use App\AuctionCarImage;
use App\CarMake;
use App\CarModel;
use App\Permission;
use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;

class AuctionCarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data Type
        $dataType = $this->dataType('slug', 'auction_cars');

        $dataType->fill([
            'name' => 'auction_cars',
            'display_name_singular' => 'Auction car',
            'display_name_plural' => 'Auction cars',
            'icon' => 'voyager-truck',
            'model_name' => 'App\\AuctionCar',
            'policy_name' => '',
            'controller' => 'App\Http\Controllers\Voyager\AuctionCarsController',
            'description' => '',
            'generate_permissions' => 1,
            'server_side' => 1,
            'details' => [
                'order_column' => 'id',
                'order_display_column' => null,
                'order_direction' => 'desc',
                'default_search_key' => null,
                'scope' => null
            ],
        ])->save();


        // Data Rows
        $dataType = DataType::where('name', 'auction_cars')->firstOrFail();

        $dataRow = $this->dataRow($dataType, 'id');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'id',
                'required' => 1,
                'browse' => 0,
                'read' => 0,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 1,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'auction_car_belongsto_auction_relationship');
        $dataRow->fill([
            'type' => 'relationship',
            'display_name' => 'Auction',
            'required' => 0,
            'browse' => 1,
            'read' => 0,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'details' => [
                'model' => Auction::class,
                'table' => 'auctions',
                'type' => 'belongsTo',
                'column' => 'auction_id',
                'key' => 'id',
                'label' => 'city',
                'pivot_table' => '',
                'pivot' => '0',
                'taggable' => '0'
            ],
            'order' => 2,
        ])->save();



        $dataRow = $this->dataRow($dataType, 'car_make_id');
        $dataRow->fill([
            'type' => 'number',
            'display_name' => 'Make',
            'required' => 1,
            'browse' => 1,
            'read' => 1,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'order' => 3,
        ])->save();


        $dataRow = $this->dataRow($dataType, 'auction_car_belongsto_car_make_relationship');
        $dataRow->fill([
            'type' => 'relationship',
            'display_name' => 'Make',
            'required' => 0,
            'browse' => 1,
            'read' => 0,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'details' => [
                'model' => CarMake::class,
                'table' => 'car_makes',
                'type' => 'belongsTo',
                'column' => 'car_make_id',
                'key' => 'id',
                'label' => 'name',
                'pivot_table' => '',
                'pivot' => '0',
                'taggable' => '0'
            ],
            'order' => 4,
        ])->save();


        $dataRow = $this->dataRow($dataType, 'car_model_id');
        $dataRow->fill([
            'type' => 'number',
            'display_name' => 'Model',
            'required' => 1,
            'browse' => 1,
            'read' => 1,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'order' => 5,
        ])->save();


        $dataRow = $this->dataRow($dataType, 'auction_car_belongsto_car_model_relationship');
        $dataRow->fill([
            'type' => 'relationship',
            'display_name' => 'Model',
            'required' => 0,
            'browse' => 1,
            'read' => 0,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'details' => [
                'model' => CarModel::class,
                'table' => 'car_models',
                'type' => 'belongsTo',
                'column' => 'car_model_id',
                'key' => 'id',
                'label' => 'name',
                'pivot_table' => '',
                'pivot' => '0',
                'taggable' => '0'
            ],
            'order' => 6,
        ])->save();


        $dataRow = $this->dataRow($dataType, 'auction_car_hasmany_auction_car_image_relationship');
        $dataRow->fill([
            'type' => 'relationship',
            'display_name' => 'Image',
            'required' => 0,
            'browse' => 1,
            'read' => 1,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'details' => [
                'model' => AuctionCarImage::class,
                'table' => 'auction_car_images',
                'type' => 'hasMany',
                'column' => 'auction_car_id',
                'key' => 'id',
                'label' => 'uri',
                'pivot_table' => 'auction_car_images',
                'pivot' => '0',
                'taggable' => '0'
            ],
            'order' => 7,
        ])->save();

        $dataRow = $this->dataRow($dataType, 'category');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Category',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 8,
            ])->save();
        }

        $dataRow = $this->dataRow($dataType, 'auction_id');
        $dataRow->fill([
            'type' => 'number',
            'display_name' => 'Auction',
            'required' => 1,
            'browse' => 0,
            'read' => 1,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'order' => 9,
        ])->save();



        $dataRow = $this->dataRow($dataType, 'alcopa_car_id');
        $dataRow->fill([
            'type' => 'number',
            'display_name' => 'Alcopa Car ID',
            'required' => 1,
            'browse' => 0,
            'read' => 1,
            'edit' => 0,
            'add' => 0,
            'delete' => 0,
            'order' => 10,
        ])->save();


        $dataRow = $this->dataRow($dataType, 'lot_number');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'Lot number',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 11,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'lot_price');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'Current price',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 12,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'lot_price_currency');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Currency',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 13,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'finish');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Finish',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 14,
            ])->save();
        }

        $dataRow = $this->dataRow($dataType, 'registration_number');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Registration number',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 15,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'fuel_type');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Fuel type',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 16,
            ])->save();
        }



        $dataRow = $this->dataRow($dataType, 'first_produced');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'First produced',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 17,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'mileage');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Mileage',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 18,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'vin');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'VIN',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 19,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'storage_location');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Storage location',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 20,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'gearbox');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Gearbox',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 21,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'description');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Description',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 22,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'inspection_report_url');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Inspection report url',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 23,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'created_at');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'timestamp',
                'display_name' => 'Created at',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 24,
            ])->save();
        }

        $dataRow = $this->dataRow($dataType, 'updated_at');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'timestamp',
                'display_name' => 'Updated at',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 25,
            ])->save();
        }


        // Permissions
        Permission::generateFor('auction_cars');
    }

    /**
     * @param $type
     * @param $field
     * @return mixed
     */
    protected function dataRow($type, $field)
    {
        return DataRow::firstOrNew([
            'data_type_id' => $type->id,
            'field' => $field,
        ]);
    }

    /**
     * @param $field
     * @param $for
     * @return mixed
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }
}
