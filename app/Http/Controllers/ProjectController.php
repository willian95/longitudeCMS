<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\projects\projectsStoreRequest;
use App\Http\Requests\projects\ProjectsUpdateRequest;
use App\Models\Project;
use App\Models\File;

class ProjectController extends Controller
{

    function makeSlug($title){

        $slug = strtolower($title);
        $slug = str_replace("á", "a", $slug);
        $slug = str_replace("é", "e", $slug);
        $slug = str_replace("í", "i", $slug);
        $slug = str_replace("ó", "o", $slug);
        $slug = str_replace("ú", "u", $slug);
        $slug = str_replace("/", "-", $slug);
        $slug = str_replace(" ", "-", $slug);
        $slug = str_replace("?", "-", $slug);
        return $slug;
    }

    function validateSlug($slug){

        if(Project::where("slug", $slug)->count() > 0){
            return false;
        }

        return true;

    }
    
    function store(projectsStoreRequest $request){

        try{

            $project = new Project;
            $project->title = $request->title;
            $project->description = $request->description;
            $project->image = $request->image;
            $slug = $this->makeSlug($request->title);
            
            if($this->validateSlug($slug)){

                $project->slug = $slug;

            }else{

                $project->slug = $slug."-".uniqid();

            }


            if($request->mainFileTypeSelect == '360'){
                $project->file =  $request->file;
                $project->type = "360";
            }else{
                $project->file = $request->file;
                $project->type = $request->type;
            }

            
            $project->save();
            
            if($request->mainFileTypeSelect == 'file'){
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
                            dump("error 1");
                            return response()->json(["success" => false]);
                        }

            
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
            if( $fileUpload["extension"] == "360"){
                $modelFile->file = $fileUpload["finalName"];
                $modelFile->type = $fileUpload["extension"];
            }else{
                $modelFile->file = $fileUpload["finalName"];
                $modelFile->type = $fileUpload["extension"];
            }
            
            $modelFile->project_id = $project_id;
            $modelFile->save();

            if( $fileUpload["extension"] == "zip"){
                
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
    
                
                        } else {
                            dump("error 2");
                            return response()->json(["success" => false]);
                        }
    
            
                    }
                
                    
                }

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

        $slug = $this->makeSlug($request->title);

        if(Project::where("slug", $slug)->where("id", "<>",$request->id)->count() == 0){

            $project->slug = $slug;

        }else{

            $project->slug = $slug."-".uniqid();

        }
        
        if(isset($request->file)){

            if($request->mainFileTypeSelect == '360'){

                $project->file = $request->file;
                $project->type = '360';

            }else{
                $project->file = $request->file;
                $project->type = $request->type;
            }

        }
        
        $project->update();

        if($request->mainFileTypeSelect == 'zip'){
            if(isset($request->file)){
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
                            dump("error 3");
                            return response()->json(["success" => false]);
                        }

            
                    }
                
                    
                }
            }
        }
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

                if($workImage["type"] == "zip"){
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
                            dump("error 4");
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
