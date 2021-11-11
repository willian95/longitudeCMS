<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Services\ServiceUpdateRequest;
use App\Models\Service;
use App\Models\ServiceFile;

class ServiceController extends Controller
{
    
    function fetch(Request $request){

        $services = Service::paginate(20);
        return response()->json($services);

    }

    function edit($id){

        $service = Service::findOrFail($id);
        $files = ServiceFile::where("service_id", $id)->get();

        return view("services.edit.index", ["service" => $service, "files" => $files]);

    }

    function update(ServiceUpdateRequest $request){

        $service = Service::find($request->id);
        $service->title = $request->title;
        $service->description = $request->description;
        $service->image = $request->image;
        $service->update();

        $this->updateFiles($request, $service->id);
  
        return response()->json(["success" => true, "msg" => "Servicio actualizado"]);

    }

    function updateFiles($request, $service_id){

        $WorkImagesArray = [];
        $workImages = ServiceFile::where("service_id", $service_id)->get();
        foreach($workImages as $productSecondaryImage){
            array_push($WorkImagesArray, $productSecondaryImage->id);
        }

        $requestArray = [];
        foreach($request->filesUpload as $image){
            if(array_key_exists("id", $image)){
                array_push($requestArray, $image["id"]);
            }
        }

        $deleteWorkImages = array_diff($WorkImagesArray, $requestArray);
        
        foreach($deleteWorkImages as $imageDelete){
            ServiceFile::where("id", $imageDelete)->delete();
        }
        
        foreach($request->filesUpload as $workImage){
            if(!isset($workImage["id"])){
           
                $modelFile = new ServiceFile;
                $modelFile->file = $workImage["finalName"];
                $modelFile->type = $workImage["type"];
                $modelFile->service_id = $service_id;
                $modelFile->save();

                //dump($workImage);
                //
                if($workImage["type"] == 'zip'){
           
                    if($this->prepareRender($workImage["finalName"], $workImage["type"])){
                      
                        $fileName = str_replace(env('APP_URL'), env('ROOT_FOLDER'), $workImage["finalName"]);
    
                        $folderName = str_replace(env('ROOT_FOLDER')."files", "", $fileName);
                        $folderName = str_replace(".zip", "", $folderName);
                        $folderName = str_replace("/", "", $folderName);
    
                        if(!file_exists(env('DESTINATION_FOLDER').$folderName)){
    
                            mkdir(env('DESTINATION_FOLDER').$folderName, 0777);
    
                        }
    
                        $zip = new \ZipArchive;
                        $res = $zip->open($fileName);
                        if ($res === TRUE) {
               
                            $zip->extractTo(env('DESTINATION_FOLDER').$folderName);
                            $zip->close();
    
                            $modelFile->file = env('RENDER_DOMAIN').$folderName."/index.html";
                            $modelFile->update();
                 
                
                        } else {
                            return response()->json(["success" => false]);
                        }
            
                    }
                }
                

            }

        }


    }

    function prepareRender($file, $type){
        
        try{
            if(strpos(strtoupper($type), "ZIP") > -1){
    
                return true;
    
            }
    
            return false;
        }catch(\Exception $e){

            dd($e->getMessage());

        }

    }

}
