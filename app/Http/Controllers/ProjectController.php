<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\projects\projectsStoreRequest;
use App\Http\Requests\projects\ProjectsUpdateRequest;
use App\Models\Project;
use App\Models\File;

class ProjectController extends Controller
{
    
    function store(projectsStoreRequest $request){

        try{

            $project = new Project;
            $project->title = $request->title;
            $project->description = $request->description;
            $project->image = $request->image;
            $project->file = $request->file;
            $project->type = $request->type;
            $project->save();

            if($this->prepareRender($request->file, $request->type)){

              
                if(strpos(strtoupper($request->type), "ZIP") > -1){
    
                    $fileName = str_replace(env('APP_URL'), env('ROOT_FOLDER'), $request->file);

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

                        $project->file = env('RENDER_DOMAIN').$folderName."/index.html";
                        $project->update();

            
                    } else {
                        return response()->json(["success" => false]);
                    }

        
                }
            
                
            }

            $this->storeFiles($request, $project->id);
            
            return response()->json(["success" => true]);

        }catch(\Exception $e){

            dd($e->getMessage());

        }
        

    }

    function storeFiles($request, $project_id){
  
        foreach($request->filesUpload as $fileUpload){
            
            $modelFile = new File;
            $modelFile->file = $fileUpload["finalName"];
            $modelFile->type = $fileUpload["extension"];
            $modelFile->project_id = $project_id;
            $modelFile->save();

            if($this->prepareRender($fileUpload["finalName"], $fileUpload["extension"])){

                if(strpos(strtoupper($fileUpload["extension"]), "ZIP") > -1){
    
                    $fileName = str_replace(env('APP_URL'), env('ROOT_FOLDER'), $fileUpload["finalName"]);

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

                        dd($modelFile->file);

            
                    } else {
                        return response()->json(["success" => false]);
                    }

        
                }
            
                
            }

            if($this->prepareRender($fileUpload["finalName"], $fileUpload["extension"])){

                $modelFile->file = str_replace(env('APP_URL'), env('RENDER_DOMAIN'), $fileUpload["finalName"]);
                $modelFile->update();
    
            }

        }

    }

    function edit($id){

        $project = Project::findOrFail($id);
        $files = File::where("project_id", $id)->get();

        return view("projects.edit.index", ["project" => $project, "files" => $files]);

    }

    function update(ProjectsUpdateRequest $request){

        $project = Project::find($request->id);
        $project->title = $request->title;
        $project->description = $request->description;
        $project->image = $request->image;
        $project->file = $request->file;
        $project->type = $request->type;
        $project->update();

        $this->updateFiles($request, $project->id);
        
        return response()->json(["success" => true]);

    }

    function updateFiles($request, $project_id){

        $WorkImagesArray = [];
        $workImages = File::where("project_id", $project_id)->get();
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
            File::where("id", $imageDelete)->delete();
        }

        foreach($request->filesUpload as $workImage){
            if(!isset($workImage["id"])){
                
                $modelFile = new File;
                $modelFile->file = $workImage["finalName"];
                $modelFile->type = $workImage["type"];
                $modelFile->project_id = $project_id;
                $modelFile->save();

                if($this->prepareRender($workImage["finalName"], $workImage["type"])){

                    $fileName = str_replace(env('APP_URL'), env('ROOT_FOLDER'), $request->file);

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

                        $project->file = env('RENDER_DOMAIN').$folderName."/index.html";
                        $project->update();

            
                    } else {
                        return response()->json(["success" => false]);
                    }

                    $modelFile->file = str_replace(env('APP_URL'), env('RENDER_DOMAIN'), $workImage["finalName"]);
                    $modelFile->update();
        
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

    function fetch(Request $request){

        $projects = Project::paginate(20);
        return response()->json($projects);

    }

    function delete(Request $request){

        $files = File::where("project_id", $request->id)->get();
        foreach($files as $file){

            $file->delete();

        }

        Project::find($request->id)->delete();
        return response()->json(["success" => true, "msg" => "Proyecto eliminado"]);

    }

}
