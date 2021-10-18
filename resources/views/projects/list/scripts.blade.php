<script>
        
    const app = new Vue({
        el: '#dev-product-list',
        data(){
            return{
                products:[],
                pages:0,
                page:1,
                loading:false,
                links:[],
                currentPage:"",
                totalPages:"",

                linkClass:"page-link",
                activeLinkClass:"page-link active-link",
            }
        },
        methods:{
            
            fetch(page){
                
                let url = ""
                if(page)
                    url = page.url
                else
                    url = "{{ route('project.fetch') }}"

                axios.get(url)
                .then(res => {
                    
                    this.links = res.data.links
                    this.currentPage = res.data.current_page
                    this.totalPages = res.data.last_page

                    this.products = res.data.data
                    
                })
                

            },
            erase(id){

                swal({
                    title: "¿Estás seguro?",
                    text: "Eliminarás este proyecto!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        this.loading = true
                        axios.post("{{ route('project.delete') }}", {id: id}).then(res => {
                            this.loading = false
                            if(res.data.success == true){
                                swal({
                                    title: "Genial!",
                                    text: "Proyecto eliminado!",
                                    icon: "success"
                                });
                                this.fetch()
                            }else{

                                swal({
                                    title: "Lo sentimos!",
                                    text: res.data.msg,
                                    icon: "error"
                                });

                            }

                        })

                    }
                });

            }


        },
        mounted(){
            
            this.fetch()

        }

    })

</script>