<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $services = [
            [
                "id" => 1,
                "title" => "Suite magic box",
                "description" => "descripction",
                "fileAmount" => 1
            ],
            [
                "id" => 2,
                "title" => "Render 360°",
                "description" => "descripction",
                "fileAmount" => 9
            ],
            [
                "id" => 3,
                "title" => "Recorrido interactivo 360°",
                "description" => "descripction",
                "fileAmount" => 4
            ],
            [
                "id" => 4,
                "title" => "Video interactivo 360°",
                "description" => "descripction",
                "fileAmount" => 1
            ],
            [
                "id" => 5,
                "title" => "Fotografía 8k 360°",
                "description" => "descripction",
                "fileAmount" => 6
            ],
            [
                "id" => 6,
                "title" => "Render 2D",
                "description" => "descripction",
                "fileAmount" => 4
            ],
            [
                "id" => 7,
                "title" => "Videos de proyectos",
                "description" => "descripction",
                "fileAmount" => 4
            ],
        ];

        foreach($services as $service){

            if(Service::where("id", $service['id'])->count() == 0){

                $serviceModel = new Service;
                $serviceModel->id = $service["id"];
                $serviceModel->title = $service['title'];
                $serviceModel->description = $service["description"];
                $serviceModel->fileAmount = $service["fileAmount"];
                $serviceModel->save();

            }

        }

    }
}
