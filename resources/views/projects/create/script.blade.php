<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>

    var app = new Vue({
        el: '#dev-products',
        data(){
            return{

                name:"",

                imagesToUpload:[],
                workImages:[],
                secondaryPreviewPicture:"",
                secondaryPicture:"",

                location:"",
                squareMeter:"",
                projectType:"",
                description:"",
                action:"create",

                errors:[],
                loading:false,

                imagePreview:"",
                file:"",
                imageProgress:0,
                pictureStatus:"",
                finalPictureName:"",

                mainFile:"",
                mainFileProgress:0,
                mainFileStatus:"",
                finalMainFileName:"",
                mainFileType:"",

                secondaryPicture:"",
                secondaryPreviewPicture:"",
                fileName:""


            }
        },
        methods:{

            store(){

                var completeUploading = true

                this.workImages.forEach((data) => {
                    if(data.status == 'subiendo'){
                        completeUploading = false
                    }
                })

                if(completeUploading && this.pictureStatus == "listo"){

                    this.workImages.forEach((data) => {
                        this.imagesToUpload.push({finalName:data.finalName, type: data.type})
                    })

                    this.loading = true
                    axios.post("{{ url('/products/store') }}", {
                        name:this.name,
                        location:this.location,
                        square_meter: this.squareMeter,
                        image: this.finalPictureName,
                        description: CKEDITOR.instances.editor1.getData(),
                        workImages: this.imagesToUpload,
                        project_type: this.projectType,
                        mainImageFileType: this.mainImageFileType,
                    }).then(res => {
                        this.loading = false
                        if(res.data.success == true){

                            swal({
                                title: "Excelente!",
                                text: "Proyecto creado!",
                                icon: "success"
                            }).then(function() {
                                window.location.href = "{{ url('products/list') }}";
                            });


                        }else{

                            alert(res.data.msg)
                        }

                    }).catch(err => {

                        this.loading = false
                        this.errors = err.response.data.errors

                        swal({
                            text: "Hay campos que debes verificar!",
                            icon: "warning"
                        })

                    })


                }else{

                    swal({
                        text:"Aún hay contenido cargandose",
                        icon:"warning"
                    })

                }



            },

            onMainImageChange(e){
                this.getImage(e)
            },

            getImage(e){
               
                let picture = e.target.files[0];
                
                this.imagePreview = URL.createObjectURL(picture);
                
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);

            },
            createImage(file) {
                this.file = file
      
                if(file['type'].split('/')[0] == "image"){

                    this.uploadMainImage()

                }else{

                    $("#form1").get(0).reset();
                
                    swal({
                        text:"Debe seleccionar un archivo de imagen",
                        icon:"error"
                    })

                }

            },
            uploadMainImage(type){

           

                this.imageProgress = 0;
                

                let formData = new FormData()
                formData.append("file", this.file)

                var _this = this
            
                this.pictureStatus = "subiendo";
                

                var config = {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                    onUploadProgress: function(progressEvent) {

                        var progressPercent = Math.round((progressEvent.loaded * 100.0) / progressEvent.total);
                        
                        _this.imageProgress = progressPercent
                        

                    }
                }

                axios.post(
                    "{{ url('/upload/file') }}",
                    formData,
                    config
                ).then(res => {

               
                        this.pictureStatus = "listo";
                        this.finalPictureName = res.data.fileRoute
           


                }).catch(err => {
                    console.log(err)
                })

            },



            onSecondaryImageChange(e){
                this.secondaryPicture = e.target.files[0];

                this.secondaryPreviewPicture = URL.createObjectURL(this.secondaryPicture);
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createSecondaryImage(files[0]);
            },
            createSecondaryImage(file) {

                this.file = file

                if(file['type'].split('/')[0] == "image"){
                    this.fileName = file['name']

                    let reader = new FileReader();
                    let vm = this;
                    reader.onload = (e) => {
                        vm.secondaryPicture = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }else{
                    swal({
                        text:"Debes seleccionar un archivo de imagen",
                        icon:"error"
                    })
                }

            },
            uploadSecondaryImage(){

                let formData = new FormData()
                formData.append("file", this.file)

                var _this = this
                var fileName = this.fileName

                var config = {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                    onUploadProgress: function(progressEvent) {

                        var progressPercent = Math.round((progressEvent.loaded * 100.0) / progressEvent.total);


                        if(_this.workImages.length > 0){

                            _this.workImages.forEach((data,index) => {

                                if(data.originalName == fileName){
                                _this.workImages[index].progress = progressPercent
                                }

                            })

                        }

                    }
                }

                axios.post(
                    "{{ url('/upload/picture') }}",
                    formData,
                    config
                ).then(res => {
                    this.workImages.forEach((data, index) => {

                        let returnedName = res.data.originalName.toLowerCase()

                        if(data.originalName.toLowerCase() == returnedName.toLowerCase()){
                            this.workImages[index].status = "listo";
                            this.workImages[index].finalName = res.data.fileRoute
                        }

                    })

                }).catch(err => {
                    console.log(err)
                })

            },
            addSecondaryImage(){

                if(this.secondaryPicture != null){
                    this.uploadSecondaryImage()
                    this.workImages.push({image: this.secondaryPicture, status: "subiendo", originalName:this.fileName, finalName:"", progress:0})

                    this.secondaryPicture = ""
                    this.secondaryPreviewPicture = ""

                }else{
                    swal({
                        title: "Oppss!",
                        text: "Debes añadir una imágen",
                        icon: "error"
                    });
                }


            },

            onMainFileChange(e){
                let picture = e.target.files[0];

                //this.imagePreview = URL.createObjectURL(picture);
                
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createFile(files[0]);
            },

            createFile(file) {

                this.file = file
                if(this.mainFileValidation(file)){


                    this.uploadMainFile()

                }else{
                    $("#form2").get(0).reset();
                    swal({
                        text:"Debe seleccionar un archivo válido",
                        icon:"error"
                    })

                }

            },

            mainFileValidation(file){

                if(
                    (file['type'].split('/')[0] == "image" && file['type'].split('/')[1].toUpperCase() == "PNG") || 
                    (file['type'].split('/')[0] == "image" && file['type'].split('/')[1].toUpperCase() == "JPG")  || 
                    (file['type'].split('/')[0] == "video" && file['type'].split('/')[1].toUpperCase() == "MP4") ||
                    (file['type'].split('/')[0] == "application" && file['type'].split('/')[1].toUpperCase() == "x-zip-compressed")
                    ){
                        return true
                }

                return false

            },

            uploadMainFile(){

                this.mainFileProgress = 0;

                let formData = new FormData()
                formData.append("file", this.file)

                var _this = this
                this.pictureStatus = "subiendo";
                
                var config = {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                    onUploadProgress: function(progressEvent) {

                        var progressPercent = Math.round((progressEvent.loaded * 100.0) / progressEvent.total);
                        _this.mainFileProgress = progressPercent

                    }
                }

                axios.post(
                    "{{ url('/upload/file') }}",
                    formData,
                    config
                ).then(res => {

                    this.mainFileStatus = "listo";
                    this.mainFileType = res.data.extension
                    this.finalMainFileName = res.data.fileRoute                    


                }).catch(err => {
                    console.log(err)
                })

            },


        },
        mounted(){

            CKEDITOR.replace( 'editor1' );

        }

    })

</script>