@extends("layouts.main")

@section("content")

    <div class="d-flex flex-column-fluid" id="dev-products">
        <div class="loader-cover-custom" v-if="loading == true">
			<div class="loader-custom"></div>
		</div>
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Header-->
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Editar servicio
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Título</label>
                                <input type="text" class="form-control" v-model="name">
                                <small v-if="errors.hasOwnProperty('title')">@{{ errors['title'][0] }}</small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">Imágen de portada (jpg,png | Dimensiones recomendadas: 1350x487px )</label>
                                <form id="form1">
                                    <input type="file" class="form-control" ref="file" @change="onMainImageChange" accept="image/*" style="overflow: hidden;">
                                </form>

                                <img id="blah" :src="imagePreview" class="full-image" style="margin-top: 10px; width: 40%">

                                <div v-if="pictureStatus == 'subiendo'" class="progress-bar progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="{'width': `${imageProgress}%`}">
                                    @{{ imageProgress }}%
                                </div>

                                <p v-if="pictureStatus == 'subiendo' && imageProgress < 100">Subiendo</p>
                                <p v-if="pictureStatus == 'subiendo' && imageProgress == 100">Espere un momento</p>
                                <p v-if="pictureStatus == 'listo' && imageProgress == 100">Imágen lista</p>

                                <small v-if="errors.hasOwnProperty('image')">@{{ errors['image'][0] }}</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea rows="3" id="editor1">{{ $service->description }}</textarea>
                                <small v-if="errors.hasOwnProperty('description')">@{{ errors['description'][0] }}</small>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12">
                        <h3 class="text-center">Archivos secundarios <button v-if="workImages.length < fileAmount" class="btn btn-success" data-toggle="modal" data-target="#secondaryImagesModal">+</button></h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">

                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Preview</th>
                                        <th>Tipo de archivo</th>
                                        <th>Progreso</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(workImage, index) in workImages">
                                        <td>@{{ index + 1 }}</td>
                                        <td>

                                            <img id="blah" :src="workImage.file" v-if="workImage.type == 'jpg' || workImage.type == 'png'" class="full-image" style="margin-top: 10px; width: 40%">

                                            <video style="width: 250px;" v-if="workImage.type == 'mp4'" controls>
                                                <source :src="workImage.file" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>

                                            <iframe
                                                v-if="workImage.type == 'zip' && workImage.file.indexOf('html') > 0" :src="workImage.file">
                                            </iframe>

                                            <div class="embed360" v-if="workImage.type == '360'" style="height: 250px;">
                                                <img :src="workImage.file" class="w-100" >
                                            </div>

                                            @{{ init360() }}
                                        
                                        </td>
                                        <td>
                                            @{{ workImage.type }}
                                        </td>
                                        <td>

                                            <div v-if="workImage.status == 'subiendo'" class="progress-bar progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="{'width': `${workImage.progress}%`}">
                                                @{{ workImage.progress }}%
                                            </div>

                                            <p v-if="workImage.status == 'subiendo' && workImage.progress < 100">Subiendo</p>
                                            <p v-if="workImage.status == 'subiendo' && workImage.progress == 100">Espere un momento</p>
                                            <p v-if="workImage.status == 'listo' && workImage.progress == 100">Contenido listo</p>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger" @click="deleteWorkImage(index)"><i class="far fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>


                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-center">
                                <button class="btn btn-success" @click="update()">Actualizar</button>
                            </p>
                        </div>
                    </div>


                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->

        @include("services.edit.modal")


    </div>

@endsection

@push("scripts")

    @include("services.edit.scripts")

@endpush