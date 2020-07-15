<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use App\Permission;


class AuctionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data Type
        $dataType = $this->dataType('slug', 'auctions');

        $dataType->fill([
            'name' => 'auctions',
            'display_name_singular' => 'Auction',
            'display_name_plural' => 'Auctions',
            'icon' => 'voyager-shop',
            'model_name' => 'App\\Auction',
            'policy_name' => '',
            'controller' => '',
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
        $dataType = DataType::where('name', 'auctions')->firstOrFail();

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


        $dataRow = $this->dataRow($dataType, 'auction_id');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'Alcopa auction ID',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 2,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'auction_id');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'Alcopa auction ID',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 3,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'name');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Name',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 4,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'uri');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'text',
                'display_name' => 'Auction URI',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 5,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'start_date');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'timestamp',
                'display_name' => 'Start date',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 6,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'end_date');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'timestamp',
                'display_name' => 'End date',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 7,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'city');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'date',
                'display_name' => 'City',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 8,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'lots_number');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'Number of lots',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 9,
            ])->save();
        }


        $dataRow = $this->dataRow($dataType, 'lots_number');
        if (!$dataRow->exists) {
            $dataRow->fill([
                'type' => 'number',
                'display_name' => 'Number of lots',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'order' => 9,
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
                'order' => 10,
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
                'order' => 11,
            ])->save();
        }


        // Permissions
        Permission::generateFor('auctions');
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
