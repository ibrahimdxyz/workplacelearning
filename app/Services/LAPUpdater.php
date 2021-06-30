<?php

declare(strict_types=1);

namespace App\Services;

use App\Category;
use App\Chain;
use App\ChainManager;
use App\Column;
use App\column_data;
use App\fieldtype;
use App\ResourceMaterial;
use Carbon\Carbon;
use App\Genericlearningactivity;

class LAPUpdater
{
    /**
     * @var ChainManager
     */
    private $chainManager;

    public function __construct(ChainManager $chainManager)
    {
        $this->chainManager = $chainManager;
    }

    public function update(Genericlearningactivity $genericlearningactivity, $data): bool
    {
        //choosing fieldtypes from database
        $radiobutton = Fieldtype::where("fieldtype","radiobutton")->first();
        $text = Fieldtype::where("fieldtype","text")->first();
        $datePicker = Fieldtype::where("fieldtype","date")->first();
        $button = Fieldtype::where("fieldtype","date")->first();

        if (isset($data['datum'])) {
            $column = $this->createColumn("date", null, Carbon::parse($data['datum'])->format('Y-m-d'), $datePicker, "date");
            $genericlearningactivity->column()->associate($column);
        }
        if (isset($data['omschrijving'])) {
            $column = $this->createColumn("description", null, $data['omschrijving'], $text, "string");
            $genericlearningactivity->column()->associate($column);
        }
        if (isset($data['aantaluren']) || isset($data['aantaluren_custom'])) {
            $description = $data['aantaluren'] !== 'x' ? $data['aantaluren'] : round(
                ((int) $data['aantaluren_custom']) / 60, 2);
            $columnOptions = "[0.25,0.50,0.75]";
            $column = $this->createColumn("duration", $columnOptions, $description, $button, "float");
            $genericlearningactivity->column()->associate($column);
        }

        if (isset($data['resource'])) {
            switch ($data['resource']) {
                case 'persoon':
                    $genericlearningactivity->resourcePerson()->associate($data['personsource']);
                    $genericlearningactivity->resourceMaterial()->dissociate();
                    $columnOptions = "['persoon','internet','boek', 'alleen']";
                    $column = $this->createColumn("res_material_detail", $columnOptions, null, $button, "string");
                    $genericlearningactivity->column()->associate($column);
                    break;
                case 'internet':
                    $genericlearningactivity->resourceMaterial()->associate((new ResourceMaterial())->find(1));
                    $columnOptions = "['persoon','internet','boek', 'alleen']";
                    $column = $this->createColumn("res_material_detail", $columnOptions, $data['internetsource'], $button, "string");
                    $genericlearningactivity->column()->associate($column);
                    $genericlearningactivity->resourcePerson()->dissociate();
                    break;
                case 'boek':
                    $genericlearningactivity->resourceMaterial()->associate((new ResourceMaterial())->find(2));
                    $columnOptions = "['persoon','internet','boek', 'alleen']";
                    $column = $this->createColumn("res_material_detail", $columnOptions, $data['booksource'], $button, "string");
                    $genericlearningactivity->column()->associate($column);
                    $genericlearningactivity->resourcePerson()->dissociate();
                    break;
                case 'alleen':
                    $genericlearningactivity->resourcePerson()->dissociate();
                    $genericlearningactivity->resourceMaterial()->dissociate();
                    $columnOptions = "['persoon','internet','boek', 'alleen']";
                    $column = $this->createColumn("res_material_detail", $columnOptions, null, $button, "string");
                    $genericlearningactivity->column()->associate($column);
                    break;
            }
        }

        if (isset($data["category_id"])) {
            $genericlearningactivity->category()->associate((new Category())->find($data['category_id']));
        }

        if (isset($data["moeilijkheid"])) {
            $columnOptions = "['makkelijk','gemiddeld','moeilijk']";
            $column = $this->createColumn("difficulty", $columnOptions, $data['moeilijkheid'], $button, "string");
            $genericlearningactivity->column()->associate($column);
        }

        if (isset($data["status"])) {
            $columnOptions = "['afgerond','mee bezig','overgedragen']";
            $column = $this->createColumn("status", $columnOptions, $data['status'], $button, "string");
            $genericlearningactivity->column()->associate($column);
        }

//        Based on model LAA & LAP instead of GLA
//        $chainId = $data['chain_id'] ?? null;
//
//        if ($chainId !== null) {
//            if (((int) $chainId) === -1) {
//                $genericlearningactivity->chain_id = null;
//            } elseif (((int) $chainId) !== -1) {
//                $chain = (new Chain())->find($chainId);
//                if ($chain->status !== Chain::STATUS_FINISHED) {
//                    $this->chainManager->attachActivity($genericlearningactivity, $chain);
//                }
//            }
//        }

        return $genericlearningactivity->save();
    }

    private function createColumn($name, $columnOptions, $data, $fieldType, $dataType) {
        $column = new Column;
        $column->name = $name;
        $column->columnOptions = $columnOptions;
        $column->fieldType()->associate($fieldType);
        $column->save();

        $columnData = new column_data();
        $columnData->column()->associate($column);
        $columnData->data_as_string = $data;
        $columnData->dataType = $dataType;
        $columnData->save();

        return $column;
    }
}
