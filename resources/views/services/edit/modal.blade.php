<!-- Modal-->
<div class="modal fade" id="secondaryImagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar archivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Tipo de archivo</label>
                            <select class="form-control" v-model="secondaryFileTypeSelect">
                                <option value="file">Archivo</option>
                                <option value="360">Imagen 360</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="type">Archivo (render 3D, video, imágen (jpg, png))</label>
                            <form id="form3">
                                <input type="file" class="form-control" ref="file" @change="onSecondaryFileChange" accept="*" style="overflow: hidden;">
                            </form>

                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-success" @click="addSecondaryFile()">Añadir</button>
            </div>
        </div>
    </div>
</div>